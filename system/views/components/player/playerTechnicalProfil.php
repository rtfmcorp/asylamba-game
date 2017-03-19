<?php
# playerTechnicalProfil componant
# in zeus package

# affiche le profil technique d'un joueur

# require
	# {player}	player_playerTechnicalProfil

use Asylamba\Classes\Library\Format;

$playerBaseLevel = $this->getContainer()->getParameter('zeus.player.base_level');

echo '<div class="component">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'embassy/player-' . $player_playerTechnicalProfil->getId() . '">voir votre journal</a></span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">niveau</span>';
				echo '<span class="value">' . $player_playerTechnicalProfil->getLevel() . '</span>';
			echo '</div>';

			$exp = $player_playerTechnicalProfil->getExperience();
			$nlv = $playerBaseLevel * (pow(2, ($player_playerTechnicalProfil->getLevel() - 1)));
			$clv = $playerBaseLevel * (pow(2, ($player_playerTechnicalProfil->getLevel() - 2)));
			$prc = ((($exp - $clv) * 200) / $nlv);

			echo '<div class="number-box">';
				echo '<span class="label">expérience</span>';
				echo '<span class="value">';
					echo Format::numberFormat($exp) . ' / ';
					echo Format::numberFormat($nlv);
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $prc . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">victoires</span>';
				echo '<span class="value">' . $player_playerTechnicalProfil->getVictory() . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">défaites</span>';
				echo '<span class="value">' . $player_playerTechnicalProfil->getDefeat() . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
