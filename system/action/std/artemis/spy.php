<?php
# send a spy

# int rPlace 		id of the place to spy
# int price			credit price for spying

include_once GAIA;
include_once ARTEMIS;
include_once ZEUS;

$rPlace = Utils::getHTTPData('rplace');
$price = Utils::getHTTPData('price');

if ($rPlace !== FALSE AND $price !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('credit') >= $price) {
		# débit des crédits au joueur
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		ASM::$pam->get()->decreaseCredit($price);
		ASM::$pam->changeSession($S_PAM1);

		# espionnage
		$S_PLM1 = ASM::$plm->getCurrentSession();
		ASM::$plm->newSession();
		ASM::$plm->load(array('id' => $rPlace));

		$sr = new SpyReport();
		$sr->rPlayer = CTR::$data->get('playerId');
		$sr->price = $price;
		$sr->rPlace = $rPlace;
		$sr->placeColor = ASM::$plm->get()->playerColor;

		$sr->typeOfBase = ASM::$plm->get()->typeOfBase;
		$sr->typeOfOrbitalBase = ;

		$sr->placeName = ASM::$plm->get()->baseName;
		$sr->points = ASM::$plm->get()->points;

		$sr->resources = ASM::$plm->get()->resources;
		$sr->commanders = "tableau sérialisé de malade";
		$sr->dSpying = Utils::now();

		//ASM::$srm->add($sr);

		ASM::$plm->changeSession($S_PLM1);
	} else {
		CTR::$alert->add('impossible de lancer un espionnage', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour espionner', ALERT_STD_FILLFORM);
}