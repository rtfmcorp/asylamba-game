<?php

namespace App\Modules\Athena\Infrastructure\Controller\Trade;

use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteRoute extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommercialRouteManager $commercialRouteManager,
		OrbitalBaseManager $orbitalBaseManager,
		NotificationManager $notificationManager,
		int $baseId,
		int $id,
	): Response {
		$session = $request->getSession();

		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
		}

		if (in_array($baseId, $verif)) {
			$cr = $commercialRouteManager->get($id);
			if ($cr !== null && in_array($cr->statement, [CommercialRoute::ACTIVE, CommercialRoute::STANDBY])) {
				if ($cr->playerId1 === $currentPlayer->getid() || $cr->playerId2 === $currentPlayer->getId()) {
					if ($cr->getROrbitalBase() == $baseId OR $cr->getROrbitalBaseLinked() == $baseId) {
						$proposerBase = $orbitalBaseManager->get($cr->getROrbitalBase());
						$linkedBase = $orbitalBaseManager->get($cr->getROrbitalBaseLinked());
						if ($cr->getROrbitalBase() == $baseId) {
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
						$n->addBeg()->addLnk('embassy/player-' . $currentPlayer->getId(), $currentPlayer->getName())->addTxt(' annule les accords commerciaux entre ');
						$n->addLnk('map/place-' . $myBaseId, $myBaseName)->addTxt(' et ');
						$n->addLnk('map/place-' . $otherBaseId, $otherBaseName)->addTxt('.');
						$n->addSep()->addTxt('La route commerciale qui liait les deux bases orbitales est détruite, elle ne vous rapporte donc plus rien !');
						$n->addEnd();
						$notificationManager->add($n);

						//destruction de la route
						$commercialRouteManager->remove($cr);

						$this->addFlash('success', 'Route commerciale détruite');

						return $this->redirect($request->headers->get('referer'));
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
	}
}
