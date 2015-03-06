<?php
echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'message') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'admin/view-message" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'admin/main.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Messagerie';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'roadmap') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'admin/view-roadmap" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'admin/roadmap.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Roadmap';
			echo '</span>';
		echo '</a>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
	echo '</div>';
echo '</div>';
?>