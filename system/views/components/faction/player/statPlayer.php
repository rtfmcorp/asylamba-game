<?php
# statPlayer component
# in player.demeter package

# affichage des stats joueurs de la faction

# require
	# int 		nbPlayer_statPlayer
	# int 		nbOnlinePlayer_statPlayer
	# int 		nbOfflinePlayer_statPlayer
	# int 		avgVictoryPlayer_statPlayer
	# int 		avgDefeatPlayer_statPlayer
	# int 		avgPointsPlayer_statPlayer

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<h1>Membres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">Joueurs actifs dans la faction</span>';
				echo '<span class="value">' . $nbPlayer_statPlayer . '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box grey">';
				echo '<span class="label">Joueurs en ligne actuellement</span>';
				echo '<span class="value">' . $nbOnlinePlayer_statPlayer . '</span>';
			echo '</div>';
			
			echo '<hr />';

			echo '<div class="number-box grey">';
				echo '<span class="label">Moyenne de l\'expérience des joueurs</span>';
				echo '<span class="value">' . $avgPointsPlayer_statPlayer . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre moyen des victoires des joueurs</span>';
				echo '<span class="value">' . $avgVictoryPlayer_statPlayer . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">Nombre moyen des défaites des joueurs</span>';
				echo '<span class="value">' . $avgDefeatPlayer_statPlayer . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>