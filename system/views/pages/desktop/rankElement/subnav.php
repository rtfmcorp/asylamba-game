<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'rank/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'rank/player.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des joueurs</span>';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'faction') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'rank/view-faction" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'rank/faction.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des factions</span>';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>