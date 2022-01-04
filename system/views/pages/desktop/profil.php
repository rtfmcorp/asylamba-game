<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$mediaPath = $container->getParameter('media');
$componentPath = $container->getParameter('component');

if ($request->query->get('mode') === 'splash') {
	echo '<div class="splash-screen">';
		echo '<div class="modal">';
			echo '<img src="' . $mediaPath . 'avatar/big/jm-' . $session->get('playerInfo')->get('color') . '.png" alt="" />';
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
	include $componentPath . 'publicity.php';

	$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));

	# playerRoleplayProfil component
	$player_playerRoleplayProfil = $playerManager->get($session->get('playerId'));
	include $componentPath . 'player/playerRoleplayProfil.php';

	# obFastView component
	foreach ($playerBases as $ob_fastView) {
		$ob_index = ($i + 1);
		$fastView_profil = TRUE;
		include $componentPath . 'bases/fastView.php';
	}

	if (count($playerBases) === 1) {
		include $componentPath . 'default.php';
	}
echo '</div>';
