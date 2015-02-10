<?php
include_once ZEUS;
include_once DEMETER;

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
				echo '<p>Chaque faction a ses forces et ses faiblesses. Certaines sont plus belliqueuses, certaines sont plus sages. De plus, le système politique change en fonction de la faction.</p>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	$_CLM = ASM::$clm->getCurrentSession();
	ASM::$clm->newSession(FALSE);
	ASM::$clm->load(array(), array('activePlayers', 'ASC'));

	for ($i = 1; $i < ASM::$clm->size(); $i++) {
		$ally = ASM::$clm->get($i)->id;

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
					if (!ASM::$clm->get($i)->isClosed) {
						echo '<a href="' . APP_ROOT . 'inscription/step-2/ally-' . $ally . '" class="chooseLink">';
							echo '<strong>choisir cette faction</strong>';
							if ($i == 0) {
								echo '<em>recommandée pour les joueurs débutants</em>';
							} else {
								echo '<em>et passer à l\'étape suivante</em>';
							}
						echo '</a>';
					} else {
						echo '<span class="chooseLink">';
							echo '<strong>Cette faction est actuellement fermée</strong>';
							echo '<em>De manière à équilibrer le jeu</em>';
						echo '</span>';
					}
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
	}
	ASM::$clm->changeSession($_CLM);
echo '</div>';
?>