<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Library\Flashbag;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$bugManager = $this->getContainer()->get('hephaistos.bug_manager');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');

if (empty($title = $request->request->get('title'))) {
    throw new FormException('Titre non renseigné');
}
if (empty($description = $request->request->get('description'))) {
    throw new FormException('Description non renseignée');
}
if (empty($authorId = $session->get('playerId'))) {
    throw new ErrorException('Vous devez être connecté');
}

$result = $bugManager->create($title, $description, $playerManager->get($authorId));

$session->addFlashbag('Le bug a bien été reporté', Flashbag::TYPE_SUCCESS);

$response->redirect('project');