<?php
echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Registres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'financial') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-data/mode-financial" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Finance</strong>';
				echo '<em>richesse, imposition et donations</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'trade') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-data/mode-trade" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Commerce</strong>';
				echo '<em>routes commerciales et taxes sur le commerce</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'war') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-data/mode-war" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Guerre</strong>';
				echo '<em>Territoire, armée et mouvement de flottes</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'law') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-data/mode-law" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Lois</strong>';
				echo '<em>Table des Lois</em>';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';