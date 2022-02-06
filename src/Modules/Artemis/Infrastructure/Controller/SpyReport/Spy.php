<?php

namespace App\Modules\Artemis\Infrastructure\Controller\SpyReport;

use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Artemis\Manager\SpyReportManager;
use App\Modules\Artemis\Model\SpyReport;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Model\Place;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Helper\TutorialHelper;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Resource\TutorialResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Spy extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommanderManager $commanderManager,
		CommercialRouteManager $commercialRouteManager,
		PlaceManager $placeManager,
		PlayerManager $playerManager,
		NotificationManager $notificationManager,
		OrbitalBaseManager $orbitalBaseManager,
		SpyReportManager $spyReportManager,
		TutorialHelper $tutorialHelper,
	): Response {
		$rPlace = $request->query->get('baseId');
		$price 	= $request->query->has('price') ? $request->query->get('price') : $request->request->get('price');

		if ($rPlace !== FALSE AND $price !== FALSE) {
			$price = intval($price);
			$price = $price > 0 ? $price : 0;
			$price = $price < 1000000 ? $price : 0;

			if ($currentPlayer->getCredit() >= $price && $price > 0) {
				# place
				$place = $placeManager->get($rPlace);

				if ($place->typeOfPlace == Place::TERRESTRIAL && $place->playerColor != $currentPlayer->getRColor()) {
					# débit des crédits au joueur
					$playerManager->decreaseCredit($currentPlayer, $price);

					# espionnage
					$sr = new SpyReport();
					$sr->rPlayer = $currentPlayer->getId();
					$sr->rPlace = $rPlace;
					$sr->price = $price;
					$sr->placeColor = $place->playerColor;
					$sr->typeOfBase = $place->typeOfBase;
					$sr->placeName = $place->baseName;
					$sr->points = $place->points;
					$sr->shipsInStorage = serialize([]);
					$sr->antiSpyInvest = 0;
					$sr->commercialRouteIncome = 0;

					$sr->dSpying = Utils::now();

					switch ($place->typeOfBase) {
						case Place::TYP_EMPTY:
							$sr->resources = $place->resources;

							$sr->typeOfOrbitalBase = OrbitalBase::TYP_NEUTRAL;
							$sr->rEnemy = 0;
							$sr->enemyName = 'Rebelle';
							$sr->enemyAvatar = '';
							$sr->enemyLevel = 1;

							# generate a commander for the place
							$commander = $commanderManager->createVirtualCommander($place);

							$commandersArray = array();
							$commandersArray[0]['name'] = $commander->name;
							$commandersArray[0]['avatar'] = $commander->avatar;
							$commandersArray[0]['level'] = $commander->level;
							$commandersArray[0]['line'] = $commander->line;
							$commandersArray[0]['statement'] = $commander->statement;
							$commandersArray[0]['pev'] = $commander->getPev();
							$commandersArray[0]['army'] = $commander->getNbrShipByType();

							$sr->commanders = serialize($commandersArray);

							$antiSpy = $place->maxDanger * 40;
							$sr->success = Game::getSpySuccess($antiSpy, $price);
							$sr->type = SpyReport::TYP_NOT_CAUGHT;

							break;
						case Place::TYP_ORBITALBASE:
							# orbitalBase
							$orbitalBase = $orbitalBaseManager->get($rPlace);

							$enemy = $playerManager->get($orbitalBase->rPlayer);

							$sr->resources = $orbitalBase->resourcesStorage;

							$sr->typeOfOrbitalBase = $orbitalBase->typeOfBase;
							$sr->rEnemy = $orbitalBase->rPlayer;
							$sr->enemyName = $enemy->name;
							$sr->enemyAvatar = $enemy->avatar;
							$sr->enemyLevel = $enemy->level;

							$sr->shipsInStorage = serialize($orbitalBase->shipStorage);
							$sr->antiSpyInvest = $orbitalBase->iAntiSpy;
							$sr->commercialRouteIncome = $commercialRouteManager->getBaseIncome($orbitalBase);

							$commandersArray = array();
							$commanders = $commanderManager->getBaseCommanders($rPlace, [Commander::AFFECTED, Commander::MOVING]);

							foreach ($commanders as $commander) {
								$commandersArray[] = [
									'name' => $commander->name,
									'avatar' => $commander->avatar,
									'level' => $commander->level,
									'line' => $commander->line,
									'statement' => $commander->statement,
									'pev' => $commander->getPev(),
									'army' => $commander->getNbrShipByType()
								];
							}
							$sr->commanders = serialize($commandersArray);

							$antiSpy = $orbitalBase->antiSpyAverage; // entre 100 et 4000
							$sr->success = Game::getSpySuccess($antiSpy, $price);
							$sr->type = Game::getTypeOfSpy($sr->success, $antiSpy);

							switch ($sr->type) {
								case SpyReport::TYP_ANONYMOUSLY_CAUGHT:
									$n = new Notification();
									$n->setRPlayer($orbitalBase->rPlayer);
									$n->setTitle('Espionnage détecté');
									$n->addBeg();
									$n->addTxt('Un joueur a espionné votre base ');
									$n->addLnk('map/place-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
									$n->addBrk()->addTxt('Malheureusement, nous n\'avons pas pu connaître l\'identité de l\'espion.');
									$n->addEnd();
									$notificationManager->add($n);
									break;
								case SpyReport::TYP_CAUGHT:
									$n = new Notification();
									$n->setRPlayer($orbitalBase->rPlayer);
									$n->setTitle('Espionnage intercepté');
									$n->addBeg();
									$n->addLnk('embassy/player-' . $currentPlayer->getId(), $currentPlayer->getName())->addTxt(' a espionné votre base ');
									$n->addLnk('map/place-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
									$n->addBrk()->addTxt('L\'espion s\'est fait attrapé en train de fouiller dans vos affaires.');
									$n->addEnd();
									$notificationManager->add($n);
									break;
								default:
									break;
							}
							break;
						default:
							throw new \LogicException('espionnage pour vaisseau-mère pas encore implémenté');
					}

					$spyReportManager->add($sr);

					# tutorial
					if ($currentPlayer->stepDone == FALSE &&
						$currentPlayer->stepTutorial === TutorialResource::SPY_PLANET) {
						$tutorialHelper->setStepDone();
					}

					$this->addFlash('success', 'Espionnage effectué.');
					return $this->redirectToRoute('spy_reports', ['report' => $sr->id]);
				} else {
					throw new ErrorException('Impossible de lancer un espionnage');
				}
			} else {
				throw new ErrorException('Impossible de lancer un espionnage avec le montant proposé');
			}
		} else {
			throw new FormException('Pas assez d\'informations pour espionner');
		}
	}
}
