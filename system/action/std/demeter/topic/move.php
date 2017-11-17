<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Resource\ForumResources;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');

$rForum = $request->request->get('rforum');
$id = $request->query->get('id');

if ($rForum !== false && $id !== false) {
    $_TOM = $topicManager->getCurrentSession();
    $topicManager->newSession();
    $topicManager->load(array('id' => $id));

    if ($topicManager->size() > 0) {
        if ($session->get('playerInfo')->get('status') > 2) {
            $isOk = false;

            for ($i = 1; $i < ForumResources::size() + 1; $i++) {
                if (ForumResources::getInfo($i, 'id') == $rForum) {
                    $isOk = true;
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
