<?php
echo '<div id="subnav">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'overview') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-overview" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/overview.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Vue générale';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'forum') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-forum" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/forum.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Forum';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'government') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-government" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/government.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Gouvernement';
			echo '</span>';
		echo '</a>';

		if (in_array($faction->electionStatement, array(Color::CAMPAIGN, Color::ELECTION))) {
			$active = (CTR::$get->get('view') == 'election') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-election" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'faction/nav/election.png" alt="" />';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>Election';
				echo '</span>';
			echo '</a>';
		}

		$active = (CTR::$get->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/register.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Registres';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>