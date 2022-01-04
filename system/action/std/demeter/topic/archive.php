<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$topicManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\ForumTopicManager::class);

$id = $request->query->get('id');

if ($id !== FALSE) {
	$S_TOM = $topicManager->getCurrentSession();
	$topicManager->newSession();
	$topicManager->load(array('id' => $id));

	if ($topicManager->size() == 1) {
		if ($session->get('playerInfo')->get('status') > 2) {
			if ($topicManager->get()->isArchived == 1) {
				$topicManager->get()->isArchived = 0;
			} else {
				$topicManager->get()->isArchived = 1;
			}
			$response->redirect('faction/view-forum/forum-' . $topicManager->get()->rForum);
			$session->addFlashbag('Le sujet a bien été archivé/désarchivé', Flashbag::TYPE_SUCCESS);
		} else {
			throw new FormException('Vous n\'avez pas les droits');	
		}
	} else {
		throw new FormException('Ce sujet n\'existe pas');
	}

	$topicManager->changeSession($S_TOM);
} else {
	throw new FormException('Manque d\'information');
}
