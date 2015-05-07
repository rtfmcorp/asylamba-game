<?php
# void

echo '<div class="component">';
	echo '<div class="head">';
		echo '<h1>Paramètres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="info">'	;
			echo 'Informations : Nous utilisons des cookies pour retenir vos préférences d\'affichage sur la carte de la galaxie.';
			echo '</p>';

			echo '<hr />';

			echo '<p class="info">';
			echo 'Pour abandonner la partie, cliquez sur le bouton ci-dessous. Attention, cette action est irréversible. ';
			echo 'Si vous souhaitez recommencer, vous pouvez abandonner la partie ici et recommencer dans cette partie depuis le portail principal.';
			echo '<a class="more-button confirm" href="' . Format::actionBuilder('abandonserver') . '">Abandonner la partie</a>';
			echo '</p>';

		echo '</div>';
	echo '</div>';
echo '</div>';
?>