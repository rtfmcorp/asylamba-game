<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') OR $request->query->get('view') == 'university') ? 'active' : '';
		echo '<a href="' . $appRoot . 'technology/view-university" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'orbitalbase/university.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Université';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'technos') ? 'active' : '';
		echo '<a href="' . $appRoot . 'technology/view-technos" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'orbitalbase/technosphere.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Arbre technologique';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
