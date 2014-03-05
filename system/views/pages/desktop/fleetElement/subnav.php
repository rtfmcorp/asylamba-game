<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-main" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/general-quarter.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Quartier Général';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'movement') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-movement" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/movement.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Centre des Opérations';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'archive') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-archive" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/archive.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Archives Militaires';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'memorial') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-memorial" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Mémorial';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>