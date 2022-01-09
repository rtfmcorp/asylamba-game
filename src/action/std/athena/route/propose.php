<?php
# propose a commercial route action

# int basefrom 		id (rPlace) de la base orbitale qui propose la route
# int baseto 		id (rPlace) de la base orbitale à qui la route est proposée

use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Classes\Library\Format;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$commercialRouteManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialRouteManager::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$routeColorBonus = $this->getContainer()->getParameter('athena.trade.route.color_bonus');
$routeSectorBonus = $this->getContainer()->getParameter('athena.trade.route.sector_bonus');
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}
$baseFrom 	= $request->query->get('basefrom');
$baseTo 	= $request->query->get('baseto');

if ($baseFrom !== FALSE AND $baseTo !== FALSE AND in_array($baseFrom, $verif)) {
	$proposerBase = $orbitalBaseManager->get($baseFrom);
	$otherBase = $orbitalBaseManager->get($baseTo);

	$nbrMaxCommercialRoute = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $proposerBase->getLevelSpatioport(), 'nbRoutesMax');
	
	// Check if a route already exists between these two bases
	$alreadyARoute = $commercialRouteManager->isAlreadyARoute($proposerBase->getId(), $otherBase->getId());

	if (($commercialRouteManager->countBaseRoutes($proposerBase->getId()) < $nbrMaxCommercialRoute) && (!$alreadyARoute) && ($proposerBase->getLevelSpatioport() > 0) && ($otherBase->getLevelSpatioport() > 0)) {
		$player = $playerManager->get($otherBase->getRPlayer());

		$playerFaction = $colorManager->get($session->get('playerInfo')->get('color'));
		$otherFaction = $colorManager->get($player->rColor);
		
		if ($playerFaction->colorLink[$player->rColor] != Color::ENEMY && $otherFaction->colorLink[$session->get('playerInfo')->get('color')] != Color::ENEMY) {

			if ($proposerBase !== null && $otherBase !== null && ($proposerBase->getRPlayer() != $otherBase->getRPlayer()) && $player !== null) {
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
				if (in_array(ColorResource::COMMERCIALROUTEPRICEBONUS, $playerFaction->bonus)) {
					$priceWithBonus = round($price - ($price * ColorResource::BONUS_NEGORA_ROUTE / 100));
				} else {
					$priceWithBonus = $price;
				}

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
					$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $priceWithBonus);

					$n = new Notification();
					$n->setRPlayer($otherBase->getRPlayer());
					$n->setTitle('Proposition de route commerciale');
					$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
					$n->addTxt(' vous propose une route commerciale liant ');
					$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt(' et ');
					$n->addLnk('map/place-' . $otherBase->getRPlace(), $otherBase->getName())->addTxt('.');
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
	} else {
		throw new ErrorException('impossible de proposer une route commerciale (3)');
	}
} else {
	throw new FormException('pas assez d\'informations pour proposer une route commerciale');
}