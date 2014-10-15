<?php
# topics component
# in demeter.forum package

# affichage de la liste des topics

# require
	# int 			forum_topics
	# [{topic}] 	topic_topics

echo '<div class="component">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'orbitalbase/situation.png">';
		echo '<h2>' . ForumResources::getInfoForId($forum_topics, 'name') . '</h2>';
		echo '<em>' . ForumResources::getInfoForId($forum_topics, 'shortDescription') . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="set-item">';
				echo '<a class="item" href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/mode-create/sftr-2">';
					echo '<div class="left">';
						echo '<span>+</span>';
					echo '</div>';

					echo '<div class="center">';
						echo 'Créer un nouveau sujet';
					echo '</div>';
				echo '</a>';
			
				if (count($topic_topics) == 0) {
					echo '<p class="info">Aucun sujet n\'a encore été créé dans cette partie de forum.</p>';
				} else {
					foreach ($topic_topics as $t) {
						if ($t->id == CTR::$get->get('topic')) {
							$isNew = '';
						} elseif ($t->lastView == NULL || strtotime($t->lastView) < strtotime($t->dLastMessage)) {
							$isNew = ' round-color' . CTR::$data->get('playerInfo')->get('color');
						} else {
							$isNew = '';
						}

						/*echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/sftr-2" class="topic ' . $isNew . '">';
							echo '<strong>' . $t->title . '</strong>';
							echo '<span class="button hb lt" title="message">' . $t->nbMessage . '</span>';
						echo '</a>';*/

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<span class="' . $isNew . '">' . $t->nbMessage . '</span>';
							echo '</div>';

							echo '<div class="center">';
								echo $t->title;
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('topic', $t->id)  ? 'active' : NULL) . '" href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/sftr-2"></a>';
							echo '</div>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';