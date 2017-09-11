<?php

# int player 		id du joueur (facultatif)
# string message 	message à envoyer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$parser = $this->getContainer()->get('parser');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');

# protection des inputs
$content = $parser->parse($request->request->get('message'));

if ($content !== false) {
    if (($player = $playerManager->get($session->get('playerId')))) {
        if ($player->status > Player::PARLIAMENT) {
            if ($content !== '' && strlen($content) < 25000) {
                if (($factionAccount = $playerManager->getFactionAccount($player->rColor)) !== null) {
                    $S_CVM = $conversationManager->getCurrentSession();
                    $conversationManager->newSession();
                    $conversationManager->load(
                        ['cu.rPlayer' => $factionAccount->id]
                    );

                    if ($conversationManager->size() == 1) {
                        $conv = $conversationManager->get();

                        $conv->messages++;
                        $conv->dLastMessage = Utils::now();

                        # désarchiver tout les users
                        $users = $conv->players;
                        foreach ($users as $user) {
                            $user->convStatement = ConversationUser::CS_DISPLAY;
                        }

                        # création du message
                        $message = new ConversationMessage();

                        $message->rConversation = $conv->id;
                        $message->rPlayer = $player->id;
                        $message->type = ConversationMessage::TY_STD;
                        $message->content = $content;
                        $message->dCreation = Utils::now();
                        $message->dLastModification = null;

                        $conversationMessageManager->add($message);
                    } else {
                        throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
                    }
                    
                    $conversationManager->changeSession($S_CVM);
                }
            } else {
                throw new FormException('Le message est vide ou trop long');
            }
        } else {
            throw new FormException('Vizs n\'avez pas les droits pour poster un message officiel');
        }
    } else {
        throw new FormException('Ce joueur n\'existe pas');
    }
} else {
    throw new FormException('Pas assez d\'informations pour écrire un message officiel');
}
