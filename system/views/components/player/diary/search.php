<?php
# search componant
# in player.diary package

# affiche la recherche d'un joueur

# require
#	{player}	player_selected
# 	{ob}		ob_selected
#	bool 		player_ishim

$status = ColorResource::getInfo($player_selected->rColor, 'status');

echo '<div class="component search-player size2 color' . $player_selected->rColor . '">';
	echo '<div class="box">';
		echo '<div class="top">';
			echo '<form action="' . APP_ROOT . 'action/a-searchplayer" method="post">';
				echo '<label>';
					echo 'Recherchez un joueur';
					echo '<input type="text" name="name" class="autocomplete-player ac_input" autocomplete="off" />';
				echo '</label>';
			echo '</form>';
		echo '</div>';
		echo '<div class="center">';
			echo '<img src="' . MEDIA . 'avatar/big/' . $player_selected->avatar . '	.png" alt="avatar de ' . $player_selected->name . '" class="avatar" />';

			echo '<div class="right">';
				echo '<h1>' . $player_selected->name . '</h1>';
				echo '<p>' . $status[$player_selected->status - 1] . ' de ' . ColorResource::getInfo($player_selected->rColor, 'popularName') . '</p>';
				echo '<hr />';
				echo '<p>';
					echo '<span>niveau ' . $player_selected->level . '</span>';
					echo '<span>' . Format::numberFormat($player_selected->experience) . ' xp</span>';
				echo '</p>';

			echo '</div>';
		echo '</div>';
		echo '<div class="bottom">';
			echo '<div class="overflow">';
				echo '<div class="container">';
					for ($i = 0; $i < 6; $i++) { 
						$ob = $ob_selected[0];

						echo '<div class="base">';
							echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($ob->planetPopulation) . '.png" alt="Ma planète" />';
							echo '<div class="right" ' . ($i == 0 ? 'style="display:block;"' : NULL) . '>';
								echo '<strong>' . PlaceResource::get($ob->typeOfBase, 'name') . ' ' . $ob->name . '</strong>';
								echo '<em>' . Format::numberFormat($ob->points) . ' points</em>';
								echo '<a href="' . APP_ROOT . 'map/place-' . $ob->getId() . '">' . Game::formatCoord($ob->xSystem, $ob->ySystem, $ob->position, $ob->sector) . '</a>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>