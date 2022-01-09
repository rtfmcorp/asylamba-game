<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') OR $request->query->get('view') == 'movement' OR $request->query->get('view') == 'main') ? 'active' : '';
		echo '<a href="' . $appRoot . 'fleet/view-main" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'fleet/general-quarter.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Centre des Opérations';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'overview') ? 'active' : '';
		echo '<a href="' . $appRoot . 'fleet/view-overview" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'fleet/armies.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Aperçu des armées';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'spyreport') ? 'active' : '';
		echo '<a href="' . $appRoot . 'fleet/view-spyreport" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'fleet/spy.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Rapports d\'espionnage';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'archive') ? 'active' : '';
		echo '<a href="' . $appRoot . 'fleet/view-archive" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'fleet/archive.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Archives Militaires';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'memorial') ? 'active' : '';
		echo '<a href="' . $appRoot . 'fleet/view-memorial" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . $mediaPath . 'fleet/memorial.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Mémorial';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
