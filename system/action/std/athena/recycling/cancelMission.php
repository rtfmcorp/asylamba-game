<?php
# cancel recycling mission

# int id 			id de la mission
# int place 		id de la base orbitale

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Athena\Model\RecyclingMission;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$missionId = Utils::getHTTPData('id');
$rPlace = Utils::getHTTPData('place');

if ($missionId !== FALSE AND $rPlace !== FALSE AND in_array($rPlace, $verif)) {
	
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $rPlace));

	if (ASM::$obm->size() == 1) {
		$base = ASM::$obm->get();


		$S_REM1 = ASM::$rem->getCurrentSession();
		ASM::$rem->newSession(ASM_UMODE);
		ASM::$rem->load(array('id' => $missionId, 'rBase' => $rPlace, 'statement' => RecyclingMission::ST_ACTIVE));

		if (ASM::$rem->size() == 1) {
			ASM::$rem->get()->statement = RecyclingMission::ST_BEING_DELETED;
			CTR::$alert->add('Ordre de mission annulé.', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('impossible de supprimer la mission.', ALERT_STD_ERROR);
		}
		ASM::$rem->changeSession($S_REM1);
	} else {
		CTR::$alert->add('cette base orbitale ne vous appartient pas', ALERT_STD_ERROR);
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour supprimer une mission de recyclage', ALERT_STD_FILLFORM);
}