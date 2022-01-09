<?php
# forum component
# in demeter.forum package

# affichage du menu des forums

use App\Modules\Demeter\Resource\ForumResources;
use App\Modules\Zeus\Model\Player;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
# require

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Forum</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="' . $appRoot .'faction/view-forum" class="more-button">Revenir aux forums</a>';

			for ($i = 1; $i <= ForumResources::size(); $i++) {
				if ((ForumResources::getInfo($i, 'id') < 10) || (ForumResources::getInfo($i, 'id') >= 10 && ForumResources::getInfo($i, 'id') < 20 && $session->get('playerInfo')->get('status') > 2) || (ForumResources::getInfo($i, 'id') >= 20 && ForumResources::getInfo($i, 'id') < 30 && $session->get('playerInfo')->get('status') == Player::CHIEF)) {
					$active = ((!$request->query->has('forum') AND $i == 1) OR $request->query->get('forum') == ForumResources::getInfo($i, 'id')) ? 'active' : '';
					echo '<a href="' . $appRoot . 'faction/view-forum/forum-' . ForumResources::getInfo($i, 'id') . '" class="nav-element ' . $active . '">';
						echo '<img src="' . $mediaPath . 'orbitalbase/situation.png" alt="" />';
						echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
						echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
