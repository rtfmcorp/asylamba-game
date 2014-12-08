<?php
# newMessage componant
# in hermes package

# formulaire de création de message, autocomplétion des joueurs

# require
	# NULL

$S_MSM_SCOPE = ASM::$msm->getCurrentSession();
ASM::$msm->changeSession($C_MSM2);

echo '<div class="component topic size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (ASM::$msm->get(0)->getRPlayerWriter() > 0) {
				echo '<div class="message write">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('pseudo') . '" class="avatar" />';
					echo '<div class="content">';
						echo '<form action="' . Format::actionBuilder('writemessage', ['thread' => ASM::$msm->get(0)->getThread()]) . '" method="POST">';
							echo '<div class="wysiwyg" data-id="new-message-wysiwyg">';
								$parser = new Parser();
								echo $parser->getToolbar();
								
								echo '<textarea name="message" id="new-message-wysiwyg" placeholder="Votre message"></textarea>';
							echo '</div>';
							echo '<button>Répondre</button>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
			}

			for ($i = 0; $i < ASM::$msm->size(); $i++) {
				$t = ASM::$msm->get($i);

				if ($t->getRPlayerReader() == CTR::$data->get('playerId')) {
					$t->setReaded(TRUE);
				}

				echo '<div class="message">';
					echo '<img src="' . MEDIA . 'avatar/medium/' . $t->getWriterAvatar() . '.png" alt="' . $t->getWriterName() . '" class="avatar" />';
					echo '<div class="content">';
						echo '<p class="text">';
							echo '≡ ' . $t->getWriterName() . '<br /><br />';
							echo $t->getContent();
						echo '</p>';
						echo '<p class="footer">';
							echo '— ' . Chronos::transform($t->getDSending());
						echo '</p>';
					echo '</div>';
				echo '</div>';
			}

			if (ASM::$msm->size() == MSM_STEPMESSAGE) {
				echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-moremessage/thread-' . ASM::$msm->get(0)->getThread() . '/page-2">';
					echo 'afficher plus de messages';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$msm->changeSession($S_MSM_SCOPE);