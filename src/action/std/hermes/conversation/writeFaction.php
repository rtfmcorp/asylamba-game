<?php

# int player 		id du joueur (facultatif)
# string message 	message à envoyer

use App\Classes\Worker\CTR;
use App\Classes\Worker\ASM;
use App\Classes\Library\Utils;
use App\Modules\Zeus\Model\Player;
use App\Modules\Hermes\Model\ConversationUser;
use App\Modules\Hermes\Model\ConversationMessage;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$parser = $this->getContainer()->get(\App\Classes\Library\Parser::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$conversationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationMessageManager::class);

# protection des inputs
$content = $parser->parse($request->request->get('message'));

if ($content !== FALSE) {
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
						$message->dLastModification = NULL;

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
