<?php
# antispy component
# in athena.bases package

# affichage des renseignements

# require
	# {orbitalBase}		ob_antispy

include_once ARES;


echo '<div class="component size2 dock1">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/antispy.png" alt="" />';
		echo '<h2>Renseignement</h2>';
		echo '<em>radar</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>  --- Onglet en construction ---  </h3>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">Description à venir.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>