<?php
# rankResources component
# in rank package

# classement joueur en fonction des ressources stockées sur ses bases

# require
    # _T PRM 		PLAYER_RANKING_RESOURCES

$playerRankingManager = $this->getContainer()->get('atlas.player_ranking_manager');
$session = $this->getContainer()->get('session_wrapper');

$playerRankingManager->changeSession($PLAYER_RANKING_RESOURCES);

echo '<div class="component player rank">';
    echo '<div class="head skin-4">';
        echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
        echo '<h2>Mineur</h2>';
        echo '<em>Production totale par relève</em>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            for ($i = 0; $i < $playerRankingManager->size(); $i++) {
                $p = $playerRankingManager->get($i);

                if ($i == 0 && $p->resourcesPosition != 1) {
                    echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-next/type-resources/current-' . $p->resourcesPosition . '" data-dir="top">';
                    echo 'afficher les joueurs précédents';
                    echo '</a>';
                }

                echo $p->commonRender($session->get('playerId'), 'resources');

                if ($i == $playerRankingManager->size() - 1) {
                    echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-prev/type-resources/current-' . $p->resourcesPosition . '">';
                    echo 'afficher les joueurs suivants';
                    echo '</a>';
                }
            }
        echo '</div>';
    echo '</div>';
echo '</div>';
