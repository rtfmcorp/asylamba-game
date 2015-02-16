<?php
include_once ATHENA;
# modify investments action

# int baseId 		id de la base orbitale
# string category 	catégorie
# int credit 		nouveau montant à investir

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$credit = Utils::getHTTPData('credit');
$category = Utils::getHTTPData('category');


if ($baseId !== FALSE AND $credit !== FALSE AND $category !== FALSE AND in_array($baseId, $verif)) { 
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
		if (ASM::$obm->size() == 1) {
			$base = ASM::$obm->get();
			switch ($category) {
				case 'school':
					if ($credit <= 50000) {
						$base->setISchool($credit);
						CTR::$alert->add('L\'investissement dans l\'école de commandement de votre base ' . $base->getName() . ' a été modifié', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('La limite maximale d\'investissement dans l\'école de commandement est de 50\'000 crédits.', ALERT_STD_ERROR);
					} 
					break;
				case 'antispy':
					if ($credit <= 100000) {
						$base->setIAntiSpy($credit);
						CTR::$alert->add('L\'investissement dans l\'anti-espionnage sur votre base ' . $base->getName() . ' a été modifié', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('La limite maximale d\'investissement dans l\'anti-espionnage est de 100\'000 crédits.', ALERT_STD_ERROR);
					} 
					break;
				default:
					CTR::$alert->add('modification d\'investissement impossible', ALERT_STD_ERROR);
					CTR::$alert->add('catégorie invalide', ALERT_BUG_ERROR);
					break;
			}
		} else {
			CTR::$alert->add('modification d\'investissement impossible - base inconnue', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour modifier un investissement', ALERT_STD_FILLFORM);
}
?>