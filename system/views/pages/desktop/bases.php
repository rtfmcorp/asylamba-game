<?php
# bases loading
include_once ATHENA;

# choix de la base
# si base donnée en argument
if (CTR::$get->exist('base')) {
	if (CTRHelper::baseExist(CTR::$get->get('base'))) {
		ASM::$obm->load(array('rPlace' => CTR::$get->get('base')));
		CTR::$data->get('playerParams')->add('base', CTR::$get->get('base'));
	} else {
		header('Location: ' . APP_ROOT);
		exit();
	}
# si paramètre de base initialisé
} elseif (CTR::$data->get('playerParams')->exist('base')) {
	ASM::$obm->load(array('rPlace' => CTR::$data->get('playerParams')->get('base')));
# sinon base par défaut
} else {
	ASM::$obm->load(array('rPlace' => CTR::$data->get('playerBase')->get('ob')->get(0)->get('id')));
}

$base = ASM::$obm->get(0);

# background paralax
echo '<div id="background-paralax" class="bases"></div>';

# inclusion des elements
include 'basesElement/movers.php';
include 'basesElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	# obNav component
	$ob_obNav = $base;
	include COMPONENT . 'athena/bases/obNav.php';

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') {
		include_once ARES;
		
		$ob_obSituation = $base;
		$commanders_obSituation = array();

		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('rBase' => $base->getId(), 'statement' => array(COM_AFFECTED, COM_MOVING)));
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			$commanders_obSituation[] = ASM::$com->get($i);
		}
		ASM::$com->changeSession($S_COM1);
		include COMPONENT . 'athena/bases/obSituation.php';
	} elseif (CTR::$get->get('view') == 'generator') {
		$ob_generator = $base;
		include COMPONENT . 'athena/bases/generator.php';
	} elseif (CTR::$get->get('view') == 'refinery') {
		$ob_refinery = $base;
		include COMPONENT . 'athena/bases/refinery.php';
	} elseif (CTR::$get->get('view') == 'dock1') {
		$ob_dock1 = $base;
		include COMPONENT . 'athena/bases/dock1.php';
	} elseif (CTR::$get->get('view') == 'dock2') {
		$ob_dock2 = $base;
		include COMPONENT . 'athena/bases/dock2.php';
	} elseif (CTR::$get->get('view') == 'technosphere') {
		$ob_tech = $base;
		include COMPONENT . 'athena/bases/technosphere.php';
	} elseif (CTR::$get->get('view') == 'commercialplateforme') {
		$ob_compPlat = $base;
		include COMPONENT . 'athena/bases/comPlat.php';
	} elseif (CTR::$get->get('view') == 'university') {
		$ob_university = $base;
		include COMPONENT . 'athena/bases/university.php';
	} elseif (CTR::$get->get('view') == 'school') {
		$ob_school = $base;
		include COMPONENT . 'athena/bases/school.php';
	} elseif (CTR::$get->get('view') == 'antispy') {
		$ob_antispy = $base;
		include COMPONENT . 'athena/bases/antiSpy.php';
	} else {
		CTR::redirect('404');
	}
echo '</div>';

?>