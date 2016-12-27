<?php

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');
$forumMessageManager = $this->getContainer()->get('demeter.forum_message_manager');

$content = $request->request->get('content');
$id = $request->query->get('id');

if ($content && $id) {
	$_FMM = $forumMessageManager->getCurrentSession();
	$forumMessageManager->newSession();
	$forumMessageManager->load(array('id' => $id));

	if ($forumMessageManager->size() > 0) {
		$m = $forumMessageManager->get();

		$_TOM = $topicManager->getCurrentSession();
		$topicManager->newSession();
		$topicManager->load(array('id' => $m->rTopic));

		$t = $topicManager->get();

		if ($session->get('playerId') == $m->rPlayer || ($session->get('playerInfo')->get('status') > 2 && $t->rForum != 20)) {
			$m->edit($content);
			$m->dLastModification = Utils::now();

			$response->flashbag->add('Message édité.', Response::FLASHBAG_SUCCESS);
		} else {
			throw new ErrorException('Vous ne pouvez pas éditer ce message.');
		}

		$topicManager->changeSession($_TOM);
	} else {
		throw new ErrorException('Le message n\'existe pas.');
	}

	$forumMessageManager->changeSession($_FMM);
} else {
	throw new FormException('Manque d\'information.');
}