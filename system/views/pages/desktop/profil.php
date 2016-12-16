<?php

use Asylamba\Classes\Worker\ASM;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

if ($request->query->get('mode') === 'splash') {
	echo '<div class="splash-screen">';
		echo '<div class="modal">';
			echo '<img src="' . MEDIA . 'avatar/big/jm-' . $session->get('playerInfo')->get('color') . '.png" alt="" />';
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
	include COMPONENT . 'publicity.php';

	# loading des objets
	$S_PAM1 = $playerManager->getCurrentSession();
	$playerManager->newSession();
	$playerManager->load(array('id' => $session->get('playerId')));

	$S_OBM1 = $orbitalBaseManager->getCurrentSession();
	$orbitalBaseManager->newSession();
	$orbitalBaseManager->load(array('rPlayer' => $session->get('playerId')), array('dCreation', 'ASC'));

	# playerRoleplayProfil component
	$player_playerRoleplayProfil = $playerManager->get(0);
	include COMPONENT . 'player/playerRoleplayProfil.php';

	# obFastView component
	for ($i = 0; $i < $orbitalBaseManager->size(); $i++) {
		$ob_index = ($i + 1);
		$ob_fastView = $orbitalBaseManager->get($i);
		$fastView_profil = TRUE;
		include COMPONENT . 'bases/fastView.php';
	}

	if ($orbitalBaseManager->size() == 1) {
		include COMPONENT . 'default.php';
	}

	$playerManager->changeSession($S_PAM1);
	$orbitalBaseManager->changeSession($S_OBM1);
echo '</div>';
