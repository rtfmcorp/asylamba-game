<?php


$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
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

	$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));

	# playerRoleplayProfil component
	$player_playerRoleplayProfil = $playerManager->get($session->get('playerId'));
	include COMPONENT . 'player/playerRoleplayProfil.php';

	# obFastView component
	foreach ($playerBases as $ob_fastView) {
		$ob_index = ($i + 1);
		$fastView_profil = TRUE;
		include COMPONENT . 'bases/fastView.php';
	}

	if (count($playerBases) === 1) {
		include COMPONENT . 'default.php';
	}
echo '</div>';
