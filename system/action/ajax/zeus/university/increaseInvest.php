<?php
include_once ATHENA;
# increase university investment action

# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment (facultatif, si non-défini, $quantity = 1)

if (CTR::$get->exist('category')) {
	$category = CTR::$get->get('category');
} elseif (CTR::$post->exist('category')) {
	$category = CTR::$post->get('category');
} else {
	$category = FALSE;
}
if (CTR::$get->exist('quantity')) {
	$quantity = CTR::$get->get('quantity');
} elseif (CTR::$post->exist('quantity')) {
	$quantity = CTR::$post->get('quantity');
} else {
	$quantity = FALSE;
}

// protection des inputs
$p = new Parser();
$category = $p->protect($category);

if ($category !== FALSE AND $category !== '') {
	if (in_array($category, array('natural', 'life', 'social', 'informatic'))) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		$player = ASM::$pam->get();

		$totalInvest = $player->partNaturalSciences + $player->partLifeSciences + $player->partSocialPoliticalSciences + $player->partInformaticEngineering;
		if ($totalInvest < 100) {
			if ($quantity === FALSE) {
				$quantity = 1;
			}
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
		} else {
			CTR::$alert->add('Vous devez d\'abord libérer des pourcentages avant de les attribuer ailleurs', ALERT_STD_INFO);
		}
		ASM::$pam->changeSession($S_PAM1);
	} else {
	CTR::$alert->add('Changement d\'investissement impossible - faculté inconnue', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour augmenter l\'investissement', ALERT_STD_FILLFORM);
}
?>