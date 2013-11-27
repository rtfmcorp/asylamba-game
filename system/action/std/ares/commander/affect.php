<?php
include_once ATHENA;
include_once ARES;

# affect a commander

# int id 	 		id du commandant

if (CTR::$get->exist('id')) {
	$commanderId = CTR::$get->get('id');
} elseif (CTR::$post->exist('id')) {
	$commanderId = CTR::$post->get('id');
} else {
	$commanderId = FALSE;
}


if ($commanderId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('id' => $commanderId, 'rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$com->size() == 1) {
		$commander = ASM::$com->get();
		
		# checker si on a assez de place !!!!!
		$S_COM2 = ASM::$com->newSession();
		ASM::$com->load(array('rBase' => $commander->getRBase(), 'statement' => COM_AFFECTED));
		$nbr = ASM::$com->size();

		ASM::$com->changeSession($S_COM2);

		if ($nbr < 3) {
			$commander->setDAffectation(Utils::now());
			$commander->setStatement(COM_AFFECTED);

			CTR::$alert->add('Votre commandant ' . $commander->getName() . ' a bien été affecté', ALERT_STD_SUCCESS);
			CTR::redirect('fleet/view-movement');
			
		} else {
			CTR::$alert->add('Votre base a dépassé la capacité limit de commandant en activité', ALERT_STD_ERROR);			
		}
	} else {
		CTR::$alert->add('Ce commandant n\'existe pas ou ne vous appartient pas', ALERT_STD_ERROR);
	}

	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('erreur dans le traitement de la requête', ALERT_BUG_ERROR);
}

?>