<?php
# send a spy

# int rplace 		id of the place to spy
# int price			credit price for spying

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Artemis\Model\SpyReport;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

$rPlace = $request->query->get('rplace');
$price 	= $request->request->get('price');

if ($rPlace !== FALSE AND $price !== FALSE) {
	$price = intval($price);
	$price = $price > 0 ? $price : 0;
	$price = $price < 1000000 ? $price : 0;
	
	if ($session->get('playerInfo')->get('credit') >= $price && $price > 0) {
		# place
		$S_PLM1 = $placeManager->getCurrentSession();
		$placeManager->newSession();
		$placeManager->load(array('id' => $rPlace));
		$place = $placeManager->get();

		if ($place->typeOfPlace == Place::TERRESTRIAL && $place->playerColor != $session->get('playerInfo')->get('color')) {
			# débit des crédits au joueur
			$S_PAM1 = $playerManager->getCurrentSession();
			$playerManager->newSession();
			$playerManager->load(array('id' => $session->get('playerId')));
			$playerManager->decreaseCredit($playerManager->get(), $price);
			$playerManager->changeSession($S_PAM1);

			# espionnage
			$sr = new SpyReport();
			$sr->rPlayer = $session->get('playerId');
			$sr->rPlace = $rPlace;
			$sr->price = $price;
			$sr->placeColor = $place->playerColor;
			$sr->typeOfBase = $place->typeOfBase;
			$sr->placeName = $place->baseName;
			$sr->points = $place->points;
			$sr->shipsInStorage = serialize([]);
			$sr->antiSpyInvest = 0;
			$sr->commercialRouteIncome = 0;

			$sr->dSpying = Utils::now();

			switch ($place->typeOfBase) {
				case Place::TYP_EMPTY:
					$sr->resources = $place->resources;

					$sr->typeOfOrbitalBase = OrbitalBase::TYP_NEUTRAL;
					$sr->rEnemy = 0;
					$sr->enemyName = 'Rebelle';
					$sr->enemyAvatar = '';
					$sr->enemyLevel = 1;

					# generate a commander for the place
					$commander = $placeManager->createVirtualCommander($place);

					$commandersArray = array();
					$commandersArray[0]['name'] = $commander->name;
					$commandersArray[0]['avatar'] = $commander->avatar;
					$commandersArray[0]['level'] = $commander->level;
					$commandersArray[0]['line'] = $commander->line;
					$commandersArray[0]['statement'] = $commander->statement;
					$commandersArray[0]['pev'] = $commander->getPev();
					$commandersArray[0]['army'] = $commander->getNbrShipByType();
					
					$sr->commanders = serialize($commandersArray);

					$antiSpy = $place->maxDanger * 40;
					$sr->success = Game::getSpySuccess($antiSpy, $price);
					$sr->type = SpyReport::TYP_NOT_CAUGHT;

					break;
				case Place::TYP_ORBITALBASE:
					# orbitalBase
					$S_OBM1 = $orbitalBaseManager->getCurrentSession();
					$orbitalBaseManager->newSession();
					$orbitalBaseManager->load(array('rPlace' => $rPlace));
					$orbitalBase = $orbitalBaseManager->get();

					# enemy
					$playerManager->newSession();
					$playerManager->load(array('id' => $orbitalBase->rPlayer));
					$enemy = $playerManager->get();

					# rc
					$S_CRM1 = $commercialRouteManager->getCurrentSession();
					$commercialRouteManager->changeSession($orbitalBase->routeManager);
					$RCIncome = 0;
					for ($i = 0; $i < $commercialRouteManager->size(); $i++) {
						if ($commercialRouteManager->get($i)->getStatement() == CommercialRoute::ACTIVE) {
							$RCIncome += $commercialRouteManager->get($i)->getIncome();
						} 
					}
					
					$sr->resources = $orbitalBase->resourcesStorage;

					$sr->typeOfOrbitalBase = $orbitalBase->typeOfBase;
					$sr->rEnemy = $orbitalBase->rPlayer;
					$sr->enemyName = $enemy->name;
					$sr->enemyAvatar = $enemy->avatar;
					$sr->enemyLevel = $enemy->level;

					$sr->shipsInStorage = serialize($orbitalBase->shipStorage);
					$sr->antiSpyInvest = $orbitalBase->iAntiSpy;
					$sr->commercialRouteIncome = $RCIncome;

					$commandersArray = array();
					$S_COM1 = $commanderManager->getCurrentSession();
					$commanderManager->newSession();
					$commanderManager->load(array('rBase' => $rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));

					for ($i = 0; $i < $commanderManager->size(); $i++) { 
						$commandersArray[$i]['name'] = $commanderManager->get($i)->name;
						$commandersArray[$i]['avatar'] = $commanderManager->get($i)->avatar;
						$commandersArray[$i]['level'] = $commanderManager->get($i)->level;
						$commandersArray[$i]['line'] = $commanderManager->get($i)->line;
						$commandersArray[$i]['statement'] = $commanderManager->get($i)->statement;
						$commandersArray[$i]['pev'] = $commanderManager->get($i)->getPev();
						$commandersArray[$i]['army'] = $commanderManager->get($i)->getNbrShipByType();
					}
					$sr->commanders = serialize($commandersArray);
					
					$antiSpy = $orbitalBase->antiSpyAverage; // entre 100 et 4000
					$sr->success = Game::getSpySuccess($antiSpy, $price);
					$sr->type = Game::getTypeOfSpy($sr->success, $antiSpy);

					switch ($sr->type) {
						case SpyReport::TYP_ANONYMOUSLY_CAUGHT:
							$n = new Notification();
							$n->setRPlayer($orbitalBase->rPlayer);
							$n->setTitle('Espionnage détecté');
							$n->addBeg();
							$n->addTxt('Un joueur a espionné votre base ');
							$n->addLnk('map/base-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
							$n->addBrk()->addTxt('Malheureusement, nous n\'avons pas pu connaître l\'identité de l\'espion.');
							$n->addEnd();
							$notificationManager->add($n);
							break;
						case SpyReport::TYP_CAUGHT:
							$n = new Notification();
							$n->setRPlayer($orbitalBase->rPlayer);
							$n->setTitle('Espionnage intercepté');
							$n->addBeg();
							$n->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' a espionné votre base ');
							$n->addLnk('map/base-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
							$n->addBrk()->addTxt('L\'espion s\'est fait attrapé en train de fouiller dans vos affaires.');
							$n->addEnd();
							$notificationManager->add($n);
							break;
						default:
							break;
					}

					$commercialRouteManager->changeSession($S_CRM1);
					$commanderManager->changeSession($S_COM1);
					$orbitalBaseManager->changeSession($S_OBM1);

					break;
				default:
					throw new ErrorException('espionnage pour vaisseau-mère pas encore implémenté');
			}

			$spyReportManager->add($sr);

			# tutorial
			if ($session->get('playerInfo')->get('stepDone') == FALSE &&
				$session->get('playerInfo')->get('stepTutorial') === TutorialResource::SPY_PLANET) {
				$tutorialHelper->setStepDone();
			}

			$response->flashbag->add('Espionnage effectué.', Response::FLASHBAG_SUCCESS);
			$response->redirect('fleet/view-spyreport/report-' . $sr->id);
			
			$playerManager->changeSession($S_PAM1);
		} else {
			throw new ErrorException('Impossible de lancer un espionnage');
		}

		$placeManager->changeSession($S_PLM1);
	} else {
		throw new ErrorException('Impossible de lancer un espionnage avec le montant proposé');
	}
} else {
	throw new FormException('Pas assez d\'informations pour espionner');
}