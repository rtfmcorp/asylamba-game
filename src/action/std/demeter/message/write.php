<?php
# écrire un message dans un topic du forum de faction
# content
# rtopic

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Modules\Zeus\Resource\TutorialResource;
use App\Modules\Demeter\Model\Forum\ForumMessage;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$topicManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\ForumTopicManager::class);
$forumMessageManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\ForumMessageManager::class);
$tutorialHelper = $this->getContainer()->get(\Asylamba\Modules\Zeus\Helper\TutorialHelper::class);

$content = $request->request->get('content');
$rTopic  = $request->query->get('rtopic');

if ($rTopic AND $content) {
	$S_TOM_1 = $topicManager->getCurrentSession();
	$topicManager->load(array('id' => $rTopic));

	if ($topicManager->size() == 1) {
		if (!$topicManager->get()->isClosed) {
			$message = new ForumMessage();
			$message->rPlayer = $session->get('playerId');
			$message->rTopic = $rTopic;
			$message->dCreation = Utils::now();
			$message->dLastMessage = Utils::now();

			$forumMessageManager->edit($message, $content);
			
			$forumMessageManager->add($message);

			$topicManager->get()->dLastMessage = Utils::now();

			# tutorial
			if ($session->get('playerInfo')->get('stepDone') == FALSE &&
				$session->get('playerInfo')->get('stepTutorial') === TutorialResource::FACTION_FORUM) {
				$tutorialHelper->setStepDone();
			}

			if ($topicManager->get()->rForum != 30) {
				$response->redirect('faction/view-forum/forum-' . $topicManager->get()->rForum . '/topic-' . $rTopic . '/sftr-2');
			}

			if (true === $this->getContainer()->getParameter('data_analysis')) {
				$qr = $database->prepare('INSERT INTO 
					DA_SocialRelation(`from`, type, message, dAction)
					VALUES(?, ?, ?, ?)'
				);
				$qr->execute([$session->get('playerId'), 1, $content, Utils::now()]);
			}

			$session->addFlashbag('Message créé.', Flashbag::TYPE_SUCCESS);
		} else {
			throw new ErrorException('Ce sujet est fermé.');
		}
	} else {
		throw new ErrorException('Le topic n\'existe pas.');
	}

	$topicManager->changeSession($S_TOM_1);
} else {
	throw new FormException('Manque d\'information.');
}
