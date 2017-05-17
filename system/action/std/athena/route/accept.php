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
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$database = $this->getContainer()->get('database');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$routeExperienceCoeff = $this->getContainer()->getParameter('athena.trade.experience_coeff');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base 	= $request->query->get('base');
$route 	= $request->query->get('route');


if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$cr = $commercialRouteManager->getByIdAndDistantBase($route, $base);

	if ($cr !== null && $cr->getStatement() == CommercialRoute::PROPOSED) {
		$proposerFaction = $colorManager->get($cr->playerColor1);
		$acceptorFaction = $colorManager->get($cr->playerColor2);
		
		if ($proposerFaction->colorLink[$cr->playerColor2] != Color::ENEMY && $acceptorFaction->colorLink[$cr->playerColor1] != Color::ENEMY) {
			$proposerBase = $orbitalBaseManager->get($cr->getROrbitalBase());
			$acceptorBase = $orbitalBaseManager->get($cr->getROrbitalBaseLinked());
			
			$nbrMaxCommercialRoute = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $acceptorBase->getLevelSpatioport(), 'nbRoutesMax'); 
			
			if ($commercialRouteManager->countBaseActiveAndStandbyRoutes($acceptorBase->getId()) <= $nbrMaxCommercialRoute) {
				# compute bonus if the player is from Negore
				if ($session->get('playerInfo')->get('color') == ColorResource::NEGORA) {
					$price = round($cr->getPrice() - ($cr->getPrice() * ColorResource::BONUS_NEGORA_ROUTE / 100));
				} else {
					$price = $cr->getPrice();
				}

				if ($session->get('playerInfo')->get('credit') >= $price) {
					# débit des crédits au joueur
					$acceptor = $playerManager->get($session->get('playerId'));
					$playerManager->decreaseCredit($acceptor, $price);

					# augmentation de l'expérience des deux joueurs
					$exp = round($cr->getIncome() * $routeExperienceCoeff);
					$playerManager->increaseExperience($acceptor, $exp);
					$playerManager->increaseExperience($playerManager->get($proposerBase->getRPlayer()), $exp);
					
					# activation de la route
					$cr->setStatement(CommercialRoute::ACTIVE);
					$cr->setDCreation(Utils::now());

					$n = new Notification();
					$n->setRPlayer($proposerBase->getRPlayer());
					$n->setTitle('Route commerciale acceptée');
					$n->addBeg();
					$n->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' a accepté la route commerciale proposée entre ');
					$n->addLnk('map/place-' . $acceptorBase->getRPlace(), $acceptorBase->getName())->addTxt(' et ');
					$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName());
					$n->addSep()->addTxt('Cette route vous rapporte ' . Format::numberFormat($cr->getIncome()) . ' crédits par relève.');
					$n->addBrk()->addBoxResource('xp', $exp, 'expérience gagnée');
					$n->addSep()->addLnk('action/a-switchbase/base-' . $proposerBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
					$n->addEnd();
					$notificationManager->add($n);

					$entityManager->flush();
					$entityManager->clear(CommercialRoute::class);
					if (DATA_ANALYSIS) {
						$qr = $database->prepare('INSERT INTO 
							DA_CommercialRelation(`from`, `to`, type, weight, dAction)
							VALUES(?, ?, ?, ?, ?)'
						);
						$qr->execute([$cr->playerId1, $cr->playerId2, 6, DataAnalysis::creditToStdUnit($cr->price), Utils::now()]);
					}

					$session->addFlashbag('Route commerciale acceptée, vous gagnez ' . $exp . ' points d\'expérience', Flashbag::TYPE_SUCCESS);
				} else {
					throw new ErrorException('impossible d\'accepter une route commerciale');
				}
			} else {
				throw new ErrorException('impossible d\'accepter une route commerciale');
			}
		} else {
			throw new ErrorException('Vous ne pouvez pas accepter les routes de ce joueur, vos deux factions sont en guerre');
		}
	} else {
		throw new ErrorException('impossible d\'accepter une route commerciale');
	}
} else {
	throw new FormException('pas assez d\'informations pour accepter une route commerciale');
}