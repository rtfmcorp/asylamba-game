<?php
# forum component
# in demeter.forum package

# affichage du menu des forums

# require

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Forum</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 1; $i <= ForumResources::size(); $i++) { 
				if (ForumResources::getInfo($i, 'id') < 10) {
					$active = ((!CTR::$get->exist('forum') AND $i == 1) OR CTR::$get->get('forum') == ForumResources::getInfo($i, 'id')) ? 'active' : '';
					echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . ForumResources::getInfo($i, 'id') . '" class="nav-element ' . $active . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
						echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
						echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
					echo '</a>';
				} elseif (ForumResources::getInfo($i, 'id') >= 10 && ForumResources::getInfo($i, 'id') < 20 && CTR::$data->get('playerInfo')->get('status') > 2) {
					$active = ((!CTR::$get->exist('forum') AND $i == 1) OR CTR::$get->get('forum') == ForumResources::getInfo($i, 'id')) ? 'active' : '';
					echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . ForumResources::getInfo($i, 'id') . '" class="nav-element ' . $active . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
						echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
						echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
					echo '</a>';
				} elseif (ForumResources::getInfo($i, 'id') >= 20 && CTR::$data->get('playerInfo')->get('status') == 6) {
					$active = ((!CTR::$get->exist('forum') AND $i == 1) OR CTR::$get->get('forum') == ForumResources::getInfo($i, 'id')) ? 'active' : '';
					echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . ForumResources::getInfo($i, 'id') . '" class="nav-element ' . $active . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
						echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
						echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';