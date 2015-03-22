<?php
include_once ATHENA;
# cancel a commercial route action

# int base 			id (rPlace) de la base orbitale qui a proposé la route mais qui l'annule
# int route 		id de la route commerciale

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = Utils::getHTTPData('base');
$route = Utils::getHTTPData('route');

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$S_CRM1 = ASM::$crm->getCurrentSession();
	ASM::$crm->newSession(ASM_UMODE);
	ASM::$crm->load(array('id' => $route, 'rOrbitalBase' => $base, 'statement' => CRM_PROPOSED));
	if (ASM::$crm->get() && ASM::$crm->size() == 1) {
		$cr = ASM::$crm->get();

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $cr->getROrbitalBase()));
		$proposerBase = ASM::$obm->get();
		ASM::$obm->load(array('rPlace' => $cr->getROrbitalBaseLinked()));
		$linkedBase = ASM::$obm->get(1);

		//rend 80% des crédits investis
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		ASM::$pam->get()->increaseCredit(round($cr->getPrice() * CRM_CANCELROUTE));
		ASM::$pam->changeSession($S_PAM1);

		//notification
		$n = new Notification();
		$n->setRPlayer($linkedBase->getRPlayer());
		$n->setTitle('Route commerciale annulée');

		$n->addBeg()->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'))->addTxt(' a finalement retiré la proposition de route commerciale qu\'il avait faite entre ');
		$n->addLnk('map/base-' . $linkedBase->getRPlace(), $linkedBase->getName())->addTxt(' et ');
		$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName());
		$n->addEnd();
		ASM::$ntm->add($n);

		//destruction de la route
		ASM::$crm->deleteById($route);
		CTR::$alert->add('Route commerciale annulée. Vous récupérez les ' . CRM_CANCELROUTE * 100 . '% du montant investi.', ALERT_STD_SUCCESS);
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('impossible d\'annuler une route commerciale', ALERT_STD_ERROR);
	}
	ASM::$crm->changeSession($S_CRM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour annuler une route commerciale', ALERT_STD_FILLFORM);
}
?>