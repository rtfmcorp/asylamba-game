<?php
echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Ambassades</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($factions as $i) {
				$active = $faction->id == $i ? 'active' : NULL;

				echo '<a href="' . APP_ROOT . 'embassy/faction-' . $i . '" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'avatar/small/color-' . $i . '.png" alt="" />';
					echo '<strong>' . ColorResource::getInfo($i, 'officialName') . '</strong>';
					echo '<em>Ambassade des ' . ucfirst(ColorResource::getInfo($i, 'demonym')) . '</em>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';