<?php
include_once ATHENA;
include_once ZEUS;
include_once GAIA;
# create recycling mission

# int id 			id de la mission
# int place 		id de la base orbitale
# int quantity 		recyclers quantity

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}
$missionId = Utils::getHTTPData('id');
$rPlace = Utils::getHTTPData('place');
$quantity = Utils::getHTTPData('quantity');

if ($rPlace !== FALSE AND $missionId !== FALSE AND $quantity !== FALSE AND in_array($rPlace, $verif)) {

	if ($quantity > 0) {
	
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $rPlace));

		if (ASM::$obm->size() == 1) {
			$base = ASM::$obm->get();

			$maxRecyclers = OrbitalBaseResource::getInfo(OrbitalBaseResource::RECYCLING, 'level', $base->levelRecycling, 'nbRecyclers');
			$usedRecyclers = 0;

			$S_REM1 = ASM::$rem->getCurrentSession();
			ASM::$rem->newSession();
			ASM::$rem->load(array('rBase' => $rPlace, 'statement' => array(RecyclingMission::ST_ACTIVE, RecyclingMission::ST_BEING_DELETED)));

			for ($i = 0; $i < ASM::$rem->size(); $i++) { 
				$usedRecyclers += ASM::$rem->get($i)->recyclerQuantity;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {

				$mission = NULL;
				for ($i = 0; $i < ASM::$rem->size(); $i++) {
					if (ASM::$rem->get($i)->id == $missionId && ASM::$rem->get($i)->statement == RecyclingMission::ST_ACTIVE) {
						$mission = ASM::$rem->get($i);
						break;
					}
				}
				if ($mission !== NULL) {
					$mission->addToNextMission += $quantity;
					CTR::$alert->add('Vos recycleurs ont bien été affectés, ils seront ajouté à la prochaine mission.', ALERT_STD_SUCCESS);
				} else {
					CTR::$alert->add('Il y a un problème, la mission est introuvable. Veuillez contacter un administrateur.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Vous n\'avez pas assez de recycleurs libres pour lancer cette mission.', ALERT_STD_ERROR);
			}
			ASM::$rem->changeSession($S_REM1);
		} else {
			CTR::$alert->add('cette base orbitale ne vous appartient pas', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('Ca va être dur de recycler avec autant peu de recycleurs. Entrez un nombre plus grand que zéro.', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour créer une mission de recyclage', ALERT_STD_FILLFORM);
}
?>