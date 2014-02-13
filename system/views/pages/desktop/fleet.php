<?php
# background paralax
echo '<div id="background-paralax" class="fleet"></div>';

# inclusion des elements
include 'fleetElement/subnav.php';
include 'profilElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# fleetNav component
	# include COMPONENT . 'ares/fleetNav.php';
	
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') {
		# inclusion des modules
		include_once ARES;
		
		# loading des objets
		$commandersId = array(0);
		for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				$info = CTR::$data->get('playerEvent')->get($i)->get('eventInfo');
				if ($info[0] === TRUE) { $commandersId[] = CTR::$data->get('playerEvent')->get($i)->get('eventId'); }
			}
		}
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('id' => $commandersId));

		# listFleetIncoming component
		$commander_listFleetIncoming = array();
		for ($i = 0; $i < ASM::$com->size(); $i++) {
			$commander_listFleetIncoming[$i] = ASM::$com->get($i);
		}
		include COMPONENT . 'ares/listFleetIncoming.php';
		
		ASM::$com->changeSession($S_COM1);
	} elseif (CTR::$get->get('view') == 'movement') {
		# inclusion des modules
		include_once ARES;

		# loading des objets
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => array(COM_AFFECTED, COM_MOVING)), array('rBase', 'DESC'));

		# listFleet component
		$commander_listFleet = array();
		for ($i = 0; $i < ASM::$com->size(); $i++) {
			$commander_listFleet[$i] = ASM::$com->get($i);
		}
		include COMPONENT . 'ares/listFleet.php';

		if (CTR::$get->exist('commander')) {
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array(
				'rPlayer' => CTR::$data->get('playerId'),
				'id' => CTR::$get->get('commander'),
				'statement' => array(COM_AFFECTED, COM_MOVING)
			));

			if (ASM::$com->size() == 1) {
				include_once ATHENA;
				$S_OBM_1 = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession();
				ASM::$obm->load(array('rPlace' => ASM::$com->get()->getRBase()));

				# commanderDetail component
				$commander_commanderDetail = ASM::$com->get();
				include COMPONENT . 'ares/commanderDetail.php';
				# commanderFleet component
				$commander_commanderFleet = ASM::$com->get();
				$ob_commanderFleet = ASM::$obm->get();
				include COMPONENT . 'ares/commanderFleet.php';

				ASM::$com->changeSession($S_COM2);
				ASM::$obm->changeSession($S_OBM_1);
			}
		}

		ASM::$com->changeSession($S_COM1);
	# } elseif (CTR::$get->get('view') == 'commanders') {
		# code
	} elseif (CTR::$get->get('view') == 'archive') {
		# inclusion des modules
		include_once ARES;

		# loading des objets
		$S_RPM1 = ASM::$rpm->getCurrentSession();
		ASM::$rpm->newSession();
		ASM::$rpm->load(array('rPlayerAttacker' => CTR::$data->get('playerId')));
		ASM::$rpm->load(array('rPlayerDefender' => CTR::$data->get('playerId')));

		# listReport component
		$report_listReport = array();
		for ($i = 0; $i < ASM::$rpm->size(); $i++) { 
			$report_listReport[$i] = ASM::$rpm->get($i);
		}
		usort($report_listReport, function($a, $b) {
			$ta = $a->dFight;
			$tb = $b->dFight;

			if ($ta == $tb) { return 0; }
		    return (strtotime($ta) > strtotime($tb)) ? -1 : 1;
		});
		include COMPONENT . 'ares/listReport.php';

		# report component
		if (CTR::$get->exist('report')) {
			$S_RPM2 = ASM::$rpm->getCurrentSession();
			ASM::$rpm->newSession();
			ASM::$rpm->load(array('id' => CTR::$get->get('report')));

			if (ASM::$rpm->size() == 1) {
				include_once ZEUS;
				$S_PAM1 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				ASM::$pam->load(array('id' => array(ASM::$rpm->get()->rPlayerAttacker, ASM::$rpm->get()->rPlayerDefender)));

				$report_report = ASM::$rpm->get();

				$db = DataBase::getInstance();
				$qr = $db->query('SELECT * FROM bigReport WHERE id = ' . $report_report->rBigReport);
				$aw = $qr->fetch();

				$attacker_report = ASM::$pam->getById($report_report->rPlayerAttacker);
				$defender_report = ASM::$pam->getById($report_report->rPlayerDefender);
				$commanders_report = unserialize($aw['commanders']);
				$fight_report = unserialize($aw['fight']);

				include COMPONENT . 'ares/report.php';

				ASM::$pam->changeSession($S_PAM1);
			}
			ASM::$rpm->changeSession($S_RPM2);
		}

		ASM::$rpm->changeSession($S_RPM1);
	} elseif (CTR::$get->get('view') == 'memorial') {
		# inclusion des modules
		include_once ARES;

		# loading des objets
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => COM_DEAD), array('palmares', 'DESC'));

		# memorialTxt component
		include COMPONENT . 'ares/memorialTxt.php';

		for ($i = 0; $i < ASM::$com->size(); $i++) {
			if ($i < 6) {
				$commander_commanderDetail = ASM::$com->get($i);
				include COMPONENT . 'ares/commanderDetail.php';
			} else {
				$commander_shortMemorial = ASM::$com->get($i);
				include COMPONENT . 'ares/shortMemorial.php';
			}
		}

		if (isset($commander_commanderDetail) && count($commander_commanderDetail) > 0) {
		} else {
			# aucun commandant mort
		}

		if (isset($commander_shortMemorial) && count($commander_shortMemorial) > 0) {
		}

		ASM::$com->changeSession($S_COM1);
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>