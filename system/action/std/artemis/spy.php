<?php
# send a spy

# int rplace 		id of the place to spy
# int price			credit price for spying

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Artemis\Model\SpyReport;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\CommercialRoute;

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
$price 	= $request->query->has('price') ? $request->query->get('price') : $request->request->get('price');

if ($rPlace !== FALSE AND $price !== FALSE) {
	$price = intval($price);
	$price = $price > 0 ? $price : 0;
	$price = $price < 1000000 ? $price : 0;
	
	if ($session->get('playerInfo')->get('credit') >= $price && $price > 0) {
		# place
		$place = $placeManager->get($rPlace);

		if ($place->typeOfPlace == Place::TERRESTRIAL && $place->playerColor != $session->get('playerInfo')->get('color')) {
			# débit des crédits au joueur
			$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $price);

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
					$commander = $commanderManager->createVirtualCommander($place);

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
					$orbitalBase = $orbitalBaseManager->get($rPlace);

					$enemy = $playerManager->get($orbitalBase->rPlayer);
					
					$sr->resources = $orbitalBase->resourcesStorage;

					$sr->typeOfOrbitalBase = $orbitalBase->typeOfBase;
					$sr->rEnemy = $orbitalBase->rPlayer;
					$sr->enemyName = $enemy->name;
					$sr->enemyAvatar = $enemy->avatar;
					$sr->enemyLevel = $enemy->level;

					$sr->shipsInStorage = serialize($orbitalBase->shipStorage);
					$sr->antiSpyInvest = $orbitalBase->iAntiSpy;
					$sr->commercialRouteIncome = $commercialRouteManager->getBaseIncome($orbitalBase);

					$commandersArray = array();
					$commanders = $commanderManager->getBaseCommanders($rPlace, [Commander::AFFECTED, Commander::MOVING]);

					foreach ($commanders as $commander) { 
						$commandersArray[] = [
							'name' => $commander->name,
							'avatar' => $commander->avatar,
							'level' => $commander->level,
							'line' => $commander->line,
							'statement' => $commander->statement,
							'pev' => $commander->getPev(),
							'army' => $commander->getNbrShipByType()
						];
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
							$n->addLnk('map/place-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
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
							$n->addLnk('map/place-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
							$n->addBrk()->addTxt('L\'espion s\'est fait attrapé en train de fouiller dans vos affaires.');
							$n->addEnd();
							$notificationManager->add($n);
							break;
						default:
							break;
					}
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

			$session->addFlashbag('Espionnage effectué.', Flashbag::TYPE_SUCCESS);
			$response->redirect('fleet/view-spyreport/report-' . $sr->id);
		} else {
			throw new ErrorException('Impossible de lancer un espionnage');
		}
	} else {
		throw new ErrorException('Impossible de lancer un espionnage avec le montant proposé');
	}
} else {
	throw new FormException('Pas assez d\'informations pour espionner');
}