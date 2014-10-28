<?php
# forum component
# in demeter.forum package

# affichage du menu des forums

# require

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Option de modération</h4>';

		#	echo '<a class="more-button" href="' . APP_ROOT . 'action/a-uptopicforum/id-' . $topic_topic->id . '">Epingler</a>';
			echo '<a class="more-button" href="' . APP_ROOT . 'action/a-closetopicforum/id-' . $topic_topic->id . '">';
				echo $topic_topic->isClosed ? 'Réouvrire' : 'Fermer';
			echo '</a>';

			echo '<a class="more-button" href="' . APP_ROOT . 'action/a-archivetopicforum/id-' . $topic_topic->id . '">';
				echo $topic_topic->isArchived ? 'Désarchiver' : 'Archiver';
			echo '</a>';

			echo '<form action="' . APP_ROOT . 'action/a-movetopicforum/id-' . $topic_topic->id . '" method="post" class="choose-government">';
				echo '<select name="rforum">';
					echo '<option value="-1">Choisissez une catégorie</option>';
					for ($i = 1; $i <= ForumResources::size(); $i++) { 
						if (ForumResources::getInfo($i, 'id') < 10) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						} elseif (ForumResources::getInfo($i, 'id') >= 10 && ForumResources::getInfo($i, 'id') < 20 && CTR::$data->get('playerInfo')->get('status') > 2) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						} elseif (ForumResources::getInfo($i, 'id') >= 20 && ForumResources::getInfo($i, 'id') < 30 && CTR::$data->get('playerInfo')->get('status') == 6) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						}
					}
				echo '</select>';
				echo '<button type="submit">Déplacer</button>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';