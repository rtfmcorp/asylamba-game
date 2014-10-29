<?php
echo '<div id="map-subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>' . CTR::$data->get('playerInfo')->get('name') . '</h2>';
	echo '</div>';
	echo '<div class="body">';
		echo '<div class="black-box">';
			echo '<h2>' . $defaultBase->getName() . '</h2>';
			echo '<a class="goto-button hb rt moveTo" title="centrer sur la carte" href="#" data-x-position="' . $defaultBase->getXSystem() . '" data-y-position="' . $defaultBase->getYSystem() . '"></a>';
			echo '<p>';
				echo 'base orbitale<br />';
				echo 'secteur ' . $defaultBase->getSector() . '<br />';
				echo $defaultBase->getPoints() . ' points<br />';
				//echo '2 flottes de défenses';
			echo '</p>';
		echo '</div>';

		if (CTR::$data->get('playerBase')->get('ob')->size() >= 2) {
			echo '<a class="toggle-bases sh" data-target="base-bull" href="#">Changer de base</a>';
			echo '<div class="toogle-bases-content" id="base-bull">';
				for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
					echo '<a href="' . APP_ROOT . 'map/base-' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') . '">';
						echo '<em>Base orbitale</em>';
						echo '<strong>' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('name') . '</strong>';
					echo '</a>';
				}
			echo '</div>';
		}
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
?>