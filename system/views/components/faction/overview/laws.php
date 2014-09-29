<?php
# createTopic component
# in demeter.forum package

# création d'un topic

# require

echo '<div class="component uni">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Lois actives</h4>';
			echo '<p><em>Aucune loi active</em></p>';

			/*for ($i = 0; $i < 2; $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'university/mathematics.png" alt="">';
						echo '<strong>Mathématiques</strong>';
						echo '<em>niveau 73</em>';
					echo '</div>';
				echo '</div>';
			}*/

			echo '<h4>Lois en cours de votation</h4>';
			echo '<p><em>Aucune loi en cours de votation</em></p>';

			/*for ($i = 0; $i < 3; $i++) { 
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'university/mathematics.png" alt="">';
						echo '<strong>Mathématiques</strong>';
						echo '<em>niveau 73</em>';
					echo '</div>';
				echo '</div>';
			}*/

			echo '<h4>Bonus de factions</h4>';

			$bonus = ColorResource::getInfo($faction->id, 'bonus');
			foreach ($bonus as $b) {
				echo '<div class="build-item">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . $b['path'] . '" alt="" />';
						echo '<strong>' . $b['title'] . '</strong>';
						echo '<em>' . $b['desc'] . '</em>';
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';