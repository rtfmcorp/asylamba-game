<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationUserManager = $this->getContainer()->get('hermes.conversation_user_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

# background paralax
echo '<div id="background-paralax" class="message"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
        # liste des conv's
	$display = 
		($request->query->get('mode') === ConversationUser::CS_ARCHIVED)
		? ConversationUser::CS_ARCHIVED
		: ConversationUser::CS_DISPLAY
	;
	# chargement de toutes les conversations
	$conversationManager->newSession();
	$conversationManager->load(
		['cu.rPlayer' => $session->get('playerId'), 'cu.convStatement' => $display],
		['c.dLastMessage', 'DESC'],
		[0, Conversation::CONVERSATION_BY_PAGE]
	);

	$conversation_listmode = FALSE;

	include COMPONENT . 'conversation/list.php';

	if ($request->query->has('conversation')) {
		if ($request->query->get('conversation') === 'new') {
			include COMPONENT . 'conversation/create.php';
		} else {
			# chargement d'une conversation
			$conversationManager->newSession();
			$conversationManager->load(
				['c.id' => $request->query->get('conversation'), 'cu.rPlayer' => $session->get('playerId')]
			);

			if ($conversationManager->size() == 1) {
				# chargement des infos d'une conversation
				$conversationUserManager->newSession();
				$conversationUserManager->load(['c.rConversation' => $request->query->get('conversation')]);

				# mis à jour de l'heure de la dernière vue
				for ($i = 0; $i < $conversationUserManager->size(); $i++) { 
					if ($conversationUserManager->get($i)->rPlayer == $session->get('playerId')) {
						$dPlayerLastMessage = $conversationUserManager->get($i)->dLastView;
						$currentUser = $conversationUserManager->get($i);

						$conversationUserManager->get($i)->dLastView = Utils::now();
					}
				}

				# chargement des messages
				$conversationMessageManager->newSession();
				$conversationMessageManager->load(
					['c.rConversation' => $request->query->get('conversation')],
					['c.dCreation', 'DESC'],
					[0, ConversationMessage::MESSAGE_BY_PAGE]
				);

				$message_listmode = FALSE;

				include COMPONENT . 'conversation/messages.php';
				include COMPONENT . 'conversation/manage.php';
			} else {
				$this->getContainer()->get('app.response')->redirect('message');
			}
		}
	} else {
		include COMPONENT . 'conversation/new.php';
	}

	$unarchivedNotifications = $notificationManager->getPlayerNotificationsByArchive($session->get('playerId'), 0);
	include COMPONENT . 'notif/last.php';

	$archivedNotifications = $notificationManager->getPlayerNotificationsByArchive($session->get('playerId'), 0);
	if (count($archivedNotifications) > 0) {
		include COMPONENT . 'notif/archived.php';
	}
echo '</div>';