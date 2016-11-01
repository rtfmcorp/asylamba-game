<?php
# int id 			id du rapport

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Ares\Model\Report;

$id = Utils::getHTTPData('id');

if ($id) {
	$S_LRM = ASM::$lrm->getCurrentSession();
	ASM::$lrm->newSession();
	ASM::$lrm->load(array('r.id' => $id));

	if (ASM::$lrm->size() > 0) {
		$report = ASM::$lrm->get();

		if ($report->rPlayerAttacker == CTR::$data->get('playerId')) {
			ASM::$lrm->get()->statementAttacker = Report::DELETED;
			CTR::redirect('fleet/view-archive');
		} elseif ($report->rPlayerDefender == CTR::$data->get('playerId')) {
			ASM::$lrm->get()->statementDefender = Report::DELETED;
			CTR::redirect('fleet/view-archive');
		} else {
			CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
	}

	ASM::$lrm->changeSession($S_LRM);	
} else {
	CTR::$alert->add('veuillez indiquer le numéro du rapport', ALERT_STD_ERROR);
}