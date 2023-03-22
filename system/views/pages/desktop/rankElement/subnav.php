<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') OR $request->query->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . $appRoot . 'rank/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'rank/player.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des joueurs</span>';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'faction') ? 'active' : '';
		echo '<a href="' . $appRoot . 'rank/view-faction" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'rank/faction.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Classement des factions</span>';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
