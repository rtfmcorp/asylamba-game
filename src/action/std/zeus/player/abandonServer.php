<?php

use App\Classes\Exception\ErrorException;
use App\Modules\Zeus\Model\Player;

$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$response = $this->getContainer()->get('app.response');

if (($player = $playerManager->get($session->get('playerId'))) !== null) {
	# sending API call to delete account link to server
	$success = $this->getContainer()->get('api')->abandonServer($player->bind);

	if ($success) {
		$player->bind = $player->bind . 'ABANDON';
		$player->statement = Player::DELETED;
		
		$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($player);
		# clean session
		$session->destroy();
		$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs', TRUE);
	} else {
		throw new ErrorException('Une erreur s\'est produite sur le portail. Contactez un administrateur pour résoudre ce problème.');
	}
} else {
	throw new ErrorException('Une erreur s\'est produite. Contactez un administrateur pour résoudre ce problème.');
}
