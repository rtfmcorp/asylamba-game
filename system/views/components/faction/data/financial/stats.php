<?php
echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Finance</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Richesse de la faction</span>';
				echo '<span class="value">' . Format::number($faction->credits) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" /></span>';
			echo '</div>';

			echo '<p>évolution des crédits (20 derniers jours)</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>