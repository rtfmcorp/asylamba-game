<?php

namespace App\Modules\Ares\Infrastructure\Controller\CombatReport;

use App\Classes\Entity\EntityManager;
use App\Modules\Ares\Manager\ReportManager;
use App\Modules\Ares\Model\Report;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteReport extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		ReportManager $reportManager,
		EntityManager $entityManager,
		int $id,
	): Response {
		if (($report = $reportManager->get($id)) !== null) {
			if ($report->rPlayerAttacker == $currentPlayer->getId()) {
				$report->statementAttacker = Report::DELETED;
			} elseif ($report->rPlayerDefender == $currentPlayer->getId()) {
				$report->statementDefender = Report::DELETED;
			} else {
				throw new AccessDeniedHttpException('Ce rapport ne vous appartient pas');
			}
		} else {
			throw new NotFoundHttpException('Ce rapport n\'existe pas');
		}
		$entityManager->flush();

		return $this->redirectToRoute('fleet_archives');
	}
}
