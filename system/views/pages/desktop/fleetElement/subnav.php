<?php

use Asylamba\Classes\Worker\CTR;

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'movement' OR CTR::$get->get('view') == 'main') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-main" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/general-quarter.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Centre des Opérations';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'overview') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-overview" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/armies.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Aperçu des armées';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'spyreport') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-spyreport" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/spy.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Rapports d\'espionnage';
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
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
