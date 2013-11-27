<?php
# adminNav component
# in apollon.admin package

# affichage du menu de l'admin

echo '<div class="component nav">';
	echo '<div class="head">';
		echo '<h1>Administration</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'admin/view-main" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Administration</strong>';
				echo '<em>Page générale de gestion de l\'admin</em>';
			echo '</a>';

			echo '<hr />';

			$active = (CTR::$get->get('view') == 'message') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'admin/view-message" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Messagerie</strong>';
				echo '<em>Envoie de mail groupés</em>';
			echo '</a>';

			$active = (CTR::$get->get('view') == 'roadmap') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'admin/view-roadmap" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Roadmap</strong>';
				echo '<em>Affichage des améliorations</em>';
			echo '</a>';
			
			$active = (CTR::$get->get('view') == 'bugtracker') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'admin/view-bugtracker" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
				echo '<strong>Bugtracker</strong>';
				echo '<em>Gestion du trackeur de bug et des améliorations</em>';
			echo '</a>';

		echo '</div>';
	echo '</div>';
echo '</div>';