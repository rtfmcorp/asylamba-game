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
				echo '<p class="info">Il vous faut choisir une des trois factions en activités.</p>';
				echo '<p class="info">Le jeu final disposera de sept factions, cependant pour des raisons de jeu, nous préférons ouvrir trois grosses factions ';
				echo 'que sept petites.</p>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

#	$allies = array(1, 2, 3, 4, 5, 6, 7);
	$allies = array(2, 3);
	shuffle($allies);

	foreach ($allies as $ally) {
		echo '<div class="component inscription size2 color' . $ally . '">';
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
						echo '<blockquote>' . ColorResource::getInfo($ally, 'devise') . '</blockquote>';
						echo '<div class="text-box">';
							echo '<h3>Bonus & Malus de faction</h3>';
							echo '<p>pas encore implémenté</p>';
						echo '</div>';
						/*echo '<div class="build-item">';
							echo '<div class="name">';
								echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
								echo '<strong>Générateur</strong>';
								echo '<em>Générateur niv. 5</em>';
							echo '</div>';
						echo '</div>';
						echo '<p>Historiquement, l\'empire Akhénien est la faction la plus ancienne de la Galaxie de l\'Oeil. Cette ancienneté vous acquiert un générateur de niveau plus élevé dès le début du jeu.</p>';
						
						echo '<div class="build-item">';
							echo '<div class="name">';
								echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
								echo '<strong>Vaisseaux-mère</strong>';
								echo '<em>-3 % de coût</em>';
							echo '</div>';
						echo '</div>';
						echo '<p>Encouragé par une politique d\'expansion galactique, les Vaisseaux-mère sont moins chers à produire.</p>';
					
						echo '<div class="build-item">';
							echo '<div class="name">';
								echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
								echo '<strong>Vaisseaux</strong>';
								echo '<em>+ 5% de défense</em>';
							echo '</div>';
						echo '</div>';
						echo '<p>Le savoir-faire militaire athénien n\'est plus à remettre en cause. Vos vaisseaux ont une meilleure défense.</p>';*/
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
echo '</div>';
?>