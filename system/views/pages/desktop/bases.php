<?php
# bases loading
include_once ATHENA;
# choix de la base
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
	} elseif (CTR::$get->get('view') == 'commercialplateforme' && $base->levelCommercialPlateform > 0) {
		$ob_compPlat = $base;
		include COMPONENT . 'bases/ob/comPlat.php';
	} elseif (CTR::$get->get('view') == 'school') {
		$ob_school = $base;
		include COMPONENT . 'bases/ob/school.php';
	} else {
		CTR::redirect('bases');
	}
echo '</div>';

?>