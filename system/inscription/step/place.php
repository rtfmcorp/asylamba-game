<?php

$container = $this->getContainer();
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$appRoot = $container->getParameter('app_root');
$componentPath = $container->getParameter('component');

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';

	echo '<form action="' . $appRoot . 'inscription/step-4" method="post" >';
		include $componentPath . 'invisible.php';
		echo '<div class="component inscription color' . $session->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
				echo '<h1>Localisation</h1>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<h4>Choisissez le nom de votre première planète</h4>';
					echo '<p><input type="text" name="base" id="base" maxlength="20" required placeholder="nom de votre planète" /></p>';
					echo '<p>Vous pourrez changer ce nom plus tard.</p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription size2 color' . $session->get('inscription')->get('ally') . '">';
			echo '<div class="head skin-5">';
				echo '<h2>Choisissez l\'emplacement dans la galaxie</h2>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';

					$galaxyConfiguration = $this->getContainer()->get(\Asylamba\Modules\Gaia\Galaxy\GalaxyConfiguration::class);
					$sectors = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\SectorManager::class)->getAll();
					$rate = 750 / $galaxyConfiguration->galaxy['size'];

					echo '<div class="tactical-map reactive">';
						echo '<input type="hidden" id="input-sector-id" name="sector" />';
						echo '<svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg" style="width: 580px; height: 580px;">';
							foreach ($sectors as $sector) {
								echo '<polygon data-id="' . $sector->getId() . '"';
									echo 'class="ally' . $sector->getRColor() . ' ' . ($sector->getRColor() == $session->get('inscription')->get('ally') ? 'enabled' : 'disabled') . '" ';
									echo 'points="' . $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) . '" ';
								echo '/>';
							}

						echo '</svg>';
						echo '<div class="number">';
							$nbSectors = count($sectors);
							for ($i = 0; $i < $nbSectors; ++$i) {
								$sector = $sectors[$i];
								echo '<span id="sector' . $sector->getId() . '" class="ally' . ($sector->getRColor() == $session->get('inscription')->get('ally') ? $sector->getRColor() : 0) . '" style="top: ' . ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
									echo $sector->getId();
								echo '</span>';
							}
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription color' . $session->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<button type="submit" class="chooseLink">';
						echo '<strong>Choisir ce secteur</strong>';
						echo '<em>et commencer le jeu</em>';
					echo '</button>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
