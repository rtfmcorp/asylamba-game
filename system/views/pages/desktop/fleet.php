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
		$S_COM_UKN = $commanderManager->getCurrentSession();

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

		$S_COM_ATK = $commanderManager->newSession();
		$commanderManager->load(array('c.id' => $commandersId));

		for ($i = 0; $i < count($obsets); $i++) {
			for ($j = 0; $j < $commanderManager->size(); $j++) {
				if ($commanderManager->get($j)->rDestinationPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commanderManager->get($j);
				}
			}
		}
		
		# commander manager : yours
		$S_COM_BSE = $commanderManager->newSession();
		$commanderManager->load(array('c.rPlayer' => $session->get('playerId'), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)), array('c.rBase', 'DESC'));

		for ($i = 0; $i < count($obsets); $i++) {
			for ($j = 0; $j < $commanderManager->size(); $j++) {
				if ($commanderManager->get($j)->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commanderManager->get($j);
				}
			}
		}

		include COMPONENT . 'fleet/listFleet.php';

		# commander id
		if ($request->query->has('commander')) {
			$S_COM_ID = $commanderManager->getCurrentSession();
			$commanderManager->newSession();
			$commanderManager->load(array(
				'c.rPlayer' => $session->get('playerId'),
				'c.id' => $request->query->get('commander'),
				'c.statement' => array(Commander::AFFECTED, Commander::MOVING)
			));
			
			if ($commanderManager->size() == 1) {
				$S_OBM_DOCK = $orbitalBaseManager->getCurrentSession();
				$orbitalBaseManager->newSession();
				$orbitalBaseManager->load(array('rPlace' => $commanderManager->get()->getRBase()));

				# commanderDetail component
				$commander_commanderDetail = $commanderManager->get();
				$commander_commanderFleet = $commanderManager->get();
				$ob_commanderFleet = $orbitalBaseManager->get();
				
				# commanderFleet component
				include COMPONENT . 'fleet/commanderFleet.php';
				include COMPONENT . 'fleet/commanderDetail.php';

				$commanderManager->changeSession($S_COM_ID);
				$orbitalBaseManager->changeSession($S_OBM_DOCK);
			} else {
				throw new ErrorException('Cet officier ne vous appartient pas ou n\'existe pas');
				//CTR::redirect('fleet');
			}
		}

		$commanderManager->changeSession($S_COM_UKN);
	} elseif ($request->query->get('view') == 'overview') {
		$S_COM_UKN = $commanderManager->getCurrentSession();
		$S_OBM_UKN = $orbitalBaseManager->getCurrentSession();

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
		$commanderManager->newSession();
		$commanderManager->load(['c.rPlayer' => $session->get('playerId'), 'c.statement' => [Commander::AFFECTED, Commander::MOVING]], ['c.rBase', 'DESC']);

		for ($i = 0; $i < count($obsets); $i++) {
			for ($j = 0; $j < $commanderManager->size(); $j++) {
				if ($commanderManager->get($j)->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commanderManager->get($j);
				}
			}
		}

		# ship in dock
		$orbitalBaseManager->newSession();
		$orbitalBaseManager->load(['rPlayer' => $session->get('playerId')]);

		for ($i = 0; $i < count($obsets); $i++) {
			for ($j = 0; $j < $orbitalBaseManager->size(); $j++) {
				if ($orbitalBaseManager->get($j)->rPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['dock'] = $orbitalBaseManager->get($j)->shipStorage;
				}
			}
		}

		include COMPONENT . 'fleet/overview.php';

		$orbitalBaseManager->changeSession($S_OBM_UKN);
		$commanderManager->changeSession($S_COM_UKN);
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

			$S_PLM_SPY = $placeManager->getCurrentSession();
			$placeManager->newSession();
			$placeManager->load(array('id' => $spyreport->rPlace));
			$place_spy = $placeManager->get(0);

			include COMPONENT . 'fleet/spyReport.php';

			$placeManager->changeSession($S_PLM_SPY);
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
			$S_RPM2 = $reportManager->getCurrentSession();
			$reportManager->newSession();
			$reportManager->load(array('r.id' => $request->query->get('report')));

			if ($reportManager->size() == 1 && ($reportManager->get()->rPlayerAttacker == $session->get('playerId') || $reportManager->get()->rPlayerDefender == $session->get('playerId'))) {
				$S_PAM1 = $playerManager->getCurrentSession();
				$playerManager->newSession();
				$playerManager->load(array('id' => array($reportManager->get()->rPlayerAttacker, $reportManager->get()->rPlayerDefender)));

				$report_report = $reportManager->get();

				$attacker_report = $playerManager->getById($report_report->rPlayerAttacker);
				$defender_report = $playerManager->getById($report_report->rPlayerDefender);

				include COMPONENT . 'fleet/report.php';
				include COMPONENT . 'fleet/manage-report.php';

				$playerManager->changeSession($S_PAM1);
			} else {
				throw new ErrorException('Ce rapport ne vous appartient pas ou n\'existe pas');
				//CTR::redirect('fleet/view-archive');
			}

			$reportManager->changeSession($S_RPM2);
		} else {
			include COMPONENT . 'default.php';
			include COMPONENT . 'default.php';
		}

		$littleReportManager->changeSession($S_LRM1);
	} elseif ($request->query->get('view') == 'memorial') {
		# loading des objets
		$S_COM1 = $commanderManager->getCurrentSession();
		$commanderManager->newSession();
		$commanderManager->load(array('c.rPlayer' => $session->get('playerId'), 'c.statement' => Commander::DEAD), array('c.palmares', 'DESC'));

		# memorialTxt component
		include COMPONENT . 'fleet/memorialTxt.php';

		for ($i = 0; $i < $commanderManager->size(); $i++) {
			if ($i < 6) {
				$commander_commanderDetail = $commanderManager->get($i);
				include COMPONENT . 'fleet/commanderDetail.php';
			} else {
				$commander_shortMemorial = $commanderManager->get($i);
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

		$commanderManager->changeSession($S_COM1);
	} else {
		$response->redirect('404');
	}

echo '</div>';
