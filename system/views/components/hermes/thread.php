<?php
# thread componant
# in hermes package

# affiche une conversion

# require
	# (int)			threadId_thread
	# (string)		lastMessage_thread
	# [{message}]	messages_thread

if ($messages_thread[0]->getRPlayerWriter() == CTR::$data->get('playerId')) {
	$coId = $messages_thread[0]->getRPlayerReader();
	$coColor = $messages_thread[0]->getReaderColor();
	$coName = $messages_thread[0]->getReaderName();
	$coAvatar = $messages_thread[0]->getReaderAvatar();
} else {
	$coId = $messages_thread[0]->getRPlayerWriter();
	$coColor = $messages_thread[0]->getWriterColor();
	$coName = $messages_thread[0]->getWriterName();
	$coAvatar = $messages_thread[0]->getWriterAvatar();
}

$totalMessage = count($messages_thread);

echo '<div class="component thread">';
	echo '<div class="head skin-1">';
		if ($coId == 0) {
			echo '<img src="' . MEDIA . 'avatar/medium/059-1.png" alt="salut" />';
			echo '<h2>Jean-Mi</h2>';
			echo '<em>Administrateur système</em>';
		} else {
			echo '<a href="' . APP_ROOT . 'diary/player-' . $coId . '"><img src="' . MEDIA . 'avatar/medium/' . $coAvatar . '.png" alt="salut" /></a>';
			echo '<h2>' . $coName . '</h2>';
			echo '<em>' . $totalMessage . ' message' . Format::addPlural($totalMessage) . '</em>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($coId != 0) {
				echo '<div class="tool">';
					echo '<span><a href="#" class="sh" data-target="textarea-' . $threadId_thread . '">continuer la conversation</a></span>';
				echo '</div>';

				echo '<form action="' . APP_ROOT . 'action/a-writemessage/thread-' . $messages_thread[0]->getThread() . '" method="post" id="textarea-' . $threadId_thread . '">';
					echo '<p class="input"><textarea name="message"></textarea></p>';
					echo '<p class="button"><input type="submit" value="envoyer" /></p>';
				echo '</form>';
			}

			$i = 0;
			for ($i; $i < count($messages_thread); $i++) {
				$m = $messages_thread[$i];

				if ($coId == 0) {
					echo '<div class="message left">';
						echo '<em class="name">Jean-Mi —</em>';
						echo $m->getContent();
						echo '<em class="option">— ' . Chronos::transform($m->getDSending()) . '</em>';
					echo '</div>';
				} else {
					$side  = ($m->getRPlayerWriter() == CTR::$data->get('playerId')) ? 'left' : 'right';
					echo '<div class="message color' . $m->getWriterColor() . ' ' . $side . '">';
						echo '<em class="name">' . $m->getWriterName() . ' —</em>';
						echo $m->getContent();
						echo '<em class="option">— ' . Chronos::transform($m->getDSending()) . '</em>';
					echo '</div>';
				}

				if ($i == MSM_STEPMESSAGE - 1) {
					break;
				}
			}

			if ($i < count($messages_thread) - 1) {
				echo '<a class="more-message" href="' . APP_ROOT . 'ajax/a-moremessage/thread-' . $threadId_thread . '/page-2">afficher plus de messages</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>