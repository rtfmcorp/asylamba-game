<?php
echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Gouvernement</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'law') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-law" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Lois</strong>';
				echo '<em>Promulger de nouvelles lois</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'news') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-news" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Annonces</strong>';
				echo '<em>Gestion des annonces</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'message') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-message" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Messages groupés</strong>';
				echo '<em>Envoi de messages aux membres de la faction</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'description') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government/mode-description" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '<strong>Description</strong>';
				echo '<em>Edition de la description publique</em>';
			echo '</a>';

			if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
				$active = (CTR::$get->get('mode') == 'manage') ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'faction/view-government/mode-manage" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
					echo '<strong>Gouvernement</strong>';
					echo '<em>Gestion de votre gouvernement</em>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';