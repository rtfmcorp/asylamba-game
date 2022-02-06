<?php

namespace App\Modules\Ares\Infrastructure\Controller\CombatReport;

use App\Classes\Entity\EntityManager;
use App\Modules\Ares\Manager\ReportManager;
use App\Modules\Ares\Model\Report;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArchiveReport extends AbstractController
{
	public function __invoke(
		Player $currentPlayer,
		ReportManager $reportManager,
		EntityManager $entityManager,
		int $id
	): Response {
		if (($report = $reportManager->get($id)) !== null) {
			if ($currentPlayer->getId() == $report->rPlayerAttacker) {
				if ($report->statementAttacker == Report::STANDARD) {
					$report->statementAttacker = Report::ARCHIVED;
				} else {
					$report->statementAttacker = Report::STANDARD;
				}
			} else if ($currentPlayer->getId() == $report->rPlayerDefender) {
				if ($report->statementDefender == Report::STANDARD) {
					$report->statementDefender = Report::ARCHIVED;
				} else {
					$report->statementDefender = Report::STANDARD;
				}
			} else {
				throw new AccessDeniedHttpException('Ce rapport ne vous appartient pas.');
			}
		} else {
			throw new NotFoundHttpException('Ce rapport n\'existe pas.');
		}
		$entityManager->flush();

		return $this->redirectToRoute('fleet_archives');
	}
}
