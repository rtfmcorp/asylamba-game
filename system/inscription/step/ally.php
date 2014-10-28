<?php
include_once ZEUS;

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	echo '<div class="component">';
		echo '<div class="head">';
			echo '<h1>Faction</h1>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<h4>Choisissez votre faction</h4>';
				echo '<p>Il vous faut choisir entre l\'une des septs factions disponibles.</p>';
				echo '<p>Chaque faction a ses forces et ses faiblesses. Certaine sont plus belliqueuse, certaine sont plus sage. De plus, les systèmes politiques changent en fonction des factions.</p>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	$allies = array(1, 2, 3, 4, 5, 6, 7);
	shuffle($allies);

	foreach ($allies as $ally) {
		echo '<div class="component inscription color' . $ally . '">';
			echo '<div class="head skin-1">';
				echo '<img class="color' . $ally . '" src="' . MEDIA . 'ally/big/color' . $ally . '.png" alt="" />';
				echo '<h2>' . ColorResource::getInfo($ally, 'officialName') . '</h2>';
				echo '<em>' . ColorResource::getInfo($ally, 'government') . '</em>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<h4>A propos</h4>';
					echo '<p>' . ColorResource::getInfo($ally, 'desc1') . '</p>';
					echo '<h4>Moeurs & autres</h4>';
					echo '<p>' . ColorResource::getInfo($ally, 'desc2') . '</p>';
					echo '<h4>Guerre</h4>';
					echo '<p>' . ColorResource::getInfo($ally, 'desc3') . '</p>';
					echo '<h4>Culture</h4>';
					echo '<p>' . ColorResource::getInfo($ally, 'desc4') . '</p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription color' . $ally . '">';
			echo '<div class="head"></div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<a href="' . APP_ROOT . 'inscription/step-2/ally-' . $ally . '" class="chooseLink">';
						echo '<strong>choisir cette faction</strong>';
						echo '<em>et passer à l\'étape suivante</em>';
					echo '</a>';

					echo '<blockquote>"' . ColorResource::getInfo($ally, 'devise') . '"</blockquote>';

						echo '<h4>Bonus & Malus de faction</h4>';
						$bonuses = ColorResource::getInfo($ally, 'bonus');
						foreach ($bonuses as $bonus) {
							echo '<div class="build-item" style="margin: 25px 0;">';
								echo '<div class="name">';
									echo '<img src="' . MEDIA . $bonus['path'] . '" alt="" />';
									echo '<strong>' . $bonus['title'] . '</strong>';
									echo '<em>' . $bonus['desc'] . '</em>';
								echo '</div>';
							echo '</div>';
						}
				echo '</div>';
			echo '</div>';
		echo '</div>';




	/*	echo '<div class="component inscription size2 color' . $ally . '">';
			echo '<div class="head skin-1">';
				echo '<img class="color' . $ally . '" src="' . MEDIA . 'ally/big/color' . $ally . '.png" alt="" />';
				echo '<h2>' . ColorResource::getInfo($ally, 'officialName') . '</h2>';
				echo '<em>' . ColorResource::getInfo($ally, 'government') . '</em>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<div class="left">';
						echo '<div class="text-box">';
							echo '<h3>A propos</h3>';
							echo '<p>' . ColorResource::getInfo($ally, 'desc1') . '</p>';
						echo '</div>';
						echo '<div class="text-box">';
							echo '<h3>Moeurs & autres</h3>';
							echo '<p>' . ColorResource::getInfo($ally, 'desc2') . '</p>';
						echo '</div>';
						echo '<div class="text-box">';
							echo '<h3>Guerre</h3>';
							echo '<p>' . ColorResource::getInfo($ally, 'desc3') . '</p>';
						echo '</div>';
						echo '<div class="text-box">';
							echo '<h3>Culture</h3>';
							echo '<p>' . ColorResource::getInfo($ally, 'desc4') . '</p>';
						echo '</div>';
					echo '</div>';
					echo '<div class="right">';
						echo '<a href="' . APP_ROOT . 'inscription/step-2/ally-' . $ally . '" class="chooseLink">';
							echo '<strong>choisir cette faction</strong>';
							echo '<em>et passer à l\'étape suivante</em>';
						echo '</a>';

						echo '<blockquote>"' . ColorResource::getInfo($ally, 'devise') . '"</blockquote>';

						echo '<h4>Bonus & Malus de faction</h4>';
						$bonuses = ColorResource::getInfo($ally, 'bonus');
						foreach ($bonuses as $bonus) {
							echo '<div class="build-item" style="margin: 25px 0;">';
								echo '<div class="name">';
									echo '<img src="' . MEDIA . $bonus['path'] . '" alt="" />';
									echo '<strong>' . $bonus['title'] . '</strong>';
									echo '<em>' . $bonus['desc'] . '</em>';
								echo '</div>';
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';*/
	}
echo '</div>';
?>