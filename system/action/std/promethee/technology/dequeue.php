<?php
include_once ATHENA;
include_once ZEUS;
include_once PROMETHEE;
# dequeue a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('techno')) {
	$techno = CTR::$get->get('techno');
} elseif (CTR::$post->exist('techno')) {
	$techno = CTR::$post->get('techno');
} else {
	$techno = FALSE;
}

if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if (TechnologyResource::isATechnology($techno)) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
		$ob = ASM::$obm->get();

		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->newSession(ASM_UMODE);
		ASM::$tqm->load(array('rPlace' => $baseId, 'technology' => $techno));
		$queue = ASM::$tqm->get();
		$id = $queue->id;
		$targetLevel = $queue->targetLevel;

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		$player = ASM::$pam->get();

		for ($i = 1; $i < ASM::$tqm->size(); $i++) {
			$queue = ASM::$tqm->get($i);
			if ($queue->targetLevel > $targetLevel) {
				$id = $queue->id;
				$targetLevel = $queue->targetLevel;
			}
		}
		ASM::$tqm->deleteById($id);

		// rends les ressources et les crédits au joueur
		$resourcePrice = TechnologyResource::getInfo($techno, 'resource', $targetLevel);
		$resourcePrice *= TQM_RESOURCERETURN;
		$ob->increaseResources($resourcePrice);
		$creditPrice = TechnologyResource::getInfo($techno, 'credit', $targetLevel);
		$creditPrice *= TQM_CREDITRETURN;
		$player->increaseCredit($creditPrice);
		CTR::$alert->add('Construction annulée, vous récupérez le ' . TQM_RESOURCERETURN * 100 . '% des ressources ainsi que le ' . TQM_CREDITRETURN * 100 . '% des crédits investis pour le développement', ALERT_STD_SUCCESS);

		ASM::$pam->changeSession($S_PAM1);
		ASM::$obm->changeSession($S_OBM1);
		ASM::$tqm->changeSession($S_TQM1);
	} else {
		CTR::$alert->add('la technologie indiquée n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour annuler le développement d\'une technologie', ALERT_STD_FILLFORM);
}
?>