<?php

use App\Classes\Library\Flashbag;
use App\Classes\Exception\FormException;
use App\Classes\Library\Format;

$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');

#credit
$credit = $request->request->get('credit');

if ($credit) {
	$player = $playerManager->get($session->get('playerId'));
	
	$credit = ($credit > $player->credit) ? $player->credit : $credit;
	$playerManager->decreaseCredit($player, $credit);
	$colorManager->get($session->get('playerInfo')->get('color'))->credits += $credit;

	$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
	
	$session->addFlashbag('Vous venez de remplir les caisse de votre faction de ' . $credit . ' crédit' . Format::addPlural($credit) . ' :)', Flashbag::TYPE_SUCCESS);
} else {
	throw new FormException('Manque d\'information.');
}
