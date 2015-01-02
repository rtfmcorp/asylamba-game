<?php
include_once ATHENA;
# rename the orbital base action

# int baseid 		id (rPlayer) de la base orbitale
# string name 		new name for the orbital base

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$name = Utils::getHTTPData('name');


// protection du nouveau nom de la base
$p = new Parser();
$name = $p->protect($name);

if ($baseId !== FALSE AND $name !== FALSE AND in_array($baseId, $verif)) { 
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$obm->size() > 0) {
		$orbitalBase = ASM::$obm->get();

		$check = new CheckName();
		$check->setMaxLenght(20); 

		if ($check->checkLength($name)) {
			if ($check->checkChar($name)) {
				$orbitalBase->setName($name);

				for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
					if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
						CTR::$data->get('playerBase')->get('ob')->get($i)->add('name', $name);
					}
				}

				CTR::$alert->add('Le nom a été changé en ' . $name . ' avec succès', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('modification du nom de la base orbitale impossible - le nom contient des caractères non-autorisés', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('modification du nom de la base orbitale impossible - nom trop long ou trop court', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour changer le nom de la base orbitale', ALERT_STD_FILLFORM);
}
?>
