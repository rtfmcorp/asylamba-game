<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use GalaxyConfiguration;

$sm = new SectorManager();
$sm->load();

$S_OBM_MAP = ASM::$obm->getCurrentSession();
ASM::$obm->newSession();
ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));

# base choice
$defaultBase = ASM::$obm->getById(CTR::$data->get('playerParams')->get('base'));

# map default position
$x = $defaultBase->getXSystem();
$y = $defaultBase->getYSystem();
$systemId = 0;

# other default location
# par place
if (CTR::$get->exist('place')) {
	$S_PLM_MAP = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => CTR::$get->get('place')));

	if (ASM::$plm->size() == 1) {
		$x = ASM::$plm->get(0)->getXSystem();
		$y = ASM::$plm->get(0)->getYSystem();
		$systemId = ASM::$plm->get(0)->getRSystem();
	}
	
	ASM::$plm->changeSession($S_PLM_MAP);
# par system
} elseif (CTR::$get->exist('system')) {
	$_SYS_MAP = ASM::$sys->getCurrentSession();
	ASM::$sys->newSession();
	ASM::$sys->load(array('id' => CTR::$get->get('system')));

	if (ASM::$sys->size() == 1) {
		$x = ASM::$sys->get(0)->xPosition;
		$y = ASM::$sys->get(0)->yPosition;
		$systemId = CTR::$get->get('system');
	}
	
	ASM::$sys->changeSession($_SYS_MAP);
# par coordonnée
} elseif (CTR::$get->exist('x') && CTR::$get->exist('y')) {
	$x = CTR::$get->get('x');
	$y = CTR::$get->get('y');
}

# control include
include 'mapElement/option.php';
include 'mapElement/content.php';
include 'mapElement/commanders.php';
include 'mapElement/coordbox.php';

if ($systemId != 0) {
	$S_SYS1 = ASM::$sys->getCurrentSession();
	ASM::$sys->newSession();
	ASM::$sys->load(array('id' => $systemId));

	if (ASM::$sys->size() == 1) {
		# objet système
		$system = ASM::$sys->get();

		# objet place
		$places = array();
		$S_PLM1 = ASM::$plm->getCurrentSession();
		ASM::$plm->newSession();
		ASM::$plm->load(array('rSystem' => $systemId), array('position'));
		for ($i = 0; $i < ASM::$plm->size(); $i++) {
			$places[] = ASM::$plm->get($i);
		}
		ASM::$plm->changeSession($S_PLM1);

		$noAJAX = TRUE;

		# inclusion du "composant"
		echo '<div id="action-box" style="bottom: 0px;">';
			include PAGES . 'desktop/mapElement/actionbox.php';
		echo '</div>';
	}
	ASM::$sys->changeSession($S_SYS1);
} else {
	echo '<div id="action-box"></div>';
}

# map sytems
echo '<div id="map" ';
	echo 'data-begin-x-position="' . $x . '" ';
	echo 'data-begin-y-position="' . $y . '" ';
	echo 'data-related-place="' . $defaultBase->getId() . '"';
	echo 'data-map-size="' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '"';
	echo 'data-map-ratio="' . GalaxyConfiguration::$scale . '"';
	echo 'style="width: ' . ((GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size'])) . 'px; height: ' . ((GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size'])) . 'px;"';
echo '>';
	include 'mapElement/layer/background.php';

	include 'mapElement/layer/sectors.php';

	include 'mapElement/layer/spying.php';
	include 'mapElement/layer/ownbase.php';
	include 'mapElement/layer/commercialroutes.php';
	include 'mapElement/layer/fleetmovements.php';
	include 'mapElement/layer/attacks.php';

	include 'mapElement/layer/systems.php';
	include 'mapElement/layer/map-info.php';
echo '</div>';

ASM::$obm->changeSession($S_OBM_MAP);
