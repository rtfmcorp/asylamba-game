<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'forum') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-forum" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Forum';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Forum';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>