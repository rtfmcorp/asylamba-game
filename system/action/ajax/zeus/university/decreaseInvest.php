<?php
include_once ATHENA;
# decrease university investment action

# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment

$category = Utils::getHTTPData('category');
$quantity = Utils::getHTTPData('quantity');


// protection des inputs
$p = new Parser();
$category = $p->protect($category);

if ($category !== FALSE AND $category !== '') {
	if (in_array($category, array('natural', 'life', 'social', 'informatic'))) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		$player = ASM::$pam->get();

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

			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('Vous n\'avez plus de pourcentages à libérer dans cette faculté', ALERT_STD_INFO);
		}
	} else {
	CTR::$alert->add('Changement d\'investissement impossible - faculté inconnue', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour augmenter l\'investissement', ALERT_STD_FILLFORM);
}
?>