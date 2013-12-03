<?php
# forum component
# in demeter.forum package

# affichage du menu des forums

# require

echo '<div class="component nav">';
	echo '<div class="head skin-2">';
		echo '<h2>Forum</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 1; $i <= ForumResources::size(); $i++) { 
				$active = ((!CTR::$get->exist('forum') AND $i == 1) OR CTR::$get->get('forum') == $i) ? 'active' : '';
				echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . ForumResources::getInfo($i, 'id') . '" class="nav-element ' . $active . '">';
					echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
					echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
					echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';