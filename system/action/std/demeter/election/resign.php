<?php
#rplayer	id du joueur

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');

if ($session->get('playerInfo')->get('status') > Player::PARLIAMENT && $session->get('playerInfo')->get('status') < Player::CHIEF) {
    if (($minister = $playerManager->get($session->get('playerId'))) !== null) {
        $minister->status = Player::PARLIAMENT;
        $session->get('playerInfo')->add('status', Player::PARLIAMENT);
        $session->addFlashbag('Vous n\'êtes plus membre du gouvernement.', Flashbag::TYPE_SUCCESS);
        $this->getContainer()->get('entity_manager')->flush($minister);
    } else {
        throw new ErrorException('Ce joueur n\'existe pas.');
    }
} else {
    throw new ErrorException('Vous n\'êtes pas dans le gouvernement de votre faction ou en êtes le chef.');
}
