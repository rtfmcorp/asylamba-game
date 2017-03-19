<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$reportManager = $this->getContainer()->get('ares.report_manager');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');

# background paralax
echo '<div id="background-paralax" class="fleet"></div>';

# inclusion des elements
include 'fleetElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if (!$request->query->has('view') OR $request->query->get('view') == 'movement' OR $request->query->get('view') == 'main') {
		# set d'orbitale base
		$obsets = array(); $j = 0;
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			if ($request->cookies->get('p' . Params::LIST_ALL_FLEET, Params::LIST_ALL_FLEET) || $session->get('playerBase')->get('ob')->get($i)->get('id') == $session->get('playerParams')->get('base')) {
				$obsets[$j] = array();

				$obsets[$j]['info'] = array();
				$obsets[$j]['fleets'] = array();

				$obsets[$j]['info']['id'] = $session->get('playerBase')->get('ob')->get($i)->get('id');
				$obsets[$j]['info']['name'] = $session->get('playerBase')->get('ob')->get($i)->get('name');
				$obsets[$j]['info']['type'] = $session->get('playerBase')->get('ob')->get($i)->get('type');
				$obsets[$j]['info']['img'] = $session->get('playerBase')->get('ob')->get($i)->get('img');

				$j++;
			}
		}

		# commander manager : incoming attack
		$commandersId = array(0);
		for ($i = 0; $i < $session->get('playerEvent')->size(); $i++) {
			if ($session->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				if ($session->get('playerEvent')->get($i)->get('eventInfo')->size() > 0) {
					$commandersId[] = $session->get('playerEvent')->get($i)->get('eventId');
				}
			}
		}

		$attackingCommanders = $commanderManager->getCommandersByIds($commandersId);

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($attackingCommanders as $commander) {
				if ($commander->rDestinationPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}
		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'DESC']);

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($commanders as $commander) {
				if ($commander->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}

		include COMPONENT . 'fleet/listFleet.php';

		# commander id
		if ($request->query->has('commander')) {
			if (($commander = $commanderManager->get($request->query->get('commander'))) !== null && $commander->rPlayer === $session->get('playerId') && in_array($commander->getStatement(), [Commander::AFFECTED, Commander::MOVING])) {
				$base = $orbitalBaseManager->get($commander->getRBase());

				# commanderDetail component
				$commander_commanderDetail = $commander;
				$commander_commanderFleet = $commander;
				$ob_commanderFleet = $base;
				
				# commanderFleet component
				include COMPONENT . 'fleet/commanderFleet.php';
				include COMPONENT . 'fleet/commanderDetail.php';
			} else {
				throw new ErrorException('Cet officier ne vous appartient pas ou n\'existe pas');
				//CTR::redirect('fleet');
			}
		}
	} elseif ($request->query->get('view') == 'overview') {
		# set d'orbitale base
		$obsets = [];
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$obsets[$i] = array();

			$obsets[$i]['info'] = [];
			$obsets[$i]['fleets'] = [];
			$obsets[$i]['dock'] = [];

			$obsets[$i]['info']['id'] = $session->get('playerBase')->get('ob')->get($i)->get('id');
			$obsets[$i]['info']['name'] = $session->get('playerBase')->get('ob')->get($i)->get('name');
			$obsets[$i]['info']['type'] = $session->get('playerBase')->get('ob')->get($i)->get('type');
		}

		# commander manager : yours
		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'DESC']);

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($commanders as $commander) {
				if ($commander->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}
		# ship in dock
		$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($playerBases as $orbitalBase) {
				if ($orbitalBase->rPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['dock'] = $orbitalBase->shipStorage;
				}
			}
		}
		include COMPONENT . 'fleet/overview.php';
	} elseif ($request->query->get('view') == 'spyreport') {
		# loading des objets
		$S_SRM1 = $spyReportManager->getCurrentSession();
		$spyReportManager->newSession();
		$spyReportManager->load(array('rPlayer' => $session->get('playerId')), array('dSpying', 'DESC'), array(0, 40));

		# listReport component
		$spyreport_listSpy = array();
		for ($i = 0; $i < $spyReportManager->size(); $i++) { 
			$spyreport_listSpy[$i] = $spyReportManager->get($i);
		}
		include COMPONENT . 'fleet/listSpy.php';

		# report component
		$spyReportManager->newSession();

		if ($request->query->has('report')) {
			$spyReportManager->load(array('id' => $request->query->get('report'), 'rPlayer' => $session->get('playerId')));
		} else {
			$spyReportManager->load(array('rPlayer' => $session->get('playerId')), array('dSpying', 'DESC'), array(0, 1));
		}

		if ($spyReportManager->size() == 1) {
			$spyreport = $spyReportManager->get(0);

			$place_spy = $placeManager->get($spyreport->rPlace);

			include COMPONENT . 'fleet/spyReport.php';
		} else {
			if ($request->query->has('report')) {
				throw new ErrorException('Ce rapport ne vous appartient pas ou n\'existe pas');
				//CTR::redirect('fleet/view-spyreport');
			} else {
				include COMPONENT . 'default.php';
				include COMPONENT . 'default.php';
			}
		}

		$spyReportManager->changeSession($S_SRM1);
	} elseif ($request->query->get('view') == 'archive') {
		# loading des objets
		$S_LRM1 = $littleReportManager->getCurrentSession();
		$littleReportManager->newSession();

		if ($request->query->get('mode', 'archived')) {
			$archived = Report::ARCHIVED;
		} else {
			$archived = Report::STANDARD;
		}

		$rebels = $request->cookies->get('p'. Params::SHOW_REBEL_REPORT, Params::SHOW_REBEL_REPORT)
			? NULL
			: 'AND p2.rColor != 0';

		if ($request->cookies->get('p'. Params::SHOW_ATTACK_REPORT, Params::SHOW_ATTACK_REPORT)) {
			$littleReportManager->loadByRequest(
				'WHERE rPlayerAttacker = ? AND statementAttacker = ? ' . $rebels . ' ORDER BY dFight DESC LIMIT 0, 50',
				[$session->get('playerId'), $archived]
			);
		} else {
			$littleReportManager->loadByRequest(
				'WHERE rPlayerDefender = ? AND statementDefender = ? ' . $rebels . ' ORDER BY dFight DESC LIMIT 0, 50',
				[$session->get('playerId'), $archived]
			);
		}

		# listReport component
		$report_listReport = array();
		for ($i = 0; $i < $littleReportManager->size(); $i++) { 
			$report_listReport[$i] = $littleReportManager->get($i);
		}
		$type_listReport = 1;
		include COMPONENT . 'fleet/list-report.php';

		# report component
		if ($request->query->has('report')) {
			$report = $reportManager->get($request->query->get('report'));

			if (($report->rPlayerAttacker == $session->get('playerId') || $report->rPlayerDefender == $session->get('playerId'))) {
				$attacker_report = $playerManager->get($report->rPlayerAttacker);
				$defender_report = $playerManager->get($report->rPlayerDefender);

				include COMPONENT . 'fleet/report.php';
				include COMPONENT . 'fleet/manage-report.php';
			} else {
				throw new ErrorException('Ce rapport ne vous appartient pas ou n\'existe pas');
				//CTR::redirect('fleet/view-archive');
			}
		} else {
			include COMPONENT . 'default.php';
			include COMPONENT . 'default.php';
		}

		$littleReportManager->changeSession($S_LRM1);
	} elseif ($request->query->get('view') == 'memorial') {
		# loading des objets
		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::DEAD], ['c.palmares' => 'DESC']);

		# memorialTxt component
		include COMPONENT . 'fleet/memorialTxt.php';

		foreach ($commanders as $commander) {
			if ($i < 6) {
				$commander_commanderDetail = $commander;
				include COMPONENT . 'fleet/commanderDetail.php';
			} else {
				$commander_shortMemorial = $commander;
				include COMPONENT . 'default.php';
			}
		}

		if (isset($commander_commanderDetail) && count($commander_commanderDetail) > 0) {
		} else {
			include COMPONENT . 'default.php';
			include COMPONENT . 'default.php';
		}

		if (isset($commander_shortMemorial) && count($commander_shortMemorial) > 0) {
		}
	} else {
		$response->redirect('404');
	}

echo '</div>';
