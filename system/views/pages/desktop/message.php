<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$conversationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationManager::class);
$conversationUserManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationUserManager::class);
$conversationMessageManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationMessageManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);

# background paralax
echo '<div id="background-paralax" class="message"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';
        # liste des conv's
	$display = 
		((int) $request->query->get('mode') === ConversationUser::CS_ARCHIVED)
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

	include $componentPath . 'conversation/list.php';

	if ($request->query->has('conversation')) {
		if ($request->query->get('conversation') === 'new') {
			include $componentPath . 'conversation/create.php';
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

				include $componentPath . 'conversation/messages.php';
				include $componentPath . 'conversation/manage.php';
			} else {
				$this->getContainer()->get('app.response')->redirect('message');
			}
		}
	} else {
		include $componentPath . 'conversation/new.php';
	}

	$unarchivedNotifications = $notificationManager->getPlayerNotificationsByArchive($session->get('playerId'), 0);
	include $componentPath . 'notif/last.php';

	$archivedNotifications = $notificationManager->getPlayerNotificationsByArchive($session->get('playerId'), 1);
	if (count($archivedNotifications) > 0) {
		include $componentPath . 'notif/archived.php';
	}
echo '</div>';
