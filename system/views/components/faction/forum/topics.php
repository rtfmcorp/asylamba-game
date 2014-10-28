<?php
# topics component
# in demeter.forum package

# affichage de la liste des topics

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
				echo '<a href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '" class="nav-element">';
					echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
					echo '<strong>' . ForumResources::getInfo($i, 'name') . '</strong>';
					echo '<em>' . ForumResources::getInfo($i, 'shortDescription') . '</em>';
				echo '</a>';
			}

			echo '<div class="set-item">';
				echo '<a class="item" href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/mode-create/sftr-2">';
					echo '<div class="left">';
						echo '<span>+</span>';
					echo '</div>';

					echo '<div class="center">';
						echo 'Créer un nouveau sujet';
					echo '</div>';
				echo '</a>';
			
				if (count($topic_topics) > 0) {
					foreach ($topic_topics as $t) {
						if ($t->id == CTR::$get->get('topic')) {
							$isNew = '';
						} elseif ($t->lastView == NULL || strtotime($t->lastView) < strtotime($t->dLastMessage)) {
							$isNew = ' round-color' . CTR::$data->get('playerInfo')->get('color');
						} else {
							$isNew = '';
						}

						echo '<div class="item ' . $isNew . '">';
							echo '<div class="left">';
								echo '<span class="' . $isNew . '">' . $t->nbMessage . '</span>';
							echo '</div>';

							echo '<div class="center">';
								if ($t->isClosed) {
									echo '[Fermé] ';
								}
								echo $t->title;
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('topic', $t->id)  ? 'active' : NULL) . '" href="' . APP_ROOT . 'faction/view-forum/forum-' . $forum_topics . '/topic-' . $t->id . '/sftr-2"></a>';
							echo '</div>';
						echo '</div>';
					}
					echo '</div>';
				} else {
					echo '</div>';
					echo '<p>Aucun sujet n\'a encore été créé dans cette section du forum.</p>';
				}
		echo '</div>';
	echo '</div>';
echo '</div>';