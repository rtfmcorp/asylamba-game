<?php
# créer un nouveau topic
# content
# rtopic

include_once DEMETER;

$content = Utils::getHTTPData('content');
$rTopic = Utils::getHTTPData('rtopic');


if ($rTopic AND $content) {
	$S_TOM_1 = ASM::$tom->getCurrentSession();
	ASM::$tom->load(array('id' => $rTopic));

	if (ASM::$tom->size() == 1) {
		$message = new ForumMessage();
		$message->rPlayer = CTR::$data->get('playerId');
		$message->rTopic = $rTopic;
		$message->edit($content);

		ASM::$fmm->add($message);

		ASM::$tom->get()->dLastMessage = Utils::now();
		CTR::$alert->add('Message créé.', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Le topic n\'existe pas.', ALERT_STD_ERROR);
	}
	ASM::$tom->changeSession($S_TOM_1);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}