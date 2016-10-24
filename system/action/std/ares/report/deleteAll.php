<?php

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Ares\Model\Report;

$S_LRM = ASM::$lrm->getCurrentSession();
ASM::$lrm->newSession();
ASM::$lrm->load(array('rPlayerAttacker' => CTR::$data->get('playerId'), 'statementAttacker' => Report::STANDARD));
ASM::$lrm->load(array('rPlayerDefender' => CTR::$data->get('playerId'), 'statementDefender' => Report::STANDARD));

if (ASM::$lrm->size() > 0) {
	for ($i = 0; $i < ASM::$lrm->size(); $i++) {
		if (ASM::$lrm->get($i)->rPlayerAttacker == CTR::$data->get('playerId')) {
			ASM::$lrm->get($i)->statementAttacker = Report::DELETED;
		} elseif (ASM::$lrm->get($i)->rPlayerDefender == CTR::$data->get('playerId')) {
			ASM::$lrm->get($i)->statementDefender = Report::DELETED;
		} else {
			CTR::$alert->add('Ces rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
	}
}

ASM::$lrm->changeSession($S_LRM);