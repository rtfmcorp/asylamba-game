<?php
# propose a commercial route action

# int basefrom 		id (rPlace) de la base orbitale qui propose la route
# int baseto 		id (rPlace) de la base orbitale à qui la route est proposée

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$routeColorBonus = $this->getContainer()->getParameter('athena.trade.route.color_bonus');
$routeSectorBonus = $this->getContainer()->getParameter('athena.trade.route.sector_bonus');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}
$baseFrom 	= $request->query->get('basefrom');
$baseTo 	= $request->query->get('baseto');

if ($baseFrom !== FALSE AND $baseTo !== FALSE AND in_array($baseFrom, $verif)) {
	$S_OBM1 = $orbitalBaseManager->getCurrentSession();
	$orbitalBaseManager->newSession();
	$orbitalBaseManager->load(array('rPlace' => $baseFrom));

	$proposerBase = $orbitalBaseManager->get();

#	if ($proposerBase->getLevelSpatioport() > 0) {}

	$orbitalBaseManager->load(array('rPlace' => $baseTo));

	$otherBase = $orbitalBaseManager->get(1);

	# check s'il y a une place de route libre
	$S_CRM1 = $commercialRouteManager->getCurrentSession();
	$commercialRouteManager->newSession();
	$commercialRouteManager->load(array('rOrbitalBase' => $proposerBase->getId())); # routes avec n'importe quel statement
	$commercialRouteManager->load(array('rOrbitalBaseLinked' => $proposerBase->getId(), 'statement' => array(CommercialRoute::ACTIVE, CommercialRoute::STANDBY)));

	$nbrMaxCommercialRoute = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $proposerBase->getLevelSpatioport(), 'nbRoutesMax');
	
	# check si on n'a pas déjà une route avec ce joueur
	$alreadyARoute = FALSE;
	for ($i = 0; $i < $commercialRouteManager->size(); $i++) { 
		if ($commercialRouteManager->get($i)->getROrbitalBaseLinked() == $proposerBase->getRPlace()) {
			if ($commercialRouteManager->get($i)->getROrbitalBase() == $otherBase->getRPlace()) {
				$alreadyARoute = TRUE;
			}
		}
		if ($commercialRouteManager->get($i)->getROrbitalBase() == $proposerBase->getRPlace()) {
			if ($commercialRouteManager->get($i)->getROrbitalBaseLinked() == $otherBase->getRPlace()) {
				$alreadyARoute = TRUE;
			}
		}
	}

	if (($commercialRouteManager->size() < $nbrMaxCommercialRoute) && (!$alreadyARoute) && ($proposerBase->getLevelSpatioport() > 0) && ($otherBase->getLevelSpatioport() > 0)) {
		$S_PAM1 = $playerManager->getCurrentSession();
		$playerManager->newSession();
		$playerManager->load(array('id' => $otherBase->getRPlayer()));

		$player = $playerManager->get();

		$S_CLM1 = $colorManager->getCurrentSession();
		$colorManager->newSession();
		$colorManager->load(array('id' => array($session->get('playerInfo')->get('color'), $player->rColor)));

		if ($colorManager->size() == 2) {
			if ($colorManager->get(0)->id == $session->get('playerInfo')->get('color')) {
				$myColor = $colorManager->get(0);
				$otherColor = $colorManager->get(1);
			} else {
				$myColor = $colorManager->get(1);
				$otherColor = $colorManager->get(0);
			}
		} else {
			$myColor = $colorManager->get();
			$otherColor = $colorManager->get();
		}
		if ($myColor->colorLink[$player->rColor] != Color::ENEMY && $otherColor->colorLink[$session->get('playerInfo')->get('color')] != Color::ENEMY) {

			if ($orbitalBaseManager->size() == 2 && ($proposerBase->getRPlayer() != $otherBase->getRPlayer()) && ($playerManager->size() == 1)) {
				$distance = Game::getDistance($proposerBase->getXSystem(), $otherBase->getXSystem(), $proposerBase->getYSystem(), $otherBase->getYSystem());
				$bonusA = ($proposerBase->getSector() != $otherBase->getSector()) ? $routeSectorBonus : 1;
				$bonusB = ($session->get('playerInfo')->get('color')) != $player->getRColor() ? $routeColorBonus : 1;
				$price = Game::getRCPrice($distance);
				$income = Game::getRCIncome($distance, $bonusA, $bonusB);
				
				if ($distance == 1) {
					$imageLink = '1-' . rand(1, 3);
				} elseif ($distance < 26) {
					$imageLink = '2-' . rand(1, 3);
				} elseif ($distance < 126) {
					$imageLink = '3-' . rand(1, 3);
				} else {
					$imageLink = '4-' . rand(1, 3);
				}

				# compute bonus
				$_CLM23 = $colorManager->getCurrentSession();
				$colorManager->newSession();
				$colorManager->load(['id' => $session->get('playerInfo')->get('color')]);
				if (in_array(ColorResource::COMMERCIALROUTEPRICEBONUS, $colorManager->get()->bonus)) {
					$priceWithBonus = round($price - ($price * ColorResource::BONUS_NEGORA_ROUTE / 100));
				} else {
					$priceWithBonus = $price;
				}

				$colorManager->changeSession($_CLM23);

				if ($session->get('playerInfo')->get('credit') >= $priceWithBonus) {
					# création de la route
					$cr = new CommercialRoute();
					$cr->setROrbitalBase($proposerBase->getId());
					$cr->setROrbitalBaseLinked($otherBase->getId());
					$cr->setImageLink($imageLink);
					$cr->setDistance($distance);
					$cr->setPrice($price);
					$cr->setIncome($income);
					$cr->setDProposition(Utils::now());
					$cr->setDCreation(NULL);
					$cr->setStatement(0);
					$commercialRouteManager->add($cr);
					
					# débit des crédits au joueur
					$S_PAM2 = $playerManager->getCurrentSession();
					$playerManager->newSession(ASM_UMODE);
					$playerManager->load(array('id' => $session->get('playerId')));
					$playerManager->decreaseCredit($playerManager->get(), $priceWithBonus);
					$playerManager->changeSession($S_PAM2);

					$n = new Notification();
					$n->setRPlayer($otherBase->getRPlayer());
					$n->setTitle('Proposition de route commerciale');
					$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
					$n->addTxt(' vous propose une route commerciale liant ');
					$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt(' et ');
					$n->addLnk('map/base-' . $otherBase->getRPlace(), $otherBase->getName())->addTxt('.');
					$n->addSep()->addTxt('Les frais de l\'opération vous coûteraient ' . Format::numberFormat($priceWithBonus) . ' crédits; Les gains estimés pour cette route sont de ' . Format::numberFormat($income) . ' crédits par relève.');
					$n->addSep()->addLnk('action/a-switchbase/base-' . $otherBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
					$n->addEnd();
					$notificationManager->add($n);

					$session->addFlashbag('Route commerciale proposée', Flashbag::TYPE_SUCCESS);
				} else {
					throw new ErrorException('impossible de proposer une route commerciale - vous n\'avez pas assez de crédits');
				}
			} else {
				throw new ErrorException('impossible de proposer une route commerciale (2)');
			}
		} else {
			throw new ErrorException('impossible de proposer une route commerciale à ce joueur, vos factions sont en guerre.');
		}
		$colorManager->changeSession($S_CLM1);
		$playerManager->changeSession($S_PAM1);
	} else {
		throw new ErrorException('impossible de proposer une route commerciale (3)');
	}
	$commercialRouteManager->changeSession($S_CRM1);
	$orbitalBaseManager->changeSession($S_OBM1);
} else {
	throw new FormException('pas assez d\'informations pour proposer une route commerciale');
}