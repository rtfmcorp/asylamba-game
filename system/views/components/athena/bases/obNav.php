<?php
# obNav component
# in athena.bases package

# affichage du menu d'une abse orbitale

# require
	# {orbitalBase}		ob_obNav

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($ob_obNav->getPlanetPopulation()) . '.png" alt="' . $ob_obNav->getName() . '" />';
		echo '<h2>' . $ob_obNav->getName() . '</h2>';
		echo '<em>' . $ob_obNav->getPoints() . ' points</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-main" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Vue de situation</strong>';
				echo '<em>Affiche un aperçu rapide de votre base</em>';
			echo '</a>';

			echo '<hr />';

			if ($ob_obNav->getLevelGenerator() > 0) {
				$active = (CTR::$get->get('view') == 'generator') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-generator" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(0, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelGenerator() . '</em>';
				echo '</a>';
			}
			if ($ob_obNav->getLevelRefinery() > 0) {
				$active = (CTR::$get->get('view') == 'refinery') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-refinery" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/refinery.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(1, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelRefinery() . '</em>';
				echo '</a>';
			}
			if ($ob_obNav->getLevelTechnosphere() > 0) {
				$active = (CTR::$get->get('view') == 'technosphere') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-technosphere" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(5, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelTechnosphere() . '</em>';
				echo '</a>';
			}
			if ($ob_obNav->getLevelDock1() > 0) {
				$active = (CTR::$get->get('view') == 'dock1') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-dock1" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(2, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelDock1() . '</em>';
				echo '</a>';
			}
			if ($ob_obNav->getLevelDock2() > 0) {
				$active = (CTR::$get->get('view') == 'dock2') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-dock2" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(3, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelDock2() . '</em>';
				echo '</a>';
			}
			if ($ob_obNav->getLevelCommercialPlateforme() > 0) {
				$active = (CTR::$get->get('view') == 'commercialplateforme') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-commercialplateforme" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
					echo '<strong>' . OrbitalBaseResource::getBuildingInfo(6, 'frenchName') . '</strong>';
					echo '<em>niveau ' . $ob_obNav->getLevelCommercialPlateforme() . '</em>';
				echo '</a>';
			}

			echo '<hr />';

			$active = (CTR::$get->get('view') == 'university') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-university" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/university.png" alt="" />';
				echo '<strong>Université</strong>';
				echo '<em>recherche et développement</em>';
			echo '</a>';
			$active = (CTR::$get->get('view') == 'school') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-school" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="" />';
				echo '<strong>Ecole de Commandement</strong>';
				echo '<em>formation des officiers</em>';
			echo '</a>';
			$active = (CTR::$get->get('view') == 'antispy') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obNav->getId() . '/view-antispy" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/antispy.png" alt="" />';
				echo '<strong>Renseignement</strong>';
				echo '<em>radar</em>';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';