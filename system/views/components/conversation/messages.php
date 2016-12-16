<?php

use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Parser;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');
$session = $this->getContainer()->get('app.session');
$sessionToken = $this->get('token');

if (!$message_listmode) {
	echo '<div class="component topic size2">';
		echo '<div class="head skin-5">';
			if (!empty($conversationManager->get()->title)) {
				echo '<h2>' . $conversationManager->get()->title . '</h2>';
			}
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				if ($conversationManager->get()->type != Conversation::TY_SYSTEM) {
					echo '<div class="message write">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $session->get('playerInfo')->get('avatar') . '.png" alt="' . $session->get('playerInfo')->get('pseudo') . '" class="avatar" />';
						echo '<div class="content">';
							echo '<form action="' . Format::actionBuilder('writeconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" method="post">';
								echo '<div class="wysiwyg" data-id="new-message">';
									echo (new Parser())->getToolbar();
									echo '<textarea name="content" id="new-message"></textarea>';
								echo '</div>';

								echo '<button>Répondre</button>';
							echo '</form>';
						echo '</div>';
					echo '</div>';
				}
}

for ($i = 0; $i < $conversationMessageManager->size(); $i++) {
	$m = $conversationMessageManager->get($i);

	$status = ColorResource::getInfo($m->playerColor, 'status');
	$status = $status[$m->playerStatus - 1];

	if ($i != 0 AND $conversationMessageManager->get($i - 1)->dCreation > $dPlayerLastMessage AND $m->dCreation <= $dPlayerLastMessage) {
		echo '<div class="system-message">';
			echo 'Dernier message lu';
		echo '</div>';
	}

	if ($m->type == ConversationMessage::TY_STD) {
		echo '<div class="message">';
			echo '<a href="' . APP_ROOT . 'embassy/player-' . $m->rPlayer . '"><img src="' . MEDIA . 'avatar/medium/' . $m->playerAvatar . '.png" alt="' . $m->playerName . '" class="avatar" /></a>';
			echo '<div class="content">';
				echo '<p class="text">';
					echo $m->content;
				echo '</p>';
				echo '<p class="footer">';
					echo $status . ' ' . $m->playerName . ', ';
					echo Chronos::transform($m->dCreation);
				echo '</p>';
			echo '</div>';
		echo '</div>';
	} else {
		echo '<div class="system-message">';
			echo $m->content;
		echo '</div>';
	}
}

if ($conversationMessageManager->size() == ConversationMessage::MESSAGE_BY_PAGE) {
	echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-moremessage/conversation-' . $conversationManager->get()->id . '/page-' . (isset($page) ? ($page + 1) : 2) . '">';
		echo 'Afficher les messages précédents';
	echo '</a>';
}

if (!$message_listmode) {
			echo '</div>';
		echo '</div>';
	echo '</div>';
}