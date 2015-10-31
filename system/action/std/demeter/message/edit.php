<?php
include_once DEMETER;

$content = Utils::getHTTPData('content');
$id = Utils::getHTTPData('id');

if ($content && $id) {
	$_FMM = ASM::$fmm->getCurrentSession();
	ASM::$fmm->newSession();
	ASM::$fmm->load(array('id' => $id));

	if (ASM::$fmm->size() > 0) {
		$m = ASM::$fmm->get();

		$_TOM = ASM::$tom->getCurrentSession();
		ASM::$tom->newSession();
		ASM::$tom->load(array('id' => $m->rTopic));

		$t = ASM::$tom->get();

		if (CTR::$data->get('playerId') == $m->rPlayer || (CTR::$data->get('playerInfo')->get('status') > 2 && $t->rForum != 20)) {
			$m->edit($content);
			$m->dLastModification = Utils::now();

			CTR::$alert->add('Message édité.', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Vous ne pouvez pas éditer ce message.', ALERT_STD_FILLFORM);
		}

		ASM::$tom->changeSession($_TOM);
	} else {
		CTR::$alert->add('Le message n\'existe pas.', ALERT_STD_FILLFORM);
	}

	ASM::$fmm->changeSession($_FMM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}