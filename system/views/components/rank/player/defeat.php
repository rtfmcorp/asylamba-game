<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		PLAYER_RANKING_GENERAL

ASM::$prm->changeSession($PLAYER_RANKING_DEFEAT);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Défaite</h2>';
		echo '<em>Total des défaites de vos officiers</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$prm->size(); $i++) {
				$p = ASM::$prm->get($i);

				if ($i == 0 && $p->defeatPosition != 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-next/type-defeat/current-' . $p->defeatPosition . '" data-dir="top">';
						echo 'afficher les joueurs précédents';
					echo '</a>';
				}

				echo $p->commonRender('defeat');

				if ($i == ASM::$prm->size() - 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-prev/type-defeat/current-' . $p->defeatPosition . '">';
						echo 'afficher les joueurs suivants';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';