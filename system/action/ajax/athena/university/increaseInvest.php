<?php
include_once ATHENA;
# increase university investment action

# int baseid 		id de la base orbitale
# int category 	 	catégorie ('natural', 'life', 'social' ou 'informatic')
# int quantity		percentage of increasment (facultatif, si non-défini, $quantity = 1)

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
if (CTR::$get->exist('category')) {
	$category = CTR::$get->get('category');
} elseif (CTR::$post->exist('category')) {
	$category = CTR::$post->get('category');
} else {
	$category = FALSE;
}
if (CTR::$get->exist('quantity')) {
	$quantity = CTR::$get->get('quantity');
} elseif (CTR::$post->exist('quantity')) {
	$quantity = CTR::$post->get('quantity');
} else {
	$quantity = FALSE;
}

// protection des inputs
$p = new Parser();
$category = $p->protect($category);

if ($baseId !== FALSE AND $category !== FALSE AND $category !== '' AND in_array($baseId, $verif)) {
	if (in_array($category, array('natural', 'life', 'social', 'informatic'))) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
		$ob = ASM::$obm->get();

		$totalInvest = $ob->getPartNaturalSciences() + $ob->getPartLifeSciences() + $ob->getPartSocialPoliticalSciences() + $ob->getPartInformaticEngineering();
		if ($totalInvest < 100) {
			if ($quantity === FALSE) {
				$quantity = 1;
			}
			if ($totalInvest + $quantity > 100) {
				$quantity = 100 - $totalInvest;
			}
			switch ($category) {
				case 'natural' :
					$faculty = 'Sciences Naturelles'; 
					$ob->setPartNaturalSciences($ob->getPartNaturalSciences() + $quantity);
					break;
				case 'life' : 
					$faculty = 'Sciences de la Vie';
					$ob->setPartLifeSciences($ob->getPartLifeSciences() + $quantity);
					break;
				case 'social' :
					$faculty = 'Sciences Sociales et Politiques'; 
					$ob->setPartSocialPoliticalSciences($ob->getPartSocialPoliticalSciences() + $quantity);
					break;
				case 'informatic' : 
					$faculty = 'Ingénierie Informatique';
					$ob->setPartInformaticEngineering($ob->getPartInformaticEngineering() + $quantity);
					break;
			}
		} else {
			CTR::$alert->add('Vous devez d\'abord libérer des pourcentages avant de les attribuer ailleurs', ALERT_STD_INFO);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
	CTR::$alert->add('Changement d\'investissement impossible - faculté inconnue', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour augmenter l\'investissement', ALERT_STD_FILLFORM);
}
?>