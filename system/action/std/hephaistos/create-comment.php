<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Library\Flashbag;

use Asylamba\Modules\Hephaistos\Model\Feedback;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$commentaryManager = $this->getContainer()->get('hephaistos.commentary_manager');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$parser = $this->getContainer()->get('parser');

if (empty($feedbackId = $request->request->get('feedback-id'))) {
    throw new FormException('Identifiant non renseigné');
}
if (empty($type = $request->request->get('feedback-type'))) {
    throw new FormException('Type non renseigné');
}
if (empty($content = $request->request->get('content'))) {
    throw new FormException('Description non renseignée');
}
if (empty($authorId = $session->get('playerId'))) {
    throw new ErrorException('Vous devez être connecté');
}

$manager = $this->getContainer()->get(($type === Feedback::TYPE_BUG) ? 'hephaistos.bug_manager' : 'hephaistos.evolution_manager');

if (($feedback = $manager->get($feedbackId)) === null) {
    throw new ErrorException('Le feedback renseigné n\'existe pas');
}

$result = $commentaryManager->create($feedback, $parser->parse($content), $playerManager->get($authorId));

$session->addFlashbag('Votre commentaire a bien été créé', Flashbag::TYPE_SUCCESS);

$response->redirect("feedback/id-$feedbackId/type-$type");