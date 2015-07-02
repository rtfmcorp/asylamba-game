<?php
# rankVictory component
# in rank package

# classement faction en fonction de la somme des points des joueurs de chaque faction

# require
	# _T PRM 		FACTION_RANKING_POINTS

ASM::$frm->changeSession($FACTION_RANKING_POINTS);

echo '<div class="component profil panel-info">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Informations sur les conditions de victoire</h4>';

			echo '<p>Les différents classements (général, richesse et territorial) rapportent des points à la faction chaque jour en fonction de la position de cette dernière dans les classements. Ces points sont additionnés et font monter une jauge. Lorsqu\'un faction atteins le nombre de point requis, le serveur se termine et la victoire est revendiquée.';

			echo '<div class="center-box">';
				echo '<span class="label">Points requis pour mettre fin au serveur</span>';
				echo '<span class="value">15 000</span>';
			echo '</div>';

			echo '<p>Les différents classements n\'ont pas tous le même poids. Les gain journaliers en fonction du classement et de la position dans ce dernier sont disponibles dans le tableau ci-dessous.';

			echo '<div class="table">';
				echo '<table>';
					echo '<tr>';
						echo '<td>Position</td>';
						echo '<td>1<sup>er</sup></td>';
						echo '<td>2<sup>ème</sup></td>';
						echo '<td>3<sup>ème</sup></td>';
						echo '<td>4<sup>ème</sup></td>';
					echo '</tr>';
					echo '<tr class="small-grey">';
						echo '<td>Général</td>';
						echo '<td>+ 12</td>';
						echo '<td>+ 6</td>';
						echo '<td>+ 3</td>';
						echo '<td>+ 0</td>';
					echo '</tr>';
					echo '<tr class="small-grey">';
						echo '<td>Richesse</td>';
						echo '<td>+ 8</td>';
						echo '<td>+ 4</td>';
						echo '<td>+ 2</td>';
						echo '<td>+ 0</td>';
					echo '</tr>';
					echo '<tr class="small-grey">';
						echo '<td>Général</td>';
						echo '<td>+ 20</td>';
						echo '<td>+ 10</td>';
						echo '<td>+ 5</td>';
						echo '<td>+ 0</td>';
					echo '</tr>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';