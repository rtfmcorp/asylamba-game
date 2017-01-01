<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

# switch advertisement action
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');

$S_PAM1 = $playerManager->getCurrentSession();
$playerManager->newSession(ASM_UMODE);
$playerManager->load(array('id' => $session->get('playerId')));

if ($playerManager->size() == 1) {
	$p = $playerManager->get();

	if ($p->premium == 0) {
		$p->premium = 1;
		$session->get('playerInfo')->add('premium', 1);
		$session->addFlashbag('Publicité déactivée. Vous êtes vraiment sûr ? Allez, re-cliquez un coup, c\'est cool les pubs.', Flashbag::TYPE_SUCCESS);
	} else {
		$p->premium = 0;
		$session->get('playerInfo')->add('premium', 0);
		$session->addFlashbag('Publicitées activées. Merci beaucoup pour votre soutien. Je vous aime.', Flashbag::TYPE_SUCCESS);
	}
} else {
	throw new ErrorException('petit bug là, contactez un administrateur rapidement sous risque que votre ordinateur explose');
}
$playerManager->changeSession($S_PAM1);
	

