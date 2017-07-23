<?php
# topic component
# in demeter.forum package

# affichage d'un topic

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$session = $this->getContainer()->get('session_wrapper');
$sessionToken = $session->get('token');
# require
	# {topic}				topic_topic
	# [{message}]			message_topic
	# *bool					election_topic

echo '<div class="component topic size2">';
	echo '<div class="head skin-2">';
		echo isset($election_topic) AND $election_topic
			? NULL
			: '<h2>' . $topic_topic->title . '</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($topic_topic->isClosed) {
				echo '<div class="message write">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . $session->get('playerInfo')->get('avatar') . '.png" alt="' . $session->get('playerInfo')->get('pseudo') . '" class="avatar" />';
					echo '<div class="content">';
						echo '<form action="#" method="POST">';
							echo '<textarea name="content" placeholder="Ce sujet est fermé" disabled></textarea>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<div class="message write">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . $session->get('playerInfo')->get('avatar') . '.png" alt="' . $session->get('playerInfo')->get('pseudo') . '" class="avatar" />';
					echo '<div class="content">';
						echo '<form action="' . Format::actionBuilder('writemessageforum', $sessionToken, ['rtopic' => $topic_topic->id]) . '" method="POST">';
							echo '<div class="wysiwyg" data-id="new-topic-wysiwyg">';
								$parser = $this->getContainer()->get('parser');
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

				$canEdit = ($session->get('playerId') == $m->rPlayer || ($session->get('playerInfo')->get('status') > 2 && $topic_topic->rForum != 20));

				echo '<div class="message write">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $m->rPlayer . '"><img src="' . MEDIA . 'avatar/medium/' . $m->playerAvatar . '.png" alt="' . $m->playerName . '" class="avatar" /></a>';
					echo '<div class="content">';
						echo '<p class="text">';
							echo '≡ ' . $status . ' ' . $m->playerName . '<br /><br />';
							echo $m->pContent;
						echo '</p>';

						if ($canEdit) {
							echo '<form style="display:none;" action="' . Format::actionBuilder('editmessageforum', $sessionToken, ['id' => $m->id]) . '" id="edit-m-' . $m->id . '" method="post">';
								echo '<div class="wysiwyg" data-id="edit-wysiwyg-m-' . $m->id . '">';
									$parser = $this->getContainer()->get('parser');
									echo $parser->getToolbar();
									
									echo '<textarea name="content" id="edit-wysiwyg-m-' . $m->id . '" placeholder="Répondez">' . $m->oContent . '</textarea>';
								echo '</div>';

								echo '<button>Envoyer le message</button>';
							echo '</form>';
						}

						echo '<p class="footer">';
							echo '— ' . Chronos::transform($m->dCreation) . ($canEdit ? '&#8195;|&#8195;<a href="#" class="sh" data-target="edit-m-' . $m->id . '">Editer</a>' : NULL);
						echo '</p>';
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';