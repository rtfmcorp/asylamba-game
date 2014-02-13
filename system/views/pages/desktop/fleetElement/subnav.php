<?php
echo '<div id="subnav" class="base">';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-main" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/general-quarter.png" alt="" />';
			echo '</span>';
			echo '<span class="content">';
				echo '<span class="label">Quartier Général</span>';
				echo '<span class="value">Vue d\'ensemble de toute les opérations militaires</span>';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'movement') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-movement" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/movement.png" alt="" />';
			echo '</span>';
			echo '<span class="content">';
				echo '<span class="label">Centre des Opérations</span>';
				echo '<span class="value">Gestion des flottes, des équipages et des convois</span>';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'archive') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-archive" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/archive.png" alt="" />';
			echo '</span>';
			echo '<span class="content">';
				echo '<span class="label">Archives Militaires</span>';
				echo '<span class="value">Centre de gestions des archives militaires</span>';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'memorial') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'fleet/view-memorial" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="" />';
			echo '</span>';
			echo '<span class="content">';
				echo '<span class="label">Mémorial</span>';
				echo '<span class="value">A la mémoire de nos officiers morts au combat</span>';
			echo '</span>';
		echo '</a>';
	echo '</div>';
echo '</div>';
?>