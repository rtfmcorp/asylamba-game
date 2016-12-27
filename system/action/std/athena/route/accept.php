<?php
# accept a commercial route action

# int base 			id (rPlace) de la base orbitale qui accepte la route
# int route 		id de la route commerciale

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$database = $this->getContainer()->get('database');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base 	= $request->query->get('base');
$route 	= $request->query->get('route');


if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$S_CRM1 = $commercialRouteManager->getCurrentSession();
	$commercialRouteManager->newSession();
	$commercialRouteManager->load(array('id'=>$route, 'rOrbitalBaseLinked' => $base, 'statement' => CommercialRoute::PROPOSED));

	if ($commercialRouteManager->get() && $commercialRouteManager->size() == 1) {
		$cr = $commercialRouteManager->get();

		$S_CLM1 = $colorManager->getCurrentSession();
		$colorManager->newSession();
		$colorManager->load(array('id' => array($cr->playerColor1, $cr->playerColor2)));

		if ($colorManager->size() == 2) {
			if ($colorManager->get(0)->id == $cr->playerColor1) {
				$color1 = $colorManager->get(0);
				$color2 = $colorManager->get(1);
			} else {
				$color1 = $colorManager->get(1);
				$color2 = $colorManager->get(0);
			}
		} else {
			$color1 = $colorManager->get();
			$color2 = $colorManager->get();
		}

		if ($color1->colorLink[$cr->playerColor2] != Color::ENEMY && $color2->colorLink[$cr->playerColor1] != Color::ENEMY) {
			$S_OBM1 = $orbitalBaseManager->getCurrentSession();
			$orbitalBaseManager->newSession();
			$orbitalBaseManager->load(array('rPlace' => $cr->getROrbitalBase()));
			$proposerBase = $orbitalBaseManager->get();

			$orbitalBaseManager->load(array('rPlace' => $cr->getROrbitalBaseLinked()));
			$acceptorBase = $orbitalBaseManager->get(1);

			$commercialRouteManager->load(array('rOrbitalBase' => $acceptorBase->getId()));
			$commercialRouteManager->load(array('rOrbitalBaseLinked' => $acceptorBase->getId(), 'statement' => CommercialRoute::ACTIVE));
			$commercialRouteManager->load(array('rOrbitalBaseLinked' => $acceptorBase->getId(), 'statement' => CommercialRoute::STANDBY));

			$nbrCommercialRoute = $commercialRouteManager->size() - 1;
			$nbrMaxCommercialRoute = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $acceptorBase->getLevelSpatioport(), 'nbRoutesMax'); 
			
			if ($nbrCommercialRoute < $nbrMaxCommercialRoute) {
				# compute bonus if the player is from Negore
				if ($session->get('playerInfo')->get('color') == ColorResource::NEGORA) {
					$price = round($cr->getPrice() - ($cr->getPrice() * ColorResource::BONUS_NEGORA_ROUTE / 100));
				} else {
					$price = $cr->getPrice();
				}

				if ($session->get('playerInfo')->get('credit') >= $price) {
					# débit des crédits au joueur
					$S_PAM1 = $playerManager->getCurrentSession();
					$playerManager->newSession(ASM_UMODE);
					$playerManager->load(array('id' => $session->get('playerId')));
					$playerManager->get()->decreaseCredit($price);

					# augmentation de l'expérience des deux joueurs
					$exp = round($cr->getIncome() * CRM_COEFEXPERIENCE);
					$playerManager->load(array('id' => $proposerBase->getRPlayer()));
					$playerManager->get()->increaseExperience($exp);
					$playerManager->get(1)->increaseExperience($exp);
					
					$playerManager->changeSession($S_PAM1);
					
					# activation de la route
					$cr->setStatement(CommercialRoute::ACTIVE);
					$cr->setDCreation(Utils::now());

					$n = new Notification();
					$n->setRPlayer($proposerBase->getRPlayer());
					$n->setTitle('Route commerciale acceptée');
					$n->addBeg();
					$n->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' a accepté la route commerciale proposée entre ');
					$n->addLnk('map/place-' . $acceptorBase->getRPlace(), $acceptorBase->getName())->addTxt(' et ');
					$n->addLnk('map/base-' . $proposerBase->getRPlace(), $proposerBase->getName());
					$n->addSep()->addTxt('Cette route vous rapporte ' . Format::numberFormat($cr->getIncome()) . ' crédits par relève.');
					$n->addBrk()->addBoxResource('xp', $exp, 'expérience gagnée');
					$n->addSep()->addLnk('action/a-switchbase/base-' . $proposerBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
					$n->addEnd();
					$notificationManager->add($n);

					if (DATA_ANALYSIS) {
						$qr = $database->prepare('INSERT INTO 
							DA_CommercialRelation(`from`, `to`, type, weight, dAction)
							VALUES(?, ?, ?, ?, ?)'
						);
						$qr->execute([$cr->playerId1, $cr->playerId2, 6, DataAnalysis::creditToStdUnit($cr->price), Utils::now()]);
					}

					$response->flashbag->add('Route commerciale acceptée, vous gagnez ' . $exp . ' points d\'expérience', Response::FLASHBAG_SUCCESS);
				} else {
					throw new ErrorException('impossible d\'accepter une route commerciale');
				}
			} else {
				throw new ErrorException('impossible d\'accepter une route commerciale');
			}
			$orbitalBaseManager->changeSession($S_OBM1);
		} else {
			throw new ErrorException('Vous ne pouvez pas accepter les routes de ce joueur, vos deux factions sont en guerre');
		}
		$colorManager->changeSession($S_CLM1);
	} else {
		throw new ErrorException('impossible d\'accepter une route commerciale');
	}
	$commercialRouteManager->changeSession($S_CRM1);
} else {
	throw new FormException('pas assez d\'informations pour accepter une route commerciale');
}