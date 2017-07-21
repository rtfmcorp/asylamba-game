<?php

$session = $this->getContainer()->get('session_wrapper');

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';

	echo '<form action="' . APP_ROOT . 'inscription/step-3" method="post" >';
		include COMPONENT . 'invisible.php';
		echo '<div class="component inscription color' . $session->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
				echo '<h1>Profil</h1>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<h4>Choisissez votre nom dans le jeu</h4>';

					if ($session->get('inscription')->exist('pseudo')) {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required disabled value="' . $session->get('inscription')->get('pseudo') . '" /></p>';
					} elseif ($session->get('inscription')->get('portalPseudo') !== '') {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" value="' . $session->get('inscription')->get('portalPseudo') . '" /></p>';
					} else {
						echo '<p><input type="text" name="pseudo" id="pseudo" maxlength="15" required placeholder="pseudo" /></p>';
					}

					echo '<p>Nous déconseillons les noms moins roleplay, essayez de coller avec l\'histoire et les moeurs de votre faction.</p>';
					echo '<p>Ce nom ne pourra pas être changé plus tard.</p>';

					echo '<hr />';

					echo '<button type="submit" class="chooseLink">';
						echo '<strong>Définir son profil</strong>';
						echo '<em>et passer à l\'étape suivante</em>';
					echo '</button>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription size3 color' . $session->get('inscription')->get('ally') . '">';
			echo '<div class="head skin-2">';
				echo '<h2>Choisissez un avatar</h2>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<div class="avatars">';
						for ($i = 1; $i <= NB_AVATAR; $i++) {
							if (!in_array($i, array(77, 19))) {
								$avatar    = $i < 10 ? '00' : '0';
								$avatar   .= $i . '-' . $session->get('inscription')->get('ally');
								$avatars[] = $avatar;
							}
						}

						shuffle($avatars);

						$isLong = FALSE;
						$cursor = 0;
						for ($i = 0; $i < (NB_AVATAR - 2); $i++) { 
							echo '<div class="avatar">';
								echo '<input type="radio" name="avatar" value="' . $avatars[$i] . '" id="avatar' . $i . '" required />';
								echo '<label for="avatar' . $i . '">';
									echo '<img src="' . MEDIA . 'avatar/big/' . $avatars[$i] . '.png" alt="" />';
								echo '</label>';
							echo '</div>';

							$cursor++;

							if (!$isLong && $cursor == 5) {
								$cursor = 0;
								$isLong = TRUE;
								echo '<br />';
							} elseif ($isLong && $cursor == 6) {
								$cursor = 0;
								$isLong = FALSE;
								echo '<br />';
							}
						}
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
