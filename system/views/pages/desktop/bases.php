<?php
# bases loading
include_once ATHENA;
# choix de la base
$S_OBM1 = ASM::$obm->getCurrentSession();
ASM::$obm->newSession();
ASM::$obm->load(array('rPlace' => CTR::$data->get('playerParams')->get('base')));
$base = ASM::$obm->get(0);

# background paralax
echo '<div id="background-paralax" class="bases"></div>';

# inclusion des elements
include 'basesElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# obNav component
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') {
		include_once ARES;
		
		$ob_obSituation = $base;
		$commanders_obSituation = array();

		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.rBase' => $base->getId(), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			$commanders_obSituation[] = ASM::$com->get($i);
		}
		ASM::$com->changeSession($S_COM1);
		include COMPONENT . 'bases/ob/situation.php';

		if (CTR::$data->get('playerBase')->get('ob')->size() > 1) {
			include COMPONENT . 'bases/ob/leavebase.php';
		}
	} elseif (CTR::$get->get('view') == 'generator' && $base->levelGenerator > 0) {
		$ob_generator = $base;
		include COMPONENT . 'bases/ob/generator.php';
	} elseif (CTR::$get->get('view') == 'refinery' && $base->levelRefinery > 0) {
		$ob_refinery = $base;
		include COMPONENT . 'bases/ob/refinery.php';
	} elseif (CTR::$get->get('view') == 'dock1' && $base->levelDock1 > 0) {
		$ob_dock1 = $base;
		include COMPONENT . 'bases/ob/dock1.php';
	} elseif (CTR::$get->get('view') == 'dock2' && $base->levelDock2 > 0) {
		$ob_dock2 = $base;
		include COMPONENT . 'bases/ob/dock2.php';
	} elseif (CTR::$get->get('view') == 'technosphere' && $base->levelTechnosphere > 0) {
		$ob_tech = $base;
		include COMPONENT . 'bases/ob/technosphere.php';
	} elseif (CTR::$get->get('view') == 'commercialplateforme' && $base->levelCommercialPlateforme > 0) {
		$ob_compPlat = $base;
		include COMPONENT . 'bases/ob/comPlat.php';
	} elseif (CTR::$get->get('view') == 'storage' && $base->levelStorage > 0) {
		$ob_storage = $base;
		include COMPONENT . 'bases/ob/storage.php';
	} elseif (CTR::$get->get('view') == 'recycling' && $base->levelRecycling > 0) {
		$ob_recycling = $base;

		# load recycling missions
		$S_REM1 = ASM::$rem->getCurrentSession();
		ASM::$rem->newSession();
		ASM::$rem->load(array('rBase' => $ob_recycling->rPlace, 'statement' => array(RecyclingMission::ST_BEING_DELETED, RecyclingMission::ST_ACTIVE)));
		$recyclingSession = ASM::$rem->getCurrentSession();

		$recyclingIds = [];
		for ($i = 0; $i < ASM::$rem->size(); $i++) { 
			$recycling[] = ASM::$rem->get($i)->id;
		}

		$S_RLM1 = ASM::$rlm->getCurrentSession();
		ASM::$rlm->newSession();
		ASM::$rlm->load(['rRecycling' => $recycling], ['dLog', 'DESC'], [0, 10 * count($recycling)]);
		$missionLogSessions = ASM::$rlm->getCurrentSession();

		ASM::$rlm->changeSession($S_RLM1);
		ASM::$rem->changeSession($S_REM1);

		include COMPONENT . 'bases/ob/recycling.php';
		//include COMPONENT . 'default.php';
	} elseif (CTR::$get->get('view') == 'spatioport' && $base->levelSpatioport > 0) {
		$ob_spatioport = $base;
		include COMPONENT . 'bases/ob/spatioport.php';
	} elseif (CTR::$get->get('view') == 'school') {
		$ob_school = $base;
		include COMPONENT . 'bases/ob/school.php';
	} else {
		CTR::redirect('bases');
	}
echo '</div>';
ASM::$com->changeSession($S_OBM1);

?>