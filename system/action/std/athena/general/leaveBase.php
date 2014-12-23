<?php
include_once ATHENA;
include_once GAIA;


// supprime toutes les routes
// 	-> avec message

# int id 		id (rPlace) de la base orbitale

$baseId = Utils::getHTTPData('id');

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (count($verif) > 1) {
	$_COM = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('rBase' => $baseId));
	$areAllFleetImmobile = TRUE;
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		if (ASM::$com->get($i)->statement == Commander::MOVING) {
			$areAllFleetImmobile = FALSE;
		}
	}
	if ($areAllFleetImmobile) {
		if ($baseId != FALSE && in_array($baseId, $verif)) {
			$_OBM = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlace' => $baseId));
			$_PLM = ASM::$plm->getCurrentSession();
			ASM::$plm->newSession();
			ASM::$plm->load(array('id' => $baseId));

			ASM::$obm->changeOwnerById($baseId, ASM::$obm->get(), ID_GAIA);
			ASM::$plm->get()->rPlayer = ID_GAIA;

			ASM::$obm->changeSession($_OBM);
			ASM::$plm->changeSession($_PLM);
			
			for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
				if ($verif[$i] == $baseId) {
					unset($verif[$i]);
					$verif = array_merge($verif);
				}
			}
			CTR::redirect(Format::actionBuilder('switchbase', ['base' => $verif[0]]));
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('toute les flottes de cette base doivent être immobiles', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($_COM);
} else {
	CTR::$alert->add('vous ne pouvez pas abandonner votre unique planète', ALERT_STD_ERROR);
}