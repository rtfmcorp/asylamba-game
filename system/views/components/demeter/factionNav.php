<?php
# factioNav component
# in demeter package

# affichage du menu des faction

# require
	# int 		color_factionNav

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>' . ColorResource::getInfo($color_factionNav, 'popularName') . '</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'forum') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-forum" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Forum</strong>';
				echo '<em>Ecrivez l\'avenir de votre faction</em>';
			echo '</a>';

			$active = (CTR::$get->get('view') == 'player') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-player" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Registres</strong>';
				echo '<em>Accédez aux registres de votre faction</em>';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';