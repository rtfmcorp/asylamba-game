<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$parser = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$conversationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationMessageManager::class);

$conversation 	= $request->query->get('conversation');
$content 		= $request->request->get('content');

$content = $parser->parse($content);

if (!empty($conversation) && !empty($content)) {
	if (strlen($content) < 10000) {
		$S_CVM = $conversationManager->getCurrentSession();
		$conversationManager->newSession();
		$conversationManager->load(
			array('c.id' => $conversation, 'cu.rPlayer' => $session->get('playerId'))
		);

		if ($conversationManager->size() == 1) {
			$conv = $conversationManager->get();

			if ($conv->type != Conversation::TY_SYSTEM) {
				$DA_recipient;

				$conv->messages++;
				$conv->dLastMessage = Utils::now();

				# désarchiver tout les users
				$users = $conv->players;
				foreach ($users as $user) {
					$user->convStatement = ConversationUser::CS_DISPLAY;

					if ($user->rPlayer == $session->get('playerId')) {
						$user->dLastView = Utils::now();
					} else {
						$DA_recipient = $user->rPlayer;
					}
				}

				# création du message
				$message = new ConversationMessage();

				$message->rConversation = $conv->id;
				$message->rPlayer = $session->get('playerId');
				$message->type = ConversationMessage::TY_STD;
				$message->content = $content;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;

				$conversationMessageManager->add($message);

				if (DATA_ANALYSIS) {
					$qr = $database->prepare('INSERT INTO 
						DA_SocialRelation(`from`, `to`, `type`, `message`, dAction)
						VALUES(?, ?, ?, ?, ?)'
					);
					$qr->execute([$session->get('playerId'), $DA_recipient, 2, $content, Utils::now()]);
				}
			}
		} else {
			throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
		}
		
		$conversationManager->changeSession($S_CVM);
	} else {
		throw new ErrorException('Le message est trop long.');
	}
} else {
	throw new ErrorException('Informations manquantes pour démarrer une nouvelle conversation.');
}
