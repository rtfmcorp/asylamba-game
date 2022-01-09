<?php
#rplayer	id du joueur

use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);

if ($session->get('playerInfo')->get('status') > Player::PARLIAMENT && $session->get('playerInfo')->get('status') < Player::CHIEF) {
	if (($minister = $playerManager->get($session->get('playerId'))) !== null) {
		$minister->status = Player::PARLIAMENT;
		$session->get('playerInfo')->add('status', Player::PARLIAMENT);
		$session->addFlashbag('Vous n\'êtes plus membre du gouvernement.', Flashbag::TYPE_SUCCESS);
		$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($minister);
	} else {
		throw new ErrorException('Ce joueur n\'existe pas.');
	}
} else {
	throw new ErrorException('Vous n\'êtes pas dans le gouvernement de votre faction ou en êtes le chef.');
}
