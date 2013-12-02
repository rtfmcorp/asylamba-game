<?php
# topic component
# in demeter.forum package

# affichage d'un topic

# require
	# {topic}			topic_topic
	# [{message}]		message_topic

echo '<div class="component topic size2">';
	echo '<div class="head skin-2">';
		echo '<h2>' . $topic_topic->title . '</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
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

			echo '<div class="message write">';
				echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
				echo '<div class="content">';
					echo '<form action="' . APP_ROOT . 'action/a-writemessageforum/rtopic-' . $topic_topic->id . '" method="POST">';
						echo '<textarea name="content" placeholder="répondez"></textarea>';
						echo '<input type="submit" value="envoyer le message" />';
					echo '</form>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';