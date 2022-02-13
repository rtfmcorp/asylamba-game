<?php

namespace App\Modules\Athena\Infrastructure\Controller\Trade;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Manager\PlayerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CancelRoute extends AbstractController
{
	public function __invoke(
		Request $request,
		CommercialRouteManager $commercialRouteManager,
		PlayerManager $playerManager,
		NotificationManager $notificationManager,
		OrbitalBaseManager $orbitalBaseManager,
		EntityManager $entityManager,
		int $baseId,
		int $id,
	): Response {
		$session = $request->getSession();
		for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
		}

		if (in_array($baseId, $verif)) {
			$cr = $commercialRouteManager->getByIdAndBase($id, $baseId);
			if ($cr !== null && $cr->getStatement() == CommercialRoute::PROPOSED) {
				$routeCancelRefund = $this->getParameter('athena.trade.route.cancellation_refund');
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
				$this->addFlash('success', 'Route commerciale annulée. Vous récupérez les ' . $routeCancelRefund * 100 . '% du montant investi.');

				$entityManager->flush();

				return $this->redirect($request->headers->get('referer'));
			} else {
				throw new ConflictHttpException('impossible d\'annuler une route commerciale');
			}
		} else {
			throw new BadRequestHttpException('pas assez d\'informations pour annuler une route commerciale');
		}
	}
}
