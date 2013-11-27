<?php
include_once ATHENA;
include_once PROMETHEE;
include_once ZEUS;
# building a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

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
if (CTR::$get->exist('techno')) {
	$techno = CTR::$get->get('techno');
} elseif (CTR::$post->exist('techno')) {
	$techno = CTR::$post->get('techno');
} else {
	$techno = FALSE;
}

if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if (TechnologyResource::isATechnology($techno)) {
		$technos = new Technology(CTR::$data->get('playerId'));
		$targetLevel = $technos->getTechnology($techno) + 1;
		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->newSession(ASM_UMODE);
		ASM::$tqm->load(array('rPlace' => $baseId), array('position'));
		for ($i = 0; $i < ASM::$tqm->size(); $i++) { 
			if (ASM::$tqm->get($i)->technology == $techno) {
				$targetLevel++;
			}
		}
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));
		$ob = ASM::$obm->get();

		$S_RSM1 = ASM::$rsm->getCurrentSession();
		ASM::$rsm->newSession(ASM_UMODE);
		ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

		if (TechnologyResource::haveRights($techno, 'resource', $targetLevel, $ob->getResourcesStorage())
			AND TechnologyResource::haveRights($techno, 'credit', $targetLevel, CTR::$data->get('playerInfo')->get('credit'))
			AND TechnologyResource::haveRights($techno, 'queue', ASM::$tqm->size())
			AND TechnologyResource::haveRights($techno, 'levelPermit', $targetLevel)
			AND TechnologyResource::haveRights($techno, 'technosphereLevel', $ob->getLevelTechnosphere())
			AND (TechnologyResource::haveRights($techno, 'research', $targetLevel, ASM::$rsm->get()->getResearchList()) === TRUE)) {

			// load du joueur
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(ASM_UMODE);
			ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

			// remise en ordre des positions
			if (ASM::$tqm->size() > 0) {
				for ($i = 0; $i < ASM::$tqm->size(); $i++) {
					$tq = ASM::$tqm->get($i);
					$tq->position = $i + 1;
				}
			}
			// construit la nouvelle techno
			$tq = new TechnologyQueue();
			$tq->rPlayer = CTR::$data->get('playerId');
			$tq->rPlace = $baseId;
			$tq->technology = $techno;
			$tq->targetLevel = $targetLevel;
			$time = TechnologyResource::getInfo($techno, 'time', $targetLevel);
			$bonus = $time * CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED) / 100;
			$tq->remainingTime = round($time - $bonus);
			$tq->position = ASM::$tqm->size() + 1;
			ASM::$tqm->add($tq);

			// débit resources
			$ob->decreaseResources(TechnologyResource::getInfo($techno, 'resource', $targetLevel));
			// débit des crédits
			ASM::$pam->get()->decreaseCredit(TechnologyResource::getInfo($techno, 'credit', $targetLevel));
			
			// ajout de l'event dans le contrôleur
			$date = Utils::now();
			for ($i = 0; $i < ASM::$tqm->size(); $i++) { 
				$date = Utils::addSecondsToDate($date, ASM::$tqm->get($i)->remainingTime);
			}
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);

			// alerte
			CTR::$alert->add('Développement de la technologie programmée', ALERT_STD_SUCCESS);
			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('les conditions ne sont pas remplies pour développer une technologie', ALERT_STD_ERROR);
		}
		ASM::$rsm->changeSession($S_RSM1);
		ASM::$obm->changeSession($S_OBM1);
		ASM::$tqm->changeSession($S_TQM1);
	} else {
		CTR::$alert->add('la technologie indiquée n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour développer une technologie', ALERT_STD_FILLFORM);
}
?>