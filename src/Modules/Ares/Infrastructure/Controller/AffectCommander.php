<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Zeus\Helper\TutorialHelper;
use App\Modules\Zeus\Resource\TutorialResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AffectCommander extends AbstractController
{
	public function __invoke(
		Request $request,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
		OrbitalBaseHelper $orbitalBaseHelper,
		TutorialHelper $tutorialHelper,
		EntityManager $entityManager,
		int $id,
	): Response {
		if (($commander = $commanderManager->get($id)) === null) {
			throw new \ErrorException('Cet officier n\'existe pas ou ne vous appartient pas');
		}
		$session = $request->getSession();

		$orbitalBase = $orbitalBaseManager->get($commander->rBase);

# checker si on a assez de place !!!!!
		$nbrLine1 = $commanderManager->countCommandersByLine($commander->rBase, 1);
		$nbrLine2 = $commanderManager->countCommandersByLine($commander->rBase, 2);

		if ($commander->statement == Commander::INSCHOOL || $commander->statement == Commander::RESERVE) {
			if ($nbrLine2 < PlaceResource::get($orbitalBase->typeOfBase, 'r-line')) {
				$commander->dAffectation = Utils::now();
				$commander->statement = Commander::AFFECTED;
				$commander->line = 2;

				# tutorial
				if ($session->get('playerInfo')->get('stepDone') == FALSE && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
					$tutorialHelper->setStepDone();
				}

				$this->addFlash('success', 'Votre officier ' . $commander->getName() . ' a bien été affecté en force de réserve');
				$entityManager->flush();
				return $this->redirectToRoute('fleet_details', ['id' => $commander->getId()]);
			} elseif ($nbrLine1 < PlaceResource::get($orbitalBase->typeOfBase, 'l-line')) {
				$commander->dAffectation =Utils::now();
				$commander->statement = Commander::AFFECTED;
				$commander->line = 1;

				# tutorial
				if ($session->get('playerInfo')->get('stepDone') == FALSE && $session->get('playerInfo')->get('stepTutorial') === TutorialResource::AFFECT_COMMANDER) {
					$tutorialHelper->setStepDone();
				}

				$this->addFlash('success', 'Votre officier ' . $commander->getName() . ' a bien été affecté en force active');
				$entityManager->flush();
				return $this->redirectToRoute('fleet_details', ['id' => $commander->getId()]);
			} else {
				throw new \ErrorException('Votre base a dépassé la capacité limite de officiers en activité');
			}
		} elseif ($commander->statement == Commander::AFFECTED) {
			$baseCommanders = $commanderManager->getBaseCommanders($commander->rBase, [Commander::INSCHOOL]);

			$commander->uCommander = Utils::now();
			if (count($baseCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
				$commander->statement = Commander::INSCHOOL;
				$this->addFlash('success', 'Votre officier ' . $commander->getName() . ' a été remis à l\'école');
				$commanderManager->emptySquadrons($commander);
			} else {
				$commander->statement = Commander::RESERVE;
				$this->addFlash('success', 'Votre officier ' . $commander->getName() . ' a été remis dans la réserve de l\'armée');
				$commanderManager->emptySquadrons($commander);
			}
			$entityManager->flush();
			return $this->redirectToRoute('school');
		} else {
			throw new ConflictHttpException('Le status de votre officier ne peut pas être modifié');
		}
	}
}
