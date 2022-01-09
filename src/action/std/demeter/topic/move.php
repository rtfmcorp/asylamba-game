<?php

use App\Classes\Library\Utils;
use App\Modules\Demeter\Resource\ForumResources;
use App\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$topicManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Forum\ForumTopicManager::class);

$rForum = $request->request->get('rforum');
$id = $request->query->get('id');

if ($rForum !== FALSE && $id !== FALSE) {
	$_TOM = $topicManager->getCurrentSession();
	$topicManager->newSession();
	$topicManager->load(array('id' => $id));

	if ($topicManager->size() > 0) {
		if ($session->get('playerInfo')->get('status') > 2) {
			$isOk = FALSE;

			for ($i = 1; $i < ForumResources::size() + 1; $i++) { 
				if (ForumResources::getInfo($i, 'id') == $rForum) {
					$isOk = TRUE;
					break;
				}
			}

			if ($isOk) {
				$topicManager->get()->rForum = $rForum;
				$topicManager->get()->dLastModification = Utils::now();

				$response->redirect('faction/view-forum/forum-' . $rForum . '/topic-' . $topicManager->get()->id);
			} else {
				throw new FormException('Le forum de destination n\'existe pas');
			}
		} else {
			throw new FormException('Vous n\'avez pas les droits pour cette opération');
		}
	} else {
		throw new FormException('Ce sujet n\'existe pas');	
	}
	$topicManager->changeSession($_TOM);
} else {
	throw new FormException('Manque d\'information');
}
