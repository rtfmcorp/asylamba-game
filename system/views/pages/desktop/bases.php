<?php
# bases loading

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\RecyclingMission;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$recyclingLogManager = $this->getContainer()->get('athena.recycling_log_manager');

# choix de la base
$base = $orbitalBaseManager->get($session->get('playerParams')->get('base'));

# background paralax
echo '<div id="background-paralax" class="bases"></div>';

# inclusion des elements
include 'basesElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
	# obNav component
	if (!$request->query->has('view') || $request->query->get('view') === 'main') {
		$ob_obSituation = $base;
		$commanders_obSituation = array();

		$S_COM1 = $commanderManager->getCurrentSession();
		$commanderManager->newSession();
		$commanderManager->load(array('c.rBase' => $base->getId(), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));
		for ($i = 0; $i < $commanderManager->size(); $i++) { 
			$commanders_obSituation[] = $commanderManager->get($i);
		}
		$commanderManager->changeSession($S_COM1);

		$ob_index = 0;
		$ob_fastView = $base;
		$fastView_profil = FALSE;
		include COMPONENT . 'bases/fastView.php';

		include COMPONENT . 'bases/ob/situation.php';
		include COMPONENT . 'bases/ob/base-type.php';

		if ($session->get('playerBase')->get('ob')->size() > 1) {
			include COMPONENT . 'bases/ob/leavebase.php';
		}
	} elseif ($request->query->get('view') == 'generator' && $base->levelGenerator > 0) {
		$ob_generator = $base;
		include COMPONENT . 'bases/ob/generator.php';
	} elseif ($request->query->get('view') == 'refinery' && $base->levelRefinery > 0) {
		$ob_refinery = $base;
		include COMPONENT . 'bases/ob/refinery.php';
	} elseif ($request->query->get('view') == 'dock1' && $base->levelDock1 > 0) {
		$ob_dock1 = $base;
		include COMPONENT . 'bases/ob/dock1.php';
	} elseif ($request->query->get('view') == 'dock2' && $base->levelDock2 > 0) {
		$ob_dock2 = $base;
		include COMPONENT . 'bases/ob/dock2.php';
	} elseif ($request->query->get('view') == 'technosphere' && $base->levelTechnosphere > 0) {
		$ob_tech = $base;
		include COMPONENT . 'bases/ob/technosphere.php';
	} elseif ($request->query->get('view') == 'commercialplateforme' && $base->levelCommercialPlateforme > 0) {
		$ob_compPlat = $base;
		include COMPONENT . 'bases/ob/comPlat.php';
	} elseif ($request->query->get('view') == 'storage' && $base->levelStorage > 0) {
		$ob_storage = $base;
		include COMPONENT . 'bases/ob/storage.php';
	} elseif ($request->query->get('view') == 'recycling' && $base->levelRecycling > 0) {
		$ob_recycling = $base;

		# load recycling missions
		$S_REM1 = $recyclingMissionManager->getCurrentSession();
		$recyclingMissionManager->newSession();
		$recyclingMissionManager->load(array('rBase' => $ob_recycling->rPlace, 'statement' => array(RecyclingMission::ST_BEING_DELETED, RecyclingMission::ST_ACTIVE)));
		$recyclingSession = $recyclingMissionManager->getCurrentSession();

		$recyclingIds = [0];
		for ($i = 0; $i < $recyclingMissionManager->size(); $i++) { 
			$recyclingIds[] = $recyclingMissionManager->get($i)->id;
		}

		$S_RLM1 = $recyclingLogManager->getCurrentSession();
		$recyclingLogManager->newSession();
		$recyclingLogManager->load(['rRecycling' => $recyclingIds], ['dLog', 'DESC'], [0, 10 * count($recyclingIds)]);
		$missionLogSessions = $recyclingLogManager->getCurrentSession();

		include COMPONENT . 'bases/ob/recycling.php';
		if ($recyclingMissionManager->size() == 0) {
			include COMPONENT . 'default.php';
		}

		$recyclingLogManager->changeSession($S_RLM1);
		$recyclingMissionManager->changeSession($S_REM1);
	} elseif ($request->query->get('view') == 'spatioport' && $base->levelSpatioport > 0) {
		$ob_spatioport = $base;
		include COMPONENT . 'bases/ob/spatioport.php';
	} elseif ($request->query->get('view') == 'school') {
		$ob_school = $base;
		include COMPONENT . 'bases/ob/school.php';
	} else {
		$this->getContainer()->get('app.response')->redirect('bases');
	}
echo '</div>';