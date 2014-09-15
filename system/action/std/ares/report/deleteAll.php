<?php

include_once ARES;

$S_LRM = ASM::$lrm->getCurrentSession();
ASM::$lrm->newSession();
ASM::$lrm->load(array('rPlayerAttacker' => CTR::$data->get('playerId')));
ASM::$lrm->load(array('rPlayerDefender' => CTR::$data->get('playerId')));
if (ASM::$lrm->size() > 0) {
	for ($i = 0; $i < ASM::$lrm->size(); $i++) {
		if ($ASM::$lrm->get($i)->rPlayerAttacker == CTR::$data->get('playerId')) {
			ASM::$lrm->get($i)->statementAttacker = Report::DELETED;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} elseif ($ASM::$lrm->get($i)->rPlayerDefender == CTR::$data->get('playerId')) {
			ASM::$lrm->get($i)->statementDefender = Report::DELETED;
			CTR::$alert->add('Rapport supprimé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
	}
} else {
	CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
}
ASM::$lrm->changeSession($S_LRM);