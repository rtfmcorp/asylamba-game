<?php
# increase university investment action

# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment (facultatif, si non-défini, $quantity = 1)

use App\Classes\Exception\FormException;
use App\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');

if (($category = $request->query->get('category')) === null || ($quantity = $request->query->get('quantity')) === null) {
	throw new FormException('Pas assez d\'informations pour augmenter l\'investissement');
}
if (!in_array($category, array('natural', 'life', 'social', 'informatic'))) {
	throw new ErrorException('Changement d\'investissement impossible - faculté inconnue');
}

$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);

$player = $playerManager->get($session->get('playerId'));

$totalInvest =
	$player->partNaturalSciences + 
	$player->partLifeSciences + 
	$player->partSocialPoliticalSciences + 
	$player->partInformaticEngineering;

if ($totalInvest < 100) {
	if ($totalInvest + $quantity > 100) {
		$quantity = 100 - $totalInvest;
	}

	switch ($category) {
		case 'natural' : 
			$player->partNaturalSciences = $player->partNaturalSciences + $quantity;
			break;
		case 'life' : 
			$player->partLifeSciences = $player->partLifeSciences + $quantity;
			break;
		case 'social' : 
			$player->partSocialPoliticalSciences = $player->partSocialPoliticalSciences + $quantity;
			break;
		case 'informatic' : 
			$player->partInformaticEngineering = $player->partInformaticEngineering + $quantity;
			break;
	}
}
$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($player);
