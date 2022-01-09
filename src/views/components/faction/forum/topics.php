<?php
# topics component
# in demeter.forum package

# affichage de la liste des topics

use App\Modules\Demeter\Resource\ForumResources;
use App\Modules\Zeus\Model\Player;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

# require
	# int 			forum_topics
	# [{topic}] 	topic_topics
	# bool 		 	isStandard_topics

echo '<div class="component report topic nav">';
	if ($isStandard_topics) {
		echo '<div class="head"></div>';
	} elseif ($idColum_topics == 1) {
		echo '<div class="head skin-1">';
			echo '<h1>Forum</h1>';
		echo '</div>';
	} else {
		echo '<div class="head"></div>';
	}
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (!$isStandard_topics) {
				echo '<a href="' . $appRoot . 'faction/view-forum/forum-' . $forum_topics . '" class="nav-element">';
					echo '<img src="' . $mediaPath . 'orbitalbase/situation.png" alt="" />';
					echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
					echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
				echo '</a>';
			}

			if ($archivedMode) {
				echo '<a class="more-button" href="' . $appRoot . 'faction/view-forum/forum-' . $forum_topics . '/">Revenir aux sujets</a>';
			}

			echo '<div class="set-item">';
				if (!$archivedMode) {
					echo '<a class="item" href="' . $appRoot . 'faction/view-forum/forum-' . $forum_topics . '/mode-create/sftr-2">';
						echo '<div class="left">';
							echo '<span>+</span>';
						echo '</div>';

						echo '<div class="center">';
							echo 'Créer un nouveau sujet';
						echo '</div>';
					echo '</a>';
				}
			
				if (count($topic_topics) > 0) {
					foreach ($topic_topics as $t) {
						if ($t->id == $request->query->get('topic')) {
							$isNew = '';
						} elseif ($t->lastView == NULL || strtotime($t->lastView) < strtotime($t->dLastMessage)) {
							$isNew = ' round-color' . $session->get('playerInfo')->get('color');
						} else {
							$isNew = '';
						}

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<span class="' . $isNew . '">' . $t->nbMessage . '</span>';
							echo '</div>';

							echo '<div class="center">';
								if ($t->isClosed) {
									echo '&#128274; ';
								} elseif ($t->isUp) {
									echo '&#9733; ';
								}
								echo $t->title;
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (($request->query->get('topic') === $t->id)  ? 'active' : NULL) . '" href="' . $appRoot . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/' . ($archivedMode ? 'mode-archived/' : NULL) . 'sftr-2"></a>';
							echo '</div>';
						echo '</div>';
					}
				}
			echo '</div>';

			if (count($topic_topics) == 0) {
				echo '<p>Aucun sujet n\'a encore été créé dans cette section du forum.</p>';
			}

			if ($isStandard_topics && !$archivedMode && in_array($session->get('playerInfo')->get('status'), [Player::CHIEF, Player::WARLORD, Player::TREASURER, Player::MINISTER])) {
				echo '<a class="more-button" href="' . $appRoot . 'faction/view-forum/forum-' . $forum_topics . '/mode-archived">Voir les messages archivés</a>';
			}
		echo '</div>';
		
	echo '</div>';
echo '</div>';
