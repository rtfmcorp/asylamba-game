<?php
# rankTrader component
# in rank package

# classement joueur en fonction des ressources stockées sur ses bases

# require
	# _T PRM 		PLAYER_RANKING_TRADER

$playerRankingManager = $this->getContainer()->get('atlas.player_ranking_manager');
$session = $this->getContainer()->get('session_wrapper');

$playerRankingManager->changeSession($PLAYER_RANKING_TRADER);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Trader</h2>';
		echo '<em>Revenu de toutes les routes commerciales par relève</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < $playerRankingManager->size(); $i++) {
				$p = $playerRankingManager->get($i);

				if ($i == 0 && $p->traderPosition != 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-next/type-trader/current-' . $p->traderPosition . '" data-dir="top">';
						echo 'afficher les joueurs précédents';
					echo '</a>';
				}

				echo $p->commonRender($session->get('playerId'), 'trader');

				if ($i == $playerRankingManager->size() - 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-prev/type-trader/current-' . $p->traderPosition . '">';
						echo 'afficher les joueurs suivants';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';