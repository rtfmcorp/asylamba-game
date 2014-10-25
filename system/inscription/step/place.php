<?php
include_once GAIA;
include_once ZEUS;

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	echo '<form action="' . APP_ROOT . 'inscription/step-4" method="post" >';
		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
				echo '<h1>Localisation</h1>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p class="info">Remplissez maintenant les informations de votre première base.</p>';
					echo '<p class="info">Vous pourrez changer ce nom plus tard.</p>';
					echo '<hr />';
					echo '<p><input type="text" name="base" id="base" maxlength="20" required placeholder="nom de votre planète" /></p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription size2 color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head"></div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					$sm = new SectorManager();
					$sm->load();
					$rate = 750 / GalaxyConfiguration::$galaxy['size'];

					echo '<div class="inscription-map">';
						echo '<input type="hidden" id="input-sector-id" name="sector" />';
						echo '<svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg">';
							for ($i = 0; $i < $sm->size(); $i++) {
								$s = $sm->get($i);
								if ($s->getRColor() != CTR::$data->get('inscription')->get('ally')) {
									echo '<polygon data-id="' . $s->getId() . '"';
										echo 'class="ally' . $s->getRColor() . ' ' . ($s->getRColor() == CTR::$data->get('inscription')->get('ally') ? 'enabled' : 'disabled') . '" ';
										echo 'points="' . GalaxyConfiguration::getSectorCoord($s->getId(), $rate, 0) . '" ';
									echo '/>';
								}
							}

							for ($i = 0; $i < $sm->size(); $i++) {
								$s = $sm->get($i);
								if ($s->getRColor() == CTR::$data->get('inscription')->get('ally')) {
									echo '<polygon data-id="' . $s->getId() . '"';
										echo 'class="ally' . $s->getRColor() . ' ' . ($s->getRColor() == CTR::$data->get('inscription')->get('ally') ? 'enabled' : 'disabled') . '" ';
										echo 'points="' . GalaxyConfiguration::getSectorCoord($s->getId(), $rate, 0) . '" ';
									echo '/>';
								}
							}
						echo '</svg>';
						echo '<div class="number">';
							for ($i = 0; $i < $sm->size(); $i++) {
								$s = $sm->get($i);
								echo '<span id="sector' . $s->getId() . '" class="ally' . ($s->getRColor() == CTR::$data->get('inscription')->get('ally') ? $s->getRColor() : 0) . '" style="top: ' . (GalaxyConfiguration::$sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . (GalaxyConfiguration::$sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
									echo $s->getId();
								echo '</span>';
							}
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p><input type="submit" id="nextStep" value="valider et commencer le jeu" /></p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
?>