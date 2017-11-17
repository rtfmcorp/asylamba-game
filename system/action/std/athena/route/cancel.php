<?php
# cancel a commercial route action

# int base 			id (rPlace) de la base orbitale qui a proposé la route mais qui l'annule
# int route 		id de la route commerciale

use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$routeCancelRefund = $this->getContainer()->getParameter('athena.trade.route.cancellation_refund');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = $request->query->get('base');
$route = $request->query->get('route');

if ($base !== false and $route !== false and in_array($base, $verif)) {
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
