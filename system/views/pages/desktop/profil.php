<?php
# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'profilElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ZEUS;
	include_once ATHENA;

	# loading des objets
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')), array('dCreation', 'ASC'));
	// ASM::$msm->load(array('rPlayer' => CTR::$data->get('playerId')));

	# playerRoleplayProfil component
	$player_playerRoleplayProfil = ASM::$pam->get(0);
	include COMPONENT . 'player/playerRoleplayProfil.php';

	# playerTechnicalProfil component
	$player_playerTechnicalProfil = ASM::$pam->get(0);
	include COMPONENT . 'player/playerTechnicalProfil.php';

	# obFastView component
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_index = ($i + 1);
		$ob_obFastView = ASM::$obm->get($i);
		include COMPONENT . 'bases/obFastView.php';
	}

	# msFastView component
	/* for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_index = ($i + 1);
		$ob_obFastView = ASM::$obm->get($i);
		include COMPONENT . 'athena/obFastView.php';
	} */
echo '</div>';
?>