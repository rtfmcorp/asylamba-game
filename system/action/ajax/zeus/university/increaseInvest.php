<?php
# increase university investment action

# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment (facultatif, si non-défini, $quantity = 1)

use Asylamba\Classes\Library\Utils;

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$category = Utils::getHTTPData('category');
$quantity = Utils::getHTTPData('quantity');

if ($category !== FALSE AND $quantity !== FALSE) {
	if (in_array($category, array('natural', 'life', 'social', 'informatic'))) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

		$player = ASM::$pam->get();

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

		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('Changement d\'investissement impossible - faculté inconnue', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour augmenter l\'investissement', ALERT_STD_FILLFORM);
}