<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'university') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'technology/view-university" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/university.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Université';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'overview') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'technology/view-overview" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Roadmap';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'technos') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'technology/view-technos" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Bugtracker';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>