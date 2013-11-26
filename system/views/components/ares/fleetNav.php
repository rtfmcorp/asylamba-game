<?php
# fleetNav component
# in ares package

# affichage du menu de la page fleuve

# require
	# NULL

echo '<div class="component nav">';
	echo '<div class="head">';
		echo '<h1>Amirauté</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'fleet/view-main" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'fleet/general-quarter.png" alt="" />';
				echo '<strong>Quartier Général</strong>';
				echo '<em>Vue d\'ensemble de toute les opérations militaires</em>';
			echo '</a>';

			$active = (CTR::$get->get('view') == 'movement') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'fleet/view-movement" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'fleet/movement.png" alt="" />';
				echo '<strong>Centre des Opérations</strong>';
				echo '<em>Gestion des flottes, des équipages et des convois</em>';
			echo '</a>';

			/*$active = (CTR::$get->get('view') == 'commanders') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'fleet/view-commanders" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'fleet/commanders.png" alt="" />';
				echo '<strong>Mess des Officiers</strong>';
				echo '<em>Formation et promotion des officiers et sous-officiers</em>';
			echo '</a>';*/

			$active = (CTR::$get->get('view') == 'archive') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'fleet/view-archive" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'fleet/archive.png" alt="" />';
				echo '<strong>Archives Militaires</strong>';
				echo '<em>Centre de gestions des archives militaires</em>';
			echo '</a>';

			$active = (CTR::$get->get('view') == 'memorial') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'fleet/view-memorial" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="" />';
				echo '<strong>Mémorial</strong>';
				echo '<em>A la mémoire de nos officiers morts au combat</em>';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';