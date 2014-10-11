<?php
include_once DEMETER;
#type
#rcolor
#options un tableau sérialisé d'options
#duration

$type = Utils::getHTTPData('type');
$rColor = Utils::getHTTPData('rcolor');
$options = Utils::getHTTPData('options');
$duration = Utils::getHTTPData('duration');

include_once DEMETER;
include_once ZEUS;

if ($type !== FALSE && $rColor !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
		$_CLM = ASM::$clm->getCurrentsession();
		ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
		if (ASM::$clm->get()->credits >= LawResources::getInfo($type, 'price')) {
			$law = new Law();

			$date = new DateTime(Utils::now());
			$law->dCreation = $date->format('Y-m-d H:i:s');
			$date->modify('+' . Law::VOTEDURATION . ' second');
			$law->dEndVotation = $date->format('Y-m-d H:i:s');
			$date->modify('+' . $duration . ' second');
			$law->dEnd = $date->format('Y-m-d H:i:s');

			$law->rColor = CTR::$data->get('playerInfo')->get('color');
			$law->type = $type;
			if ($options) {
				$law->options = $options;
			}
			$law->statement = 0;

			ASM::$lam->add($law);
		} else {
		 	CTR::$alert->add('Il n\'y a pas assez de crédits dans les caisses de l\'état.', ALERT_STD_ERROR);
	 	}
		ASM::$clm->changeSession($_CLM);
	} else {
		CTR::$alert->add('Vous n\' avez pas le droit de proposer cette loi.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}