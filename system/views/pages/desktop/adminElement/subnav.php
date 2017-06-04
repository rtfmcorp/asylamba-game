<?php

use Asylamba\Classes\Worker\CTR;

$request = $this->getContainer()->get('app.request');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->exist('view') OR $request->query->get('view') == 'message') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'admin/view-message" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'admin/main.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Messagerie';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'roadmap') ? 'active' : '';
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