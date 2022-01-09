<?php

use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;

# switch advertisement action
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);

if (($player = $playerManager->get($session->get('playerId'))) !== null) {
	if ($player->premium == 0) {
		$player->premium = 1;
		$session->get('playerInfo')->add('premium', 1);
		$session->addFlashbag('Publicité déactivée. Vous êtes vraiment sûr ? Allez, re-cliquez un coup, c\'est cool les pubs.', Flashbag::TYPE_SUCCESS);
	} else {
		$player->premium = 0;
		$session->get('playerInfo')->add('premium', 0);
		$session->addFlashbag('Publicitées activées. Merci beaucoup pour votre soutien. Je vous aime.', Flashbag::TYPE_SUCCESS);
	}
	$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($player);
} else {
	throw new ErrorException('petit bug là, contactez un administrateur rapidement sous risque que votre ordinateur explose');
}
