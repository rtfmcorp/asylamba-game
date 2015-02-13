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
					echo '<h4>Choisissez votre nom dans le jeu</h4>';

					if (CTR::$data->get('inscription')->exist('pseudo')) {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required disabled value="' . CTR::$data->get('inscription')->get('pseudo') . '" /></p>';
					} elseif (CTR::$data->get('inscription')->get('portalPseudo') !== '') {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" value="' . CTR::$data->get('inscription')->get('portalPseudo') . '" /></p>';
					} else {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" /></p>';
					}

					echo '<p>Nous déconseillons les noms moins roleplay, essayez de coller avec l\'histoire et les moeurs de votre faction.</p>';
					echo '<p>Ce nom ne pourra pas être changé plus tard.</p>';
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
						for ($i = 1; $i <= NB_AVATAR; $i++) {
							$avatar    = $i < 10 ? '00' : '0';
							$avatar   .= $i . '-' . CTR::$data->get('inscription')->get('ally');
							$avatars[] = $avatar;
						}

						shuffle($avatars);

						$break = 3; $j = 1;
						for ($i = 0; $i < 24; $i++) { 
							echo '<div class="avatar">';
								echo '<input type="radio" name="avatar" value="' . $avatars[$i] . '" id="avatar' . $i . '" required />';
								echo '<label for="avatar' . $i . '">';
									echo '<img src="' . MEDIA . 'avatar/big/' . $avatars[$i] . '.png" alt="" />';
								echo '</label>';
							echo '</div>';

							if ($j == $break) {
								echo '<br />';
								$break = $break == 3 ? 4 : 3;
								$j = 0;
							}

							$j++;
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
					echo '<button type="submit" class="chooseLink">';
						echo '<strong>Définir son profil</strong>';
						echo '<em>et passer à l\'étape suivante</em>';
					echo '</button>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
?>