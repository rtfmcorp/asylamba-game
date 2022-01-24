<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Zeus\Manager\CreditTransactionManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\CreditTransaction;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SendCreditsToFaction extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		PlayerManager $playerManager,
		ColorManager $colorManager,
		CreditTransactionManager $creditTransactionManager,
		EntityManager $entityManager,
	): Response {
		$credit = $request->request->getInt('quantity');

		if (0 >= $credit) {
			throw new BadRequestHttpException('envoi de crédits impossible - il faut envoyer un nombre entier positif');
		}

		if ($currentPlayer->getCredit() < $credit) {
			throw new BadRequestHttpException('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez');
		}

		if (null === ($faction = $colorManager->get($currentPlayer->getRColor()))) {
			throw new NotFoundHttpException('envoi de crédits impossible - faction introuvable');
		}
		# make the transaction
		$playerManager->decreaseCredit($currentPlayer, $credit);
		$faction->increaseCredit($credit);

		# create the transaction
		$ct = new CreditTransaction();
		$ct->rSender = $currentPlayer->getId();
		$ct->type = CreditTransaction::TYP_FACTION;
		$ct->rReceiver = $currentPlayer->getRColor();
		$ct->amount = $credit;
		$ct->dTransaction = Utils::now();
		$ct->comment = NULL;
		$creditTransactionManager->add($ct);

		$entityManager->flush();

		$this->addFlash('success', 'Crédits envoyés');

		return $this->redirectToRoute('financial_transfers');
	}
}
