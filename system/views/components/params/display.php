<?php
# display
echo '<div class="component params">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres d\'affichage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-updatedisplayparams" method="post">';
				echo '<div class="info-building"><h4>Paramètres généraux</h4></div>';

				echo '<label class="checkbox hb rt" title="Affiche les ascenseur par défaut de votre système. Ces derniers sont moins beaux mais peuvent résoudrent certains problèmes">';
					if (CTR::$cookie->equal('movers', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('movers', FALSE)) {
						$check = NULL;
					} else {
						$check = NULL;
					}
					echo '<input type="checkbox" name="movers" value="1" ' . $check . ' />';
					echo 'Ascenseur système';
				echo '</label>';

				echo '<label class="checkbox hb rt" title="Améliorent les performances d\'affichage lorsqu\'elles sont désactivées">';
					if (CTR::$cookie->equal('anims', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('anims', FALSE)) {
						$check = NULL;
					} else {
						$check = 'checked';
					}
					echo '<input type="checkbox" name="anims" value="1" ' . $check . ' />';
					echo 'Animations activées';
				echo '</label>';

				/*echo '<label class="radio">';
					echo '<input type="checkbox" name="panel" value="1" ' . $check . ' />';
					echo 'panneau vide par défaut';
				echo '</label>';*/

				echo '<div class="info-building"><h4>Paramètres de la carte</h4></div>';

				echo '<label class="checkbox">';
					if (CTR::$cookie->equal('minimap', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('minimap', FALSE)) {
						$check = NULL;
					} else {
						$check = 'checked';
					}
					echo '<input type="checkbox" name="minimap" value="1" ' . $check . ' />';
					echo 'Afficher la minimap';
				echo '</label>';

				echo '<label class="checkbox">';
					if (CTR::$cookie->equal('rc', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('rc', FALSE)) {
						$check = NULL;
					} else {
						$check = NULL;
					}
					echo '<input type="checkbox" name="rc" value="1" ' . $check . ' />';
					echo 'Afficher les routes commerciales';
				echo '</label>';

				echo '<label class="checkbox">';
					if (CTR::$cookie->equal('spying', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('spying', FALSE)) {
						$check = NULL;
					} else {
						$check = 'checked';
					}
					echo '<input type="checkbox" name="spying" value="1" ' . $check . ' />';
					echo 'Afficher les cercles de contre-espionnage';
				echo '</label>';

				echo '<label class="checkbox">';
					if (CTR::$cookie->equal('movements', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('movements', FALSE)) {
						$check = NULL;
					} else {
						$check = 'checked';
					}
					echo '<input type="checkbox" name="movements" value="1" ' . $check . ' />';
					echo 'Afficher vos mouvement de flotte';
				echo '</label>';

				echo '<label class="checkbox">';
					if (CTR::$cookie->equal('attacks', TRUE)) {
						$check = 'checked';
					} elseif (CTR::$cookie->equal('attacks', FALSE)) {
						$check = NULL;
					} else {
						$check = 'checked';
					}
					echo '<input type="checkbox" name="attacks" value="1" ' . $check . ' />';
					echo 'Afficher les attaques entrantes';
				echo '</label>';

				echo '<p>';
					echo '<input type="submit" value="sauvegarder" />';
				echo '</p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>