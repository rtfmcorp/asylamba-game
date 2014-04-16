<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'bases/view-main" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Vue de situation</span>';
			echo '</span>';
		echo '</a>';

		if ($base->getLevelGenerator() > 0) {
			$active = (CTR::$get->get('view') == 'generator') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-generator" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
					echo '<span class="number">' . $base->getLevelGenerator() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(0, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		if ($base->getLevelRefinery() > 0) {
			$active = (CTR::$get->get('view') == 'refinery') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-refinery" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/refinery.png" alt="" />';
					echo '<span class="number">' . $base->getLevelRefinery() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(1, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		if ($base->getLevelTechnosphere() > 0) {
			$active = (CTR::$get->get('view') == 'technosphere') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-technosphere" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
					echo '<span class="number">' . $base->getLevelTechnosphere() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(5, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		if ($base->getLevelDock1() > 0) {
			$active = (CTR::$get->get('view') == 'dock1') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-dock1" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
					echo '<span class="number">' . $base->getLevelDock1() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(2, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		if ($base->getLevelDock2() > 0) {
			$active = (CTR::$get->get('view') == 'dock2') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-dock2" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" />';
					echo '<span class="number">' . $base->getLevelDock2() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(3, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		if ($base->getLevelCommercialPlateforme() > 0) {
			$active = (CTR::$get->get('view') == 'commercialplateforme') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
					echo '<span class="number">' . $base->getLevelCommercialPlateforme() . '</span>';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>' . OrbitalBaseResource::getBuildingInfo(6, 'frenchName') . '</span>';
				echo '</span>';
			echo '</a>';
		}

		$active = (CTR::$get->get('view') == 'school') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'bases/view-school" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Ecole de Commandement</span>';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>