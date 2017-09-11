<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$database = $this->getContainer()->get('database');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationUserManager = $this->getContainer()->get('hermes.conversation_user_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');
$response = $this->getContainer()->get('app.response');
$request = $this->getContainer()->get('app.request');
$parser = $this->getContainer()->get('parser');

$recipients = $request->request->get('recipients');
$content = $request->request->get('content');

$content = $parser->parse($content);

if (!empty($recipients) && !empty($content)) {
    if (strlen($content) < 10000) {
        # traitement des utilisateurs multiples
        $recipients = explode(',', $recipients);
        $plId = $session->get('playerId');
        $recipients = array_filter($recipients, function ($e) use ($plId) {
            return $e == $plId ? false : true;
        });
        $recipients[] = 0;

        if (count($recipients) <= ConversationUser::MAX_USERS) {
            # chargement des utilisateurs
            $players = $playerManager->getByIdsAndStatements($recipients, [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);

            if (count($players) >= 1) {
                # création de la date précédente
                $readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

                # créer la conversation
                $conv = new Conversation();

                $conv->messages = 1;
                $conv->type = Conversation::TY_USER;
                $conv->dCreation = Utils::now();
                $conv->dLastMessage = Utils::now();

                $conversationManager->add($conv);

                # créer le user créateur de la conversation
                $user = new ConversationUser();

                $user->rConversation = $conv->id;
                $user->rPlayer = $session->get('playerId');
                $user->convPlayerStatement = ConversationUser::US_ADMIN;
                $user->convStatement = ConversationUser::CS_DISPLAY;
                $user->dLastView = Utils::now();

                $conversationUserManager->add($user);

                # créer la liste des users
                foreach ($players as $player) {
                    $user = new ConversationUser();

                    $user->rConversation = $conv->id;
                    $user->rPlayer = $player->id;
                    $user->convPlayerStatement = ConversationUser::US_STANDARD;
                    $user->convStatement = ConversationUser::CS_DISPLAY;
                    $user->dLastView = $readingDate;

                    $conversationUserManager->add($user);
                }

                # créer le premier message
                $message = new ConversationMessage();

                $message->rConversation = $conv->id;
                $message->rPlayer = $session->get('playerId');
                $message->type = ConversationMessage::TY_STD;
                $message->content = $content;
                $message->dCreation = Utils::now();
                $message->dLastModification = null;

                $conversationMessageManager->add($message);

                if (DATA_ANALYSIS) {
                    $qr = $database->prepare(
                        'INSERT INTO 
						DA_SocialRelation(`from`, `to`, `type`, `message`, dAction)
						VALUES(?, ?, ?, ?, ?)'
                    );
                    $qr->execute([$session->get('playerId'), $players[0]->getId(), 2, $content, Utils::now()]);
                }

                $session->addFlashbag('La conversation a été créée.', Flashbag::TYPE_SUCCESS);
                $response->redirect('message/conversation-' . $conv->id);
            } else {
                throw new ErrorException('Le joueur n\'est pas joignable.');
            }
        } else {
            throw new ErrorException('Nombre maximum de joueur atteint.');
        }
    } else {
        throw new ErrorException('Le message est trop long.');
    }
} else {
    throw new FormException('Informations manquantes pour démarrer une nouvelle conversation.');
}
