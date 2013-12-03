<?php
# diaryBases componant
# in zeus package

# affiche les bases d'un joueur

# require
	# {ob}		ob_diaryBases

echo '<div class="component">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Liste des bases</h3>';
			for ($i = 0; $i < count($ob_diaryBases); $i++) { 
				$ob = $ob_diaryBases[$i];
				echo '<div class="number-box grey">';
					echo '<span class="value">' . $ob->getName() . '</span>';
					echo '<span class="label">secteur ' . $ob->getSector() . ' | ' . $ob->getPoints() . ' points</span>';

					echo '<span class="group-link">';
						echo '<a href="' . APP_ROOT . 'map/place-' . $ob->getId() . '" class="hb lt" title="voir sur la carte">→</a>';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>