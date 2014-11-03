<?php
#credit

include_once DEMETER;

$credit = Utils::getHTTPData('credit');

if ($credit) {
	$S_CLM = ASM::$clm->getCurrentSession();
	ASM::$clm->newSession();
	ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
	$S_PAM = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	$credit = ($credit > ASM::$pam->get()->credit) ? ASM::$pam->get()->credit : $credit;
	ASM::$pam->get()->decreaseCredit($credit);
	ASM::$clm->get()->credits += $credit;

	if (ASM::$pam->get()->rColor == ColorResource::CARDAN) {
		ASM::$pam->get()->factionPoint += Color::POINTDONATE + round(Color::COEFPOINTDONATE * $credit);
	}

	CTR::$alert->add('Vous venez de remplir les caisse de votre faction de ' . $credit . ' crédit' . Format::addPlural($credit) . ' :)', ALERT_STD_SUCCESS);
	
	ASM::$pam->changeSession($S_PAM);
	ASM::$clm->changeSession($S_CLM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}