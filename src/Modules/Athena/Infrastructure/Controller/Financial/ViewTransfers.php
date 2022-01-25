<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Modules\Zeus\Manager\CreditTransactionManager;
use App\Modules\Zeus\Model\CreditTransaction;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ViewTransfers extends AbstractController
{
	public function __invoke(
		Player $currentPlayer,
		CreditTransactionManager $creditTransactionManager
	): Response {
		// @TODO replace this stateful approach with a proper method call
		$creditTransactionManager->newSession();
		$creditTransactionManager->load(
			['rSender' => $currentPlayer->getId()],
			['dTransaction', 'DESC'],
			[0, 20],
		);
		$sendings = $creditTransactionManager->getAll();

		$creditTransactionManager->newSession();
		$creditTransactionManager->load(
			['rReceiver' => $currentPlayer->getId(), 'type' => CreditTransaction::TYP_PLAYER],
			['dTransaction', 'DESC'],
			[0, 20]
		);
		$receivings = $creditTransactionManager->getAll();

		return $this->render('pages/athena/financial/transfers.html.twig', [
			'sendings' => $sendings,
			'receivings' => $receivings,
		]);
	}
}
