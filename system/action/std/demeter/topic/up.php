<?php

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');

$id 		= $request->query->get('id');

if ($id !== FALSE) {
	$_TOM = $topicManager->getCurrentsession();
	$topicManager->newSession();
	$topicManager->load(array('id' => $id));

	if ($topicManager->size() == 1) {
		if (in_array($session->get('playerInfo')->get('status'), [Player::CHIEF, Player::WARLORD, Player::TREASURER, Player::MINISTER])) {
			$topic = $topicManager->get();

			if ($topic->isUp) {
				$topic->isUp = FALSE;
			} else {
				$topic->isUp = TRUE;
			}
		} else {
			throw new ErrorException('Vous ne disposez pas des droits nécessaires pour cette action.');
		}
	} else {
		throw new ErrorException('Le sujet demandé n\'existe pas.');
	}
	
	$this->getContainer()->get('app.response')->redirect('faction/view-forum/forum-' . $topicManager->get()->rForum . '/topic-' . $topicManager->get()->id . '/sftr-2');
	$topicManager->changeSession($_TOM);
} else {
	throw new ErrorException('Manque d\'information.');
}