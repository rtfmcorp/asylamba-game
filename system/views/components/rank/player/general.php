<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		PLAYER_RANKING_GENERAL

ASM::$prm->changeSession($PLAYER_RANKING_GENERAL);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'resources/resource.png">';
		echo '<h2>Classement général</h2>';
		echo '<em>total de vos possession</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$prm->size(); $i++) {
				$p = ASM::$prm->get($i);

				if ($i == 0 && $p->generalPosition != 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-prevrank/type-general/current-' . $p->generalPosition . '">';
						echo 'afficher les joueurs précédents';
					echo '</a>';
				}

				echo $p->commonRender('general');

				if ($i == ASM::$prm->size() - 1) {
					echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-nextrank/type-general/current-' . $p->generalPosition . '">';
						echo 'afficher les joueurs suivants';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';