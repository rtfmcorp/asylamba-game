<?php
echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Ambassades</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 1; $i <= 7; $i++) { 
				$active = $faction->id == $i ? 'active' : NULL;

				echo '<a href="' . APP_ROOT . 'embassy/faction-' . $i . '" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'avatar/small/color-' . $i . '.png" alt="" />';
					echo '<strong>' . ColorResource::getInfo($i, 'officialName') . '</strong>';
					echo '<em>' . ColorResource::getInfo($i, 'government') . '</em>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>