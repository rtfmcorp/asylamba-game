<?php
include_once DEMETER;

$id = Utils::getHTTPData('id');

if ($id) {
	$_TOM = ASM::$tom->getCurrensession();
	ASM::$tom->load(array('id' => $id));
	if (CTR::$data->get('playerInfo')->get('status') > 2)) {
		if (ASM::$tom->get()->isClosed = 1) {
			ASM::$tom->get()->isClosed = 0;
		} else {
			ASM::$tom->get()->isClosed = 1;
		}
	}
	ASM::$tom->changeSession($_TOM);
	CTR::redirect('faction/view-forum/forum-' . $topic->rForum . '/topic-' . ASM::$tom->id . '/sftr-2');
} else {
	CTR::$alert->add('Manque d\information.', ALERT_STD_FILLFORM);
}