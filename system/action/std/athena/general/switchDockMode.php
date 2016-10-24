<?php
# switch dock mode (production/storage) action

# int baseId 		id de la base orbitale
# int dock 			dock number

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$dock = Utils::getHTTPData('dock');


if ($baseId !== FALSE AND $dock !== FALSE AND in_array($baseId, $verif)) { 
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
	if (ASM::$obm->size() == 1) {
		$base = ASM::$obm->get();
	} else {
		$cancel = TRUE;
		CTR::$alert->add('modification du mode du dock impossible - base inconnue', ALERT_STD_ERROR);
	}
	switch ($dock) {
		case 1:
			if ($base->getIsProductionDock1() == 1) {
				$base->setIsProductionDock1(0);
			} else {
				$base->setIsProductionDock1(1);
			}
			break;
		case 2:
			if ($base->getIsProductionDock2() == 1) {
				$base->setIsProductionDock2(0);
			} else {
				$base->setIsProductionDock2(1);
			}
			break;
		default:
			CTR::$alert->add('modification du mode du dock impossible - dock inconnue', ALERT_STD_ERROR);
			break;
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour changer le mode du dock', ALERT_STD_FILLFORM);
}