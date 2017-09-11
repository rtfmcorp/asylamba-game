<?php
# bases loading

use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
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

        $commanders_obSituation = $commanderManager->getBaseCommanders($base->getId(), [Commander::AFFECTED, Commander::MOVING]);

        $ob_index = 0;
        $ob_fastView = $base;
        $fastView_profil = false;
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
        $baseMissions = $recyclingMissionManager->getBaseActiveMissions($ob_recycling->rPlace);
        $missionsLogs = $recyclingLogManager->getBaseActiveMissionsLogs($ob_recycling->rPlace);
        $missionQuantity = count($baseMissions);
        
        include COMPONENT . 'bases/ob/recycling.php';
        if ($missionQuantity === 0) {
            include COMPONENT . 'default.php';
        }
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
