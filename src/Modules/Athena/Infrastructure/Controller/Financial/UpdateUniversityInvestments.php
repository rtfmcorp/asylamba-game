<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Modules\Zeus\Helper\TutorialHelper;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Resource\TutorialResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateUniversityInvestments extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		PlayerManager $playerManager,
		TutorialHelper $tutorialHelper,
	): Response {
		if (0 === ($investment = $request->request->getInt('credit'))) {
			throw new BadRequestHttpException('Montant invalide');
		}

		if (500000 < $investment) {
			throw new BadRequestHttpException('La limite maximale d\'investissement dans l\'Université est de 500\'000 crédits.');
		}

		$session = $request->getSession();
		$playerManager->updateUniversityInvestment($currentPlayer->getId(), $investment);

		// tutorial
		if ($session->get('playerInfo')->get('stepDone') === false &&
			$session->get('playerInfo')->get('stepTutorial') === TutorialResource::MODIFY_UNI_INVEST) {
			$tutorialHelper->setStepDone();
		}

		$this->addFlash('success', 'L\'investissement dans l\'université a été modifié');

		return $this->redirectToRoute('financial_investments');
	}
}
