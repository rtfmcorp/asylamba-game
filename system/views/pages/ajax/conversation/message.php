<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');

$page = $request->query->has('page')
    ? $request->query->get('page')
    : 1;

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
        [($page - 1) * ConversationMessage::MESSAGE_BY_PAGE, ConversationMessage::MESSAGE_BY_PAGE]
    );

    $message_listmode = true;

    include COMPONENT . 'conversation/messages.php';
}
