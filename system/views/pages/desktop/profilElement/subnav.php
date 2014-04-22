<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (in_array(CTR::getPage(), array('profil'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'profil" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/general-quarter.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Profil';
			echo '</span>';
		echo '</a>';

		$active = (in_array(CTR::getPage(), array('diary'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'diary" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/movement.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Journal';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>