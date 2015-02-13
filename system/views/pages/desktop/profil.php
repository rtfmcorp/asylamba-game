<?php
if (CTR::$get->equal('mode', 'splash')) {
	echo '<div class="splash-screen">';
		echo '<div class="modal">';
			echo '<img src="' . MEDIA . 'avatar/big/jm-' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="" />';
			echo '<h1>Bienvenue sur Asylamba</h1>';
			echo '<h2>Afin de bien commencer le jeu, cliquez sur l\'icône du tutoriel. Celui-ci vous guidera dans les premières étapes du jeu.</h2>';
		echo '</div>';
	echo '</div>';
}

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
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')), array('dCreation', 'ASC'));
#	ASM::$msm->load(array('rPlayer' => CTR::$data->get('playerId')));

	# playerRoleplayProfil component
	$player_playerRoleplayProfil = ASM::$pam->get(0);
	include COMPONENT . 'player/playerRoleplayProfil.php';

	# playerTechnicalProfil component
	# $player_playerTechnicalProfil = ASM::$pam->get(0);
	# include COMPONENT . 'player/playerTechnicalProfil.php';

	# obFastView component
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_index = ($i + 1);
		$ob_obFastView = ASM::$obm->get($i);
		include COMPONENT . 'bases/obFastView.php';
	}

	if (ASM::$obm->size() == 1) {
		include COMPONENT . 'default.php';
	}

	# msFastView component
	/* for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$ob_index = ($i + 1);
		$ob_obFastView = ASM::$obm->get($i);
		include COMPONENT . 'athena/obFastView.php';
	} */
	ASM::$pam->changeSession($S_PAM1);
	ASM::$obm->changeSession($S_OBM1);
echo '</div>';
?>