<?php
# archive or unarchive action

# int id 			id du rapport

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$id = Utils::getHTTPData('id');

if ($id) {
	$S_SRM1 = ASM::$srm->getCurrentSession();
	ASM::$srm->newSession();
	ASM::$srm->load(array('id' => $id));
	$report = ASM::$srm->get();
	if (ASM::$srm->size() == 1) {

		if ($report->rPlayer == CTR::$data->get('playerId')) {
			ASM::$srm->deleteById($report->id);
			CTR::$alert->add('Rapport d\'espionnage supprimé', ALERT_STD_SUCCESS);
			CTR::redirect('fleet/view-spyreport');
		} else {
			CTR::$alert->add('Ce rapport ne vous appartient pas', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Ce rapport n\'existe pas', ALERT_STD_ERROR);
	}
	ASM::$srm->changeSession($S_SRM1);
} else {
	CTR::$alert->add('veuillez indiquer le numéro du rapport', ALERT_STD_ERROR);
}