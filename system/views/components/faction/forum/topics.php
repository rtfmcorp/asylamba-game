<?php
# topics component
# in demeter.forum package

# affichage de la liste des topics

# require
	# int 			forum_topics
	# [{topic}] 	topic_topics
	# bool 		 	isStandard_topics

echo '<div class="component report topic">';
	if ($isStandard_topics) {
		echo '<div class="head skin-4">';
			echo '<img class="main" alt="ressource" src="' . MEDIA . 'orbitalbase/situation.png">';
			echo '<h2>' . ForumResources::getInfoForId($forum_topics, 'name') . '</h2>';
			echo '<em>' . ForumResources::getInfoForId($forum_topics, 'shortDescription') . '</em>';
		echo '</div>';
	} else {
		echo '<div class="head"></div>';
	}
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/mode-create/sftr-2">créer un nouveau sujet</a></span>';
			echo '</div>';

			if (count($topic_topics) == 0) {
				echo '<p class="info">Aucun sujet n\'a encore été créé dans cette partie de forum.</p>';
			} else {
				echo '<div class="set-item">';
					foreach ($topic_topics as $t) {
						if ($t->id == CTR::$get->get('topic')) {
							$isNew = '';
						} elseif ($t->lastView == NULL || strtotime($t->lastView) < strtotime($t->dLastMessage)) {
							$isNew = ' new';
						} else {
							$isNew = '';
						}

						echo '<div class="item ' . $isNew . '">';
							echo '<div class="left">';
								echo '<span>' . $t->nbMessage . '</span>';
							echo '</div>';

							echo '<div class="center">';
								echo $t->title;
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('topic', $t->id)  ? 'active' : NULL) . '" href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/sftr-2"></a>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';