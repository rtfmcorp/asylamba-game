<?php

namespace App\Modules\Artemis\Infrastructure\Controller\SpyReport;

use App\Modules\Artemis\Manager\SpyReportManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteReport extends AbstractController
{
	public function __invoke(
		Player $currentPlayer,
		SpyReportManager $spyReportManager,
		int $id,
	): Response {
			$spyReportManager->newSession();
			$spyReportManager->load(array('id' => $id));
			$report = $spyReportManager->get();
			if ($spyReportManager->size() == 1) {

				if ($report->rPlayer == $currentPlayer->getId()) {
					$spyReportManager->deleteById($report->id);
					$this->addFlash('success', 'Rapport d\'espionnage supprimé');
					return $this->redirectToRoute('spy_reports');
				} else {
					throw new AccessDeniedHttpException('Ce rapport ne vous appartient pas');
				}
			} else {
				throw new NotFoundHttpException('Ce rapport n\'existe pas');
			}
	}
}
