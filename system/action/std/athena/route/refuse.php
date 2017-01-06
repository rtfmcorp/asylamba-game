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
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession(ASM_UMODE);
		$orbitalBaseManager->load(array('rPlace' => $cr->getROrbitalBase()));
		$proposerBase = $orbitalBaseManager->get();
		$orbitalBaseManager->load(array('rPlace' => $cr->getROrbitalBaseLinked()));
		$refusingBase = $orbitalBaseManager->get(1);

		//rend les crédits au proposant
		$S_PAM1 = $playerManager->getCurrentSession();
		$playerManager->newSession(ASM_UMODE);
		$playerManager->load(array('id' => $proposerBase->getRPlayer()));
		$playerManager->increaseCredit($playerManager->get(), intval($cr->getPrice()));

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
		$orbitalBaseManager->changeSession($S_OBM1);
		$playerManager->changeSession($S_PAM1);
	} else {
		throw new ErrorException('impossible de refuser une route commerciale');
	}
} else {
	throw new FormException('pas assez d\'informations pour refuser une route commerciale');
}