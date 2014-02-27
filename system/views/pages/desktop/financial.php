<?php
# background paralax
echo '<div id="background-paralax" class="financial"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ATHENA;
	include_once ZEUS;
	include_once ARES;

	# loading des objets
	$S_PAM_FIN = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	$S_OBM_FIN = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));

	$S_COM_FIN = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('c.rPlayer' => CTR::$data->get('playerId'), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));

	# generalFinancial component
	$ob_generalFinancial = array();
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_generalFinancial[$i] = ASM::$obm->get($i);
	}
	$commander_generalFinancial = array();
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$commander_generalFinancial[$i] = ASM::$com->get($i);
	}
	$player_generalFinancial = ASM::$pam->get(0);
	include COMPONENT . 'athena/financial/generalFinancial.php';

	# impositionFinancial component
	$ob_impositionFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/impositionFinancial.php';

	# routeFinancial component
	$ob_routeFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/routeFinancial.php';

	# investFinancial component
	$ob_investFinancial = $ob_generalFinancial;
	$player_investFinancial = ASM::$pam->get(0);
	include COMPONENT . 'athena/financial/investFinancial.php';

	# taxOutFinancial component
	$ob_taxOutFinancial = $ob_generalFinancial;
	include COMPONENT . 'athena/financial/taxOutFinancial.php';

	# fleetFeesFinancial component
	$commander_fleetFeesFinancial = $commander_generalFinancial;
	include COMPONENT . 'athena/financial/fleetFeesFinancial.php';

	# close
	ASM::$pam->changeSession($S_PAM_FIN);
	ASM::$obm->changeSession($S_OBM_FIN);
	ASM::$com->changeSession($S_COM_FIN);
echo '</div>';
?>