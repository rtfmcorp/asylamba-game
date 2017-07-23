<?php

use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationUser;

$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$session = $this->getContainer()->get('session_wrapper');

if (!$conversation_listmode) {
	echo '<div class="component">';
		echo '<div class="head"><h1>Messagerie</h1></div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<div class="set-item">';
					echo '<a class="item" href="' . APP_ROOT . 'message/conversation-new">';
						echo '<div class="left">';
							echo '<span>+</span>';
						echo '</div>';
						echo '<div class="center">Démarrer une nouvelle conversation</div>';
					echo '</a>';
				echo '</div>';
}

for ($i = 0; $i < $conversationManager->size(); $i++) {
	$conv = $conversationManager->get($i);

	$convAvatar = NULL;
	$convName = array();
	$convColor = 0;
	$counter = 0;
	$restPlayer = 0;

	$ownLastView = NULL;

	if (count($conv->players) > 2) {
		$convAvatar = 'multi';
		$convColor  = 0;
	} else {
		$convAvatar = $conv->players[0]->playerAvatar;
		$convColor  = $conv->players[0]->playerColor;
	}

	foreach ($conv->players as $player) {
		if ($session->get('playerId') !== $player->rPlayer) {
			if ($counter < 5) {
				$convName[] = '<strong>' . $player->playerName . '</strong>';
			} else {
				$restPlayer++;
			}

			if ($conv->type == Conversation::TY_SYSTEM) {
				if ($player->convPlayerStatement == ConversationUser::US_ADMIN) {
					$convAvatar = $player->playerAvatar;
					$convColor  = $player->playerColor;
				}
			}

			$counter++;
		} else {
			$ownLastView = $player->dLastView;
		}
	}

	if ($restPlayer !== 0) {
		$convName[count($convName) - 1] .= ' et <strong>' . $restPlayer . '+</strong>';
	}

	echo '<a class="conv-item" href="' . APP_ROOT . 'message/mode-' . $display . '/conversation-' . $conv->id . '">';
		echo '<span class="cover">';
			echo '<img src="' . MEDIA . 'avatar/small/' . $convAvatar . '.png" alt="" class="picture color' . $convColor . '" />';
			echo '<span class="number">' . $conv->messages . '</span>';
			if (strtotime($ownLastView) < strtotime($conv->dLastMessage)) {
				echo '<span class="new-message"><img src="' . MEDIA . 'common/nav-message.png" alt="" /></span>';
			}
		echo '</span>';

		echo '<span class="data">';
			echo Chronos::transform($conv->dLastMessage) . '<br />';
			echo empty($conv->title)
				? implode(', ', $convName)
				: '<strong>' . $conv->title . '</strong>';
		echo '</span>';
	echo '</a>';
}

if ($conversationManager->size() == Conversation::CONVERSATION_BY_PAGE) {
	echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-moreconversation/mode-' . $display . '/page-' . (isset($page) ? ($page + 1) : 2) . '">';
		echo 'Afficher plus de conversations';
	echo '</a>';
}

if (!$conversation_listmode) {
		echo $display == ConversationUser::CS_ARCHIVED
			? '<a class="common-link" href="' . APP_ROOT . 'message">Retour aux conversations</a>'
			: '<a class="common-link" href="' . APP_ROOT . 'message/mode-' . ConversationUser::CS_ARCHIVED . '">Voir les conversations archivées</a>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}