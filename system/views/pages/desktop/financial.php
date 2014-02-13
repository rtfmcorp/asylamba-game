<?php
# background paralax
echo '<div id="background-paralax" class="financial"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'profilElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ATHENA;
	include_once ARES;

	# loading des objets
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));
	ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => array(COM_AFFECTED, COM_MOVING)));

	# generalFinancial component
	$ob_generalFinancial = array();
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_generalFinancial[$i] = ASM::$obm->get($i);
	}
	$commander_generalFinancial = array();
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$commander_generalFinancial[$i] = ASM::$com->get($i);
	}
	include COMPONENT . 'athena/financial/generalFinancial.php';

	# impositionFinancial component
	$ob_impositionFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/impositionFinancial.php';

	# routeFinancial component
	$ob_routeFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/routeFinancial.php';

	# investFinancial component
	$ob_investFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/investFinancial.php';

	# taxOutFinancial component
	$ob_taxOutFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/taxOutFinancial.php';

	# fleetFeesFinancial component
	$commander_fleetFeesFinancial = $commander_generalFinancial;
	include COMPONENT . 'athena/financial/fleetFeesFinancial.php';
echo '</div>';
?>