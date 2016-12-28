<?php
# decrease university investment action

# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');

if (($category = $request->query->get('category')) === null || ($quantity = $request->query->get('quantity')) === null) {
	throw new FormException('Pas assez d\'informations pour augmenter l\'investissement');
}
if (!in_array($category, array('natural', 'life', 'social', 'informatic'))) {
	throw new ErrorException('Changement d\'investissement impossible - faculté inconnue');
}

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');

$S_PAM1 = $playerManager->getCurrentSession();
$playerManager->newSession();
$playerManager->load(array('id' => $session->get('playerId')));

$player = $playerManager->get();

if ($quantity === FALSE) {
	$quantity = 1;
}

switch ($category) {
	case 'natural' :
		$oldInvest = $player->partNaturalSciences;
		break;
	case 'life' : 
		$oldInvest = $player->partLifeSciences;
		break;
	case 'social' :
		$oldInvest = $player->partSocialPoliticalSciences;
		break;
	case 'informatic' : 
		$oldInvest = $player->partInformaticEngineering;
		break;
}

if ($oldInvest != 0) {
	if ($oldInvest < $quantity) {
		$quantity = $oldInvest;
	}
	switch ($category) {
		case 'natural' :
			$player->partNaturalSciences = $player->partNaturalSciences - $quantity;
			break;
		case 'life' : 
			$player->partLifeSciences = $player->partLifeSciences - $quantity;
			break;
		case 'social' : 
			$player->partSocialPoliticalSciences = $player->partSocialPoliticalSciences - $quantity;
			break;
		case 'informatic' : 
			$player->partInformaticEngineering = $player->partInformaticEngineering - $quantity;
			break;
	}
	$playerManager->changeSession($S_PAM1);
}