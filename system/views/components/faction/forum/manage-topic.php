<?php
# forum component
# in demeter.forum package
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ForumResources;

# affichage du menu des forums
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');
# require

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Option de modération</h4>';

			echo '<a class="more-button" href="' . Format::actionBuilder('uptopicforum', $sessionToken, ['id' => $topic_topic->id]) . '">';
				echo $topic_topic->isUp ? 'Enlever la mention importante' : 'Marquer comme important';
			echo '</a>';
			echo '<a class="more-button" href="' . Format::actionBuilder('closetopicforum', $sessionToken, ['id' => $topic_topic->id]) . '">';
				echo $topic_topic->isClosed ? 'Réouvrire' : 'Fermer';
			echo '</a>';

			echo '<a class="more-button" href="' . Format::actionBuilder('archivetopicforum', $sessionToken, ['id' => $topic_topic->id]) . '">';
				echo $topic_topic->isArchived ? 'Désarchiver' : 'Archiver';
			echo '</a>';

			echo '<form action="' . Format::actionBuilder('movetopicforum', $sessionToken, ['id' => $topic_topic->id]) . '" method="post" class="choose-government">';
				echo '<select name="rforum">';
					echo '<option value="-1">Choisissez une catégorie</option>';
					for ($i = 1; $i <= ForumResources::size(); $i++) { 
						if (ForumResources::getInfo($i, 'id') < 10) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						} elseif (ForumResources::getInfo($i, 'id') >= 10 && ForumResources::getInfo($i, 'id') < 20 && $session->get('playerInfo')->get('status') > 2) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						} elseif (ForumResources::getInfo($i, 'id') >= 20 && ForumResources::getInfo($i, 'id') < 30 && $session->get('playerInfo')->get('status') == 6) {
							echo '<option value="' . ForumResources::getInfo($i, 'id') . '">' . ForumResources::getInfo($i, 'name') . '</option>';
						}
					}
				echo '</select>';
				echo '<button type="submit">Déplacer</button>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
