<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Modules\Artemis\Manager\SpyReportManager;
use App\Modules\Artemis\Model\SpyReport;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewSpyReports extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		PlaceManager $placeManager,
		SpyReportManager $spyReportManager,
	): Response {
		$focusedSpyReport = $this->getFocusedLastReport($request, $currentPlayer, $spyReportManager);

		return $this->render('pages/ares/fleet/spy_reports.html.twig', [
			'spy_reports' => $this->getSpyReports($currentPlayer, $spyReportManager),
			'focused_spy_report' => $focusedSpyReport,
			'focused_spy_report_place' => $focusedSpyReport ? $placeManager->get($focusedSpyReport->rPlace) : null,
		]);
	}

	private function getSpyReports(Player $currentPlayer, SpyReportManager $spyReportManager): array
	{
		$spyReportManager->newSession();
		$spyReportManager->load(array('rPlayer' => $currentPlayer->getId()), array('dSpying', 'DESC'), array(0, 40));

		# listReport component
		$spyreport_listSpy = array();
		for ($i = 0; $i < $spyReportManager->size(); $i++) {
			$spyreport_listSpy[$i] = $spyReportManager->get($i);
		}

		return $spyreport_listSpy;
	}

	private function getFocusedLastReport(Request $request, Player $currentPlayer, SpyReportManager $spyReportManager): SpyReport | null
	{
		$spyReportManager->newSession();

		if ($request->query->has('report')) {
			$spyReportManager->load(array('id' => $request->query->get('report'), 'rPlayer' => $currentPlayer->getId()));
		} else {
			$spyReportManager->load(array('rPlayer' => $currentPlayer->getId()), array('dSpying', 'DESC'), array(0, 1));
		}

		return ($spyReportManager->size() == 1) ? $spyReportManager->get(0) : null;
	}
}
