<?php
# cancel a commercial route action

# int base 			id (rPlace) de la base orbitale qui a proposé la route mais qui l'annule
# int route 		id de la route commerciale

use App\Modules\Hermes\Model\Notification;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Modules\Athena\Model\CommercialRoute;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$commercialRouteManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialRouteManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$routeCancelRefund = $this->getContainer()->getParameter('athena.trade.route.cancellation_refund');
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = $request->query->get('base');
$route = $request->query->get('route');

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$cr = $commercialRouteManager->getByIdAndBase($route, $base);
	if ($cr !== null && $cr->getStatement() == CommercialRoute::PROPOSED) {
		$proposerBase = $orbitalBaseManager->get($cr->getROrbitalBase());
		$linkedBase = $orbitalBaseManager->get($cr->getROrbitalBaseLinked());

		//rend 80% des crédits investis
		$playerManager->increaseCredit($playerManager->get($session->get('playerId')), round($cr->getPrice() * $routeCancelRefund));

		//notification
		$n = new Notification();
		$n->setRPlayer($linkedBase->getRPlayer());
		$n->setTitle('Route commerciale annulée');

		$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'))->addTxt(' a finalement retiré la proposition de route commerciale qu\'il avait faite entre ');
		$n->addLnk('map/place-' . $linkedBase->getRPlace(), $linkedBase->getName())->addTxt(' et ');
		$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName());
		$n->addEnd();
		$notificationManager->add($n);

		//destruction de la route
		$commercialRouteManager->remove($cr);
		$session->addFlashbag('Route commerciale annulée. Vous récupérez les ' . $routeCancelRefund * 100 . '% du montant investi.', Flashbag::TYPE_SUCCESS);
	} else {
		throw new ErrorException('impossible d\'annuler une route commerciale');
	}
} else {
	throw new FormException('pas assez d\'informations pour annuler une route commerciale');
}
$entityManager->flush();
