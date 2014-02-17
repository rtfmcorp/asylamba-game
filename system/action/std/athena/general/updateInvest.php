<?php
include_once ATHENA;
# modify investments action

# int baseId 		id de la base orbitale
# string category 	catégorie
# int credit 		nouveau montant à investir

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
if (CTR::$get->exist('credit')) {
	$credit = CTR::$get->get('credit');
} elseif (CTR::$post->exist('credit')) {
	$credit = CTR::$post->get('credit');
} else {
	$credit = FALSE;
}
if (CTR::$get->exist('category')) {
	$category = CTR::$get->get('category');
} elseif (CTR::$post->exist('category')) {
	$category = CTR::$post->get('category');
} else {
	$category = FALSE;
}

if ($baseId !== FALSE AND $credit !== FALSE AND $category !== FALSE AND in_array($baseId, $verif)) { 
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
	if (ASM::$obm->size() == 1) {
		$base = ASM::$obm->get();
	} else {
		$cancel = TRUE;
		CTR::$alert->add('modification d\'investissement impossible - base inconnue', ALERT_STD_ERROR);
	}
	switch ($category) {
		case 'school':
			$base->setISchool($credit);
			CTR::$alert->add('L\'investissement dans l\'école de commandements de votre base ' . $base->getName() . ' a été modifié', ALERT_STD_SUCCESS);
			break;
		case 'antispy':
			$base->setIAntiSpy($credit);
			CTR::$alert->add('L\'investissement dans l\'anti-espionnage sur votre base ' . $base->getName() . ' a été modifié', ALERT_STD_SUCCESS);
			break;
		default:
			CTR::$alert->add('modification d\'investissement impossible', ALERT_STD_ERROR);
			CTR::$alert->add('catégorie invalide', ALERT_BUG_ERROR);
			break;
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour modifier un investissement', ALERT_STD_FILLFORM);
}
?>