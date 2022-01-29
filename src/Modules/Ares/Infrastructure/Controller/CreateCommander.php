<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Format;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\SchoolClassResource;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Zeus\Helper\CheckName;
use App\Modules\Zeus\Helper\TutorialHelper;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Resource\TutorialResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CreateCommander extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBase $orbitalBase,
		OrbitalBaseManager $orbitalBaseManager,
		CommanderManager $commanderManager,
		PlayerManager $playerManager,
		TutorialHelper $tutorialHelper,
		EntityManager $entityManager,
	): Response {
		$school = 0;
		$name = $request->request->get('name');

		$cn = new CheckName();
		$cn->maxLength = 20;

		if ($name !== FALSE) {
			if (($orbitalBase = $orbitalBaseManager->getPlayerBase($orbitalBase->getId(), $currentPlayer->getId())) !== null) {
				$schoolCommanders = $commanderManager->getBaseCommanders($orbitalBase->getId(), [Commander::INSCHOOL]);

				if (count($schoolCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
					$reserveCommanders = $commanderManager->getBaseCommanders($orbitalBase->getId(), [Commander::RESERVE]);

					if (count($reserveCommanders) < OrbitalBase::MAXCOMMANDERINMESS) {
						$nbrCommandersToCreate = rand(SchoolClassResource::getInfo($school, 'minSize'), SchoolClassResource::getInfo($school, 'maxSize'));

						if ($cn->checkLength($name) && $cn->checkChar($name)) {
							if (SchoolClassResource::getInfo($school, 'credit') <= $currentPlayer->getCredit()) {
								# tutorial
								if ($currentPlayer->stepDone == FALSE &&
									$currentPlayer->stepTutorial === TutorialResource::CREATE_COMMANDER) {
									$tutorialHelper->setStepDone();
								}

								# débit des crédits au joueur
								$playerManager->decreaseCredit($currentPlayer, SchoolClassResource::getInfo($school, 'credit'));

								for ($i = 0; $i < $nbrCommandersToCreate; $i++) {
									$newCommander = new Commander();
									$commanderManager->upExperience($newCommander, rand(SchoolClassResource::getInfo($school, 'minExp'), SchoolClassResource::getInfo($school, 'maxExp')));
									$newCommander->rPlayer = $currentPlayer->getId();
									$newCommander->rBase = $orbitalBase->getId();
									$newCommander->palmares = 0;
									$newCommander->statement = 0;
									$newCommander->name = $name;
									$newCommander->avatar = 't' . rand(1, 21) . '-c' . $currentPlayer->getRColor();
									$newCommander->dCreation = Utils::now();
									$newCommander->uCommander = Utils::now();
									$newCommander->setSexe(1);
									$newCommander->setAge(rand(40, 70));
									$entityManager->persist($newCommander);
									$entityManager->flush($newCommander);
								}
								$this->addFlash('success', $nbrCommandersToCreate . ' commandant' . Format::addPlural($nbrCommandersToCreate) . ' inscrit' . Format::addPlural($nbrCommandersToCreate) . ' au programme d\'entraînement.');
							} else {
								throw new AccessDeniedHttpException('vous n\'avez pas assez de crédit.');
							}
						} else {
							throw new BadRequestHttpException('le nom contient des caractères non autorisé ou trop de caractères.');
						}
					} else {
						throw new ConflictHttpException('Vous ne pouvez pas créer de nouveaux officiers si vous en avez déjà ' . Orbitalbase::MAXCOMMANDERINMESS . ' ou plus.');
					}
				} else {
					throw new ConflictHttpException('Trop d\'officiers en formation. Déplacez des officiers dans le mess pour libérer de la place.');
				}
			} else {
				throw new AccessDeniedHttpException('cette base ne vous appartient pas');
			}
		}
		return $this->redirectToRoute('school');
	}
}
