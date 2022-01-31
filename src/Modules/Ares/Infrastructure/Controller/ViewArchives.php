<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Classes\Container\Params;
use App\Modules\Ares\Manager\LiveReportManager;
use App\Modules\Ares\Manager\ReportManager;
use App\Modules\Ares\Model\Report;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ViewArchives extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		ReportManager $reportManager,
		LiveReportManager $liveReportManager,
		PlayerManager $playerManager,
	): Response {
		$archived = ($request->query->get('mode') === 'archived') ? Report::ARCHIVED : Report::STANDARD;

		$rebels = (bool) $request->cookies->get('p'. Params::SHOW_REBEL_REPORT, Params::$params[Params::SHOW_REBEL_REPORT]);

		$combatReports = ($request->cookies->get('p'. Params::SHOW_ATTACK_REPORT, Params::$params[Params::SHOW_ATTACK_REPORT]))
			? $liveReportManager->getAttackReportsByMode($currentPlayer->getId(), $rebels, $archived)
			: $liveReportManager->getDefenseReportsByMode($currentPlayer->getId(), $rebels, $archived)
		;

		if ($request->query->has('report')) {
			$report = $reportManager->get($request->query->get('report'));

			if (!\in_array($currentPlayer->getId(), [$report->rPlayerAttacker, $report->rPlayerDefender])) {
				throw new AccessDeniedHttpException('You cannot access this report');
			}

			$reportAttacker = $playerManager->get($report->rPlayerAttacker);
			$reportDefender = $playerManager->get($report->rPlayerDefender);
		}

		return $this->render('pages/ares/fleet/archives.html.twig', [
			'combat_reports' => $combatReports,
			'default_parameters' => Params::$params,
			'report' => $report ?? null,
			'report_attacker' => $reportAttacker ?? null,
			'report_defender' => $reportDefender ?? null,
		]);
	}

	private function getCombatReports()
	{

	}
}
