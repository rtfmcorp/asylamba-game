<?php
# delete a commercial route action

# int base 			id (rPlace) de la base orbitale qui veut supprimer la route
# int route 		id de la route commerciale

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$routeExperienceCoeff = $this->getContainer()->getParameter('athena.trade.experience_coeff');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = $request->query->get('base');
$route = $request->query->get('route');

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$cr = $commercialRouteManager->get($route);
	if ($cr !== null && in_array($cr->statement, [CommercialRoute::ACTIVE, CommercialRoute::STANDBY])) {
		if ($cr->playerId1 == $session->get('playerId') || $cr->playerId2 == $session->get('playerId')) {
			if ($cr->getROrbitalBase() == $base OR $cr->getROrbitalBaseLinked() == $base) {
				$proposerBase = $orbitalBaseManager->get($cr->getROrbitalBase());
				$linkedBase = $orbitalBaseManager->get($cr->getROrbitalBaseLinked());
				if ($cr->getROrbitalBase() == $base) {
					$notifReceiver = $linkedBase->getRPlayer();
					$myBaseName = $proposerBase->getName();
					$otherBaseName = $linkedBase->getName();
					$myBaseId = $proposerBase->getRPlace();
					$otherBaseId = $linkedBase->getRPlace();
				} else { //if ($cr->getROrbitalBaseLinked == $base) {
					$notifReceiver = $proposerBase->getRPlayer();
					$myBaseName = $linkedBase->getName();
					$otherBaseName = $proposerBase->getName();
					$myBaseId = $linkedBase->getRPlace();
					$otherBaseId = $proposerBase->getRPlace();
				}

				# perte du prestige pour les joueurs Négoriens
				# @TODO check if this code is used somewhere or not
//				$S_PAM1 = $playerManager->getCurrentSession();
//				$playerManager->newSession();
//				$playerManager->load(array('id' => array($cr->playerId1, $cr->playerId2)));
//				$exp = round($cr->getIncome() * $routeExperienceCoeff);
//				
//				$playerManager->changeSession($S_PAM1);
				//notification
				$n = new Notification();
				$n->setRPlayer($notifReceiver);
				$n->setTitle('Route commerciale détruite');
				$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' annule les accords commerciaux entre ');
				$n->addLnk('map/place-' . $myBaseId, $myBaseName)->addTxt(' et ');
				$n->addLnk('map/place-' . $otherBaseId, $otherBaseName)->addTxt('.');
				$n->addSep()->addTxt('La route commerciale qui liait les deux bases orbitales est détruite, elle ne vous rapporte donc plus rien !');
				$n->addEnd();
				$notificationManager->add($n);

				//destruction de la route
				$commercialRouteManager->remove($cr);

				$session->addFlashbag('Route commerciale détruite', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('impossible de supprimer une route commerciale');
			}
		} else {
				throw new ErrorException('cette route ne vous appartient pas');
			}
	} else {
		throw new ErrorException('impossible de supprimer une route commerciale');
	}
} else {
	throw new FormException('pas assez d\'informations pour supprimer une route commerciale');
}