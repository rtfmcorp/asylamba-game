<?php
# rankButcher component
# in rank package

# classement joueur en fonction du nombre de PEV détruits moins les PEV perdus

# require
	# _T PRM 		PLAYER_RANKING_BUTCHER

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$playerRankingManager = $this->getContainer()->get(\Asylamba\Modules\Atlas\Manager\PlayerRankingManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$playerRankingManager->changeSession($PLAYER_RANKING_BUTCHER);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . $mediaPath . 'rank/cup.png">';
		echo '<h2>Boucher</h2>';
		echo '<em>PEV détruits moins PEV perdus</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < $playerRankingManager->size(); $i++) {
				$p = $playerRankingManager->get($i);

				if ($i == 0 && $p->butcherPosition != 1) {
					echo '<a class="more-item" href="' . $appRoot . 'ajax/a-morerank/dir-next/type-butcher/current-' . $p->butcherPosition . '" data-dir="top">';
						echo 'afficher les joueurs précédents';
					echo '</a>';
				}

				echo $p->commonRender($session->get('playerId'), 'butcher', $appRoot, $mediaPath);

				if ($i == $playerRankingManager->size() - 1) {
					echo '<a class="more-item" href="' . $appRoot . 'ajax/a-morerank/dir-prev/type-butcher/current-' . $p->butcherPosition . '">';
						echo 'afficher les joueurs suivants';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
