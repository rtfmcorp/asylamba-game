<?php


$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$request = $container->get('app.request');
$session = $container->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = ($request->query->has('faction')) ? 'active' : NULL;
		echo '<a href="' . $appRoot . 'embassy/faction-' . $session->get('playerInfo')->get('color') . '" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'rank/faction.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Ambassades</span>';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->has('player')) ? 'active' : NULL;
		echo '<a href="' . $appRoot . 'embassy/player-' . $session->get('playerId') . '" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'profil/diary.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Journal';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
