<?php
# topics component
# in demeter.forum package

# affichage de la liste des topics

# require
	# int 			forum_topics
	# [{topic}] 	topic_topics

echo '<div class="component topic">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="test">';
		echo '<h2>' . ForumResources::getInfo($forum_topics, 'name') . '</h2>';
		echo '<em>' . ForumResources::getInfo($forum_topics, 'shortDescription') . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/mode-create/sftr-2">créer un nouveau sujet</a></span>';
			echo '</div>';

			if (count($topic_topics) == 0) {
				echo '<p class="info">Aucun sujet n\'a encore été créé dans cette partie de forum.</p>';
			} else {
				foreach ($topic_topics as $t) {
					if ($t->id == CTR::$get->get('topic')) {
						$isNew = '';
					} elseif ($t->lastView == NULL || strtotime($t->lastView) < strtotime($t->dLastMessage)) {
						$isNew = ' new';
					} else {
						$isNew = '';
					}

					echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/sftr-2" class="topic ' . $isNew . '">';
						echo '<strong>' . $t->title . '</strong>';
						echo '<span class="button hb lt" title="message">' . $t->nbMessage . '</span>';
					echo '</a>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';