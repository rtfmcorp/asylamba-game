<?php

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');

$id = $request->query->get('id');

if ($id !== FALSE) {
	$S_TOM = $topicManager->getCurrentSession();
	$topicManager->newSession();
	$topicManager->load(array('id' => $id));

	if ($topicManager->size() == 1) {
		if ($session->get('playerInfo')->get('status') > 2) {
			if ($topicManager->get()->isClosed == 1) {
				$topicManager->get()->isClosed = 0;
			} else {
				$topicManager->get()->isClosed = 1;
			}
			$response->flashbag->add('Le sujet a bien été fermé/ouvert', Response::FLASHBAG_SUCCESS);
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