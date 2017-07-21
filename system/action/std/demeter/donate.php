<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Format;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');

#credit
$credit = $request->request->get('credit');

if ($credit) {
	$player = $playerManager->get($session->get('playerId'));
	
	$credit = ($credit > $player->credit) ? $player->credit : $credit;
	$playerManager->decreaseCredit($player, $credit);
	$colorManager->get($session->get('playerInfo')->get('color'))->credits += $credit;

	$this->getContainer()->get('entity_manager')->flush();
	
	$session->addFlashbag('Vous venez de remplir les caisse de votre faction de ' . $credit . ' crédit' . Format::addPlural($credit) . ' :)', Flashbag::TYPE_SUCCESS);
} else {
	throw new FormException('Manque d\'information.');
}