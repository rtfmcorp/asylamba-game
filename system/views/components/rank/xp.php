<?php
# rankXP component
# in rank package

# liste les joueurs aux meilleurs rang

# require
	# [{player}] 		player_rankXP

echo '<div class="component player rank">';
	echo '<div class="head skin-2">';
		echo '<h2>Classement Général</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$position = 1;

			foreach ($player_rankXP as $p) {
				$status = ColorResource::getInfo($p->getRColor(), 'status');

				echo '<div class="player color' . $p->getRColor() . '">';
					echo '<span class="position">#' . $position . '</span>';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $p->getId() . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $p->getAvatar() . '.png" alt="' . $p->getName() . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$p->getStatus() - 1] . '</span>';
					echo '<strong class="name">' . $p->getName() . '</strong>';
					echo '<span class="experience">' . Format::numberFormat($p->getExperience()) . ' points</span>';
				echo '</div>';

				$position++;
			}
		echo '</div>';
	echo '</div>';
echo '</div>';