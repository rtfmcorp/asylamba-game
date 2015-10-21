<?php
# display
echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres des annonces</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Publicité</h3>';
			echo '<p>Nous avons ajouté en encart publicitaire en bas à gauche de plusieurs page sur le jeu. Ce n\'est pas du tout définitif. Il s\'agit d\'un test de la part des développeurs du jeu.</p>';
			echo '<p>Vous avez ici la possibilité de masquer cet encart si vous le souhaitez. Cependant nous vous rappelons que le jeu est totalement gratuit et que tous les frais actuels sont à notre charge.</p>';
			echo '<p>Cela comprend les frais de nom de domaine, les frais d\'hébergement et les heures de travail bénévole.</p>';
			echo '<p>Nous vous encourageons donc à laisser ce paramètre activé et à désactiver les bloqueurs de publicités que vous pourriez avoir installé sur votre navigateur. <strong>Nous vous en sommes très reconnaissants !</strong></p>';

			echo '<a href="' . Format::actionBuilder('switchadvertisement') . '"" class="on-off-button ' . (CTR::$data->get('playerInfo')->get('premium') ? 'disabled' : '') . '">';
				echo (CTR::$data->get('playerInfo')->get('premium') ? 'Afficher des publicités' : 'Cacher les publicités');
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
