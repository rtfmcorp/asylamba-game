<?php
include_once ATHENA;
# switch refinery mode (production/storage) action

# int baseId 		id de la base orbitale

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}

if ($baseId !== FALSE AND in_array($baseId, $verif)) { 
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
	if (ASM::$obm->size() == 1) {
		$base = ASM::$obm->get();
	} else {
		$cancel = TRUE;
		CTR::$alert->add('modification du mode de la raffinerie impossible - base inconnue', ALERT_STD_ERROR);
	}
	if ($base->getIsProductionRefinery() == 1) {
		$base->setIsProductionRefinery(0);
	} else {
		$base->setIsProductionRefinery(1);
		$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $base->getLevelRefinery(), 'storageSpace');
		if (CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE) > 0) {
			$storageSpace += ($storageSpace * CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE) / 100);
		}
		if ($base->getResourcesStorage() > $storageSpace) {
			$base->setResourcesStorage($storageSpace);
			CTR::$alert->add('en passant en mode production, vous avez diminué votre capacité de stockage, ce qui vous a fait perdre quelques ressources', ALERT_STD_INFO);
		}
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour changer le mode de la raffinerie', ALERT_STD_FILLFORM);
}
?>