<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Format;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');

#credit
$credit = $request->request->get('credit');

if ($credit) {
	$S_CLM = $colorManager->getCurrentSession();
	$colorManager->newSession();
	$colorManager->load(array('id' => $session->get('playerInfo')->get('color')));

	$player = $playerManager->get($session->get('playerId'));
	
	$credit = ($credit > $player->credit) ? $player->credit : $credit;
	$playerManager->decreaseCredit($player, $credit);
	$colorManager->get()->credits += $credit;

	$session->addFlashbag('Vous venez de remplir les caisse de votre faction de ' . $credit . ' crédit' . Format::addPlural($credit) . ' :)', Flashbag::TYPE_SUCCESS);
	
	$colorManager->changeSession($S_CLM);
} else {
	throw new FormException('Manque d\'information.');
}