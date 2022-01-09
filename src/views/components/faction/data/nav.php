<?php

use App\Classes\Worker\CTR;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$request = $this->getContainer()->get('app.request');

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Registres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!$request->query->has('mode') || $request->query->get('mode') == 'financial') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-financial" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'financial/taxout.png" alt="" />';
				echo '<strong>Finance</strong>';
				echo '<em>Richesse, imposition et donations</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'trade') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-trade" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Commerce</strong>';
				echo '<em>Routes commerciales et taxes sur le commerce</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'war') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-war" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'fleet/movement.png" alt="" />';
				echo '<strong>Guerre</strong>';
				echo '<em>Armées, mouvements de flottes et rapports de combat</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'tactical') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-tactical" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/data/tactical.png" alt="" />';
				echo '<strong>Tactique</strong>';
				echo '<em>Territoires et objectifs de victoire</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'diplomacy') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-diplomacy" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/data/diplomacy.png" alt="" />';
				echo '<strong>Diplomatie</strong>';
				echo '<em>Accords et traités</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'law') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-data/mode-law" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/data/law.png" alt="" />';
				echo '<strong>Lois</strong>';
				echo '<em>Table des Lois</em>';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
