<?php

use App\Modules\Hermes\Model\Conversation;
use App\Modules\Hermes\Model\ConversationMessage;
use App\Classes\Library\Chronos;
use App\Classes\Library\Format;
use App\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$conversationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationMessageManager::class);
$parser = $this->getContainer()->get(\App\Classes\Library\Parser::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

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
						echo '<img src="' . $mediaPath . 'avatar/small/' . $session->get('playerInfo')->get('avatar') . '.png" alt="' . $session->get('playerInfo')->get('pseudo') . '" class="avatar" />';
						echo '<div class="content">';
							echo '<form action="' . Format::actionBuilder('writeconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" method="post">';
								echo '<div class="wysiwyg" data-id="new-message">';
									echo $parser->getToolbar();
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
			echo '<a href="' . $appRoot . 'embassy/player-' . $m->rPlayer . '"><img src="' . $mediaPath . 'avatar/medium/' . $m->playerAvatar . '.png" alt="' . $m->playerName . '" class="avatar" /></a>';
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
	echo '<a class="more-item" href="' . $appRoot . 'ajax/a-moremessage/conversation-' . $conversationManager->get()->id . '/page-' . (isset($page) ? ($page + 1) : 2) . '">';
		echo 'Afficher les messages précédents';
	echo '</a>';
}

if (!$message_listmode) {
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
