<?php

namespace App\Modules\Artemis\Infrastructure\Controller\SpyReport;

use App\Modules\Artemis\Manager\SpyReportManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DeleteAllReports extends AbstractController
{
	public function __invoke(SpyReportManager $spyReportManager, Player $currentPlayer): Response
	{
		$nbr = $spyReportManager->deleteByRPlayer($currentPlayer->getId());

		if ($nbr > 1) {
			$this->addFlash('success', $nbr . ' rapports ont été supprimés.');
		} else if ($nbr == 1) {
			$this->addFlash('success', 'Un rapport a été supprimé.');
		} else {
			$this->addFlash('success', 'Tous vos rapports ont déjà été supprimés.');
		}

		return $this->redirectToRoute('spy_reports');
	}
}
