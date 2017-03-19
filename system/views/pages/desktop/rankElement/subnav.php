<?php

$request = $this->getContainer()->get('app.request');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') OR $request->query->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'rank/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'rank/player.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des joueurs</span>';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'faction') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'rank/view-faction" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'rank/faction.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des factions</span>';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
