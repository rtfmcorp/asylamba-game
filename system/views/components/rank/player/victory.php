<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		PLAYER_RANKING_GENERAL

ASM::$prm->changeSession($PLAYER_RANKING_VICTORY);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'resources/resource.png">';
		echo '<h2>Classment des victoires</h2>';
		echo '<em>bla</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$prm->size(); $i++) { 
				$p = ASM::$prm->get($i);
				$status = ColorResource::getInfo($p->color, 'status');

				echo '<div class="player color' . $p->color . '">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $p->rPlayer . '">';
						echo '<img src="' . MEDIA . 'avatar/small/019-5.png" alt="' . $p->name . '" />';
					echo '</a>';

				#	echo '<span class="title">' . $status[$p->getStatus() - 1] . '</span>';
					echo '<span class="title">pas les infos</span>';
					echo '<strong class="name">' . $p->name . '</strong>';
					echo '<span class="points">' . Format::numberFormat($p->victory) . ' victoire</span>';

					echo '<span class="position">#' . $p->victoryPosition . '</span>';
					echo '<span class="variation">';
						echo intval($p->victoryVariation) == 0
							? NULL
							: ($p->victoryVariation > 0
								? '+ ' . $p->victoryVariation
								: '&mdash; ' . abs($p->victoryVariation))
						;
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';