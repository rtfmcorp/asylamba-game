<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Hephaistos\Model\Feedback;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$parser = $this->getContainer()->get('parser');

if (empty($id = $request->request->get('id'))) {
    throw new FormException('Identifiant non renseigné');
}
if (empty($type = $request->request->get('type'))) {
    throw new FormException('Type non renseigné');
}
if (empty($status = $request->request->get('status'))) {
    throw new FormException('Statut non renseigné');
}
if (empty($authorId = $session->get('playerId'))) {
    throw new ErrorException('Vous devez être connecté');
}

$player = $playerManager->get($authorId);

if (!Utils::isAdmin($player->getBind())) {
    throw new ErrorException('Seul un administrateur peut réaliser cette action');
}

$manager = $this->getContainer()->get(
    ($type === Feedback::TYPE_BUG)
    ? 'hephaistos.bug_manager'
    : 'hephaistos.evolution_manager'
);

if (($feedback = $manager->get($id)) === null) {
    throw new ErrorException('L\'élément n\'existe pas');
}

$feedback->setStatus($status);

$result = $manager->update($feedback, $player);

$session->addFlashbag('Le statut a bien été mis à jour', Flashbag::TYPE_SUCCESS);

$response->redirect("feedback/id-$id/type-$type");