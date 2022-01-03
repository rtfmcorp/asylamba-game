<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') || $request->query->get('view') == 'message') ? 'active' : '';
		echo '<a href="' . $appRoot . 'admin/view-message" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'admin/main.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Messagerie';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'roadmap') ? 'active' : '';
		echo '<a href="' . $appRoot . 'admin/view-roadmap" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'admin/roadmap.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Roadmap';
			echo '</span>';
		echo '</a>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
	echo '</div>';
echo '</div>';
