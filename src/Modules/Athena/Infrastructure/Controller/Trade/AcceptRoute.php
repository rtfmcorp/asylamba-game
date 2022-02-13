<?php

namespace App\Modules\Athena\Infrastructure\Controller\Trade;

use App\Classes\Entity\EntityManager;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Classes\Library\Format;
use App\Classes\Library\Utils;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptRoute extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommercialRouteManager $commercialRouteManager,
		ColorManager $colorManager,
		OrbitalBaseManager $orbitalBaseManager,
		OrbitalBaseHelper $orbitalBaseHelper,
		PlayerManager $playerManager,
		NotificationManager $notificationManager,
		EntityManager $entityManager,
		int $baseId,
		int $id,
	): Response {
		$session = $request->getSession();
		for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
		}
		$routeExperienceCoeff = $this->getParameter('athena.trade.experience_coeff');

		if (in_array($baseId, $verif)) {
			$cr = $commercialRouteManager->getByIdAndDistantBase($id, $baseId);

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
							$playerManager->decreaseCredit($currentPlayer, $price);

							# augmentation de l'expérience des deux joueurs
							$exp = round($cr->getIncome() * $routeExperienceCoeff);
							$playerManager->increaseExperience($currentPlayer, $exp);
							$playerManager->increaseExperience($playerManager->get($proposerBase->getRPlayer()), $exp);

							# activation de la route
							$cr->setStatement(CommercialRoute::ACTIVE);
							$cr->setDCreation(Utils::now());

							$n = new Notification();
							$n->setRPlayer($proposerBase->getRPlayer());
							$n->setTitle('Route commerciale acceptée');
							$n->addBeg();
							$n->addLnk('embassy/player-' . $currentPlayer->getId(), $currentPlayer->getName())->addTxt(' a accepté la route commerciale proposée entre ');
							$n->addLnk('map/place-' . $acceptorBase->getRPlace(), $acceptorBase->getName())->addTxt(' et ');
							$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName());
							$n->addSep()->addTxt('Cette route vous rapporte ' . Format::numberFormat($cr->getIncome()) . ' crédits par relève.');
							$n->addBrk()->addBoxResource('xp', $exp, 'expérience gagnée', $this->getParameter('media'));
							$n->addSep()->addLnk('action/a-switchbase/base-' . $proposerBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
							$n->addEnd();
							$notificationManager->add($n);

							$entityManager->flush();
//							if (true === $this->getContainer()->getParameter('data_analysis')) {
//								$qr = $database->prepare('INSERT INTO
//							DA_CommercialRelation(`from`, `to`, type, weight, dAction)
//							VALUES(?, ?, ?, ?, ?)'
//								);
//								$qr->execute([$cr->playerId1, $cr->playerId2, 6, DataAnalysis::creditToStdUnit($cr->price), Utils::now()]);
//							}

							$this->addFlash('success', 'Route commerciale acceptée, vous gagnez ' . $exp . ' points d\'expérience');

							return $this->redirect($request->headers->get('referer'));
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
	}
}
