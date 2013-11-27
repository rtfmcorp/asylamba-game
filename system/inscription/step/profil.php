<?php
include_once ZEUS;

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	echo '<form action="' . APP_ROOT . 'inscription/step-3" method="post" >';
		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
				echo '<h1>Profil</h1>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p class="info">Remplissez maintenant les informations de votre profil.</p>';
					echo '<hr />';
					if (CTR::$data->get('inscription')->exist('pseudo')) {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required disabled value="' . CTR::$data->get('inscription')->get('pseudo') . '" /></p>';
					} elseif (CTR::$data->get('inscription')->get('portalPseudo') !== '') {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" value="' . CTR::$data->get('inscription')->get('portalPseudo') . '" /></p>';
					} else {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" /></p>';
					}
					echo '<p class="info">Nous déconseillons les noms moins roleplay, essayez de coller avec l\'histoire et les moeurs de votre faction.</p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription size2 color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head skin-2">';
				echo '<h2>Choisissez un avatar</h2>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<div class="avatars">';
						for ($i = 1; $i < 50; $i++) { 
							if ($i < 10) {
								$avatars[] = '00' . $i . '-' . CTR::$data->get('inscription')->get('ally');
							} else {
								$avatars[] =  '0' . $i . '-' . CTR::$data->get('inscription')->get('ally');
							}
						}
						shuffle($avatars);
						for ($i = 0; $i < 24; $i++) { 
							echo '<div class="avatar">';
								echo '<input type="radio" name="avatar" value="' . $avatars[$i] . '" id="avatar' . $i . '" required />';
								echo '<label for="avatar' . $i . '">';
									echo '<img src="' . MEDIA . 'avatar/big/' . $avatars[$i] . '.png" alt="" />';
								echo '</label>';
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p><input type="submit" id="nextStep" value="valider et passer à l\'étape suivante" /></p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
?>