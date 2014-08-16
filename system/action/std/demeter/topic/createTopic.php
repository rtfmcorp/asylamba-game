<?php
# créer un nouveau topic
# title
# content
# rforum

include_once DEMETER;

$title = Utils::getHTTPData('title');
$content = Utils::getHTTPData('content');
$rForum = Utils::getHTTPData('rforum');


if ($title AND $content AND $rForum) {
	$topic = new ForumTopic();
	$topic->title = $title;
	$topic->rForum = $rForum;
	$topic->rPlayer = CTR::$data->get('playerId');
	$topic->rColor = CTR::$data->get('playerInfo')->get('color');
	$topic->dCreation = Utils::now();
	$topic->dLastMessage = Utils::now();

	$rTopic = ASM::$tom->add($topic);

	$message = new ForumMessage();
	$message->rPlayer = CTR::$data->get('playerId');
	$message->rTopic = $rTopic;
	$message->edit($content);
	$message->dCreation = Utils::now();
	$message->dLastMessage = Utils::now();

	ASM::$fmm->add($message);

	CTR::redirect('faction/view-forum/forum-' . $topic->rForum . '/topic-' . $topic->id . '/sftr-2');
	CTR::$alert->add('Topic créé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Manque d\information.', ALERT_STD_FILLFORM);
}