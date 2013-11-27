<?php
include_once ATHENA;
# refuse a commercial route action

# int base 			id (rPlace) de la base orbitale qui refuse la route
# int route 		id de la route commerciale

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('base')) {
	$base = CTR::$get->get('base');
} elseif (CTR::$post->exist('base')) {
	$base = CTR::$post->get('base');
} else {
	$base = FALSE;
}
if (CTR::$get->exist('route')) {
	$route = CTR::$get->get('route');
} elseif (CTR::$post->exist('route')) {
	$route = CTR::$post->get('route');
} else {
	$route = FALSE;
}

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$S_CRM1 = ASM::$crm->getCurrentSession();
	ASM::$crm->newSession(ASM_UMODE);
	ASM::$crm->load(array('id'=>$route, 'rOrbitalBaseLinked' => $base, 'statement' => CRM_PROPOSED));
	if (ASM::$crm->get() && ASM::$crm->size() == 1) {
		$cr = ASM::$crm->get();

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $cr->getROrbitalBase()));
		$proposerBase = ASM::$obm->get();
		ASM::$obm->load(array('rPlace' => $cr->getROrbitalBaseLinked()));
		$refusingBase = ASM::$obm->get(1);

		//rend les crédits au proposant
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('id' => $proposerBase->getRPlayer()));
		ASM::$pam->get()->increaseCredit(intval($cr->getPrice()));

		//notification
		$n = new Notification();
		$n->setRPlayer($proposerBase->getRPlayer());
		$n->setTitle('Route commerciale refusée');
		$n->addBeg()->addLnk('diary/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'))->addTxt(' a refusé la route commerciale proposée entre ');
		$n->addLnk('map/place-' . $refusingBase->getRPlace(), $refusingBase->getName())->addTxt(' et ');
		$n->addLnk('map/base-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt('.');
		$n->addSep()->addTxt('Les ' . Format::numberFormat($cr->getPrice()) . ' crédits bloqués sont à nouveau disponibles.');
		$n->addEnd();
		ASM::$ntm->add($n);

		//destruction de la route
		ASM::$crm->deleteById($route);
		CTR::$alert->add('Route commerciale refusée', ALERT_STD_SUCCESS);
		ASM::$obm->changeSession($S_OBM1);
		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('impossible de refuser une route commerciale', ALERT_STD_ERROR);
	}
	ASM::$crm->changeSession($S_CRM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour refuser une route commerciale', ALERT_STD_FILLFORM);
}
?>