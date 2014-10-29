<?php
# topic component
# in demeter.forum package

# affichage d'un topic

# require
	# {topic}				topic_topic
	# [{message}]			message_topic
	# *bool					election_topic

echo '<div class="component topic size2">';
	echo '<div class="head skin-2">';
		if (isset($election_topic) AND $election_topic) {
			# pass
		} else {
			echo '<h2>' . $topic_topic->title . '</h2>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($topic_topic->isClosed) {
				echo '<div class="message write">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
					echo '<div class="content">';
						echo '<form action="#" method="POST">';
							echo '<textarea name="content" placeholder="Ce sujet est fermé" disabled></textarea>';
							echo '<button disabled>Envoyer le message</button>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<div class="message write">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
					echo '<div class="content">';
						echo '<form action="' . APP_ROOT . 'action/a-writemessageforum/rtopic-' . $topic_topic->id . '" method="POST">';
							echo '<div class="wysiwyg" data-id="new-topic-wysiwyg">';
								$parser = new Parser();
								echo $parser->getToolbar();
								
								echo '<textarea name="content" id="new-topic-wysiwyg" placeholder="Répondez"></textarea>';
							echo '</div>';

							echo '<button>Envoyer le message</button>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
			}

			foreach ($message_topic as $m) {
				if ($m->playerColor > 0) {
					$status = ColorResource::getInfo($m->playerColor, 'status');
					$status = $status[$m->playerStatus - 1];
				} else {
					$status = 'Rebelle';
				}
				echo '<div class="message">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $m->rPlayer . '"><img src="' . MEDIA . 'avatar/medium/' . $m->playerAvatar . '.png" alt="' . $m->playerName . '" class="avatar" /></a>';
					echo '<div class="content">';
						echo '<p class="text">';
							echo '≡ ' . $status . ' ' . $m->playerName . '<br /><br />';
							echo $m->pContent;
						echo '</p>';
						echo '<p class="footer">';
							echo '— ' . Chronos::transform($m->dCreation);
						echo '</p>';
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';