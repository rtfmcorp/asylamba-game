<?php
# refuse a commercial route action

# int base 			id (rPlace) de la base orbitale qui refuse la route
# int route 		id de la route commerciale

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = $request->query->get('base');
$route = $request->query->get('route');

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$cr = $commercialRouteManager->getByIdAndDistantBase($route, $base);
	if ($cr !== null && $cr->getStatement() === CommercialRoute::PROPOSED) {
		$proposerBase = $orbitalBaseManager->get($cr->getROrbitalBase());
		$refusingBase = $orbitalBaseManager->get($cr->getROrbitalBaseLinked());

		//rend les crédits au proposant
		$playerManager->increaseCredit($playerManager->get($proposerBase->getRPlayer()), intval($cr->getPrice()));

		//notification
		$n = new Notification();
		$n->setRPlayer($proposerBase->getRPlayer());
		$n->setTitle('Route commerciale refusée');
		$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' a refusé la route commerciale proposée entre ');
		$n->addLnk('map/place-' . $refusingBase->getRPlace(), $refusingBase->getName())->addTxt(' et ');
		$n->addLnk('map/base-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt('.');
		$n->addSep()->addTxt('Les ' . Format::numberFormat($cr->getPrice()) . ' crédits bloqués sont à nouveau disponibles.');
		$n->addEnd();
		$notificationManager->add($n);

		//destruction de la route
		$commercialRouteManager->remove($cr);
		$session->addFlashbag('Route commerciale refusée', Flashbag::TYPE_SUCCESS);
	} else {
		throw new ErrorException('impossible de refuser une route commerciale');
	}
} else {
	throw new FormException('pas assez d\'informations pour refuser une route commerciale');
}