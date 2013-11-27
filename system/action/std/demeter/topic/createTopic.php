<?php
# créer un nouveau topic
# title
# content
# rforum

include_once DEMETER;

if (CTR::$get->exist('title')) {
	$title = CTR::$get->get('title');
} else if (CTR::$post->exist('title')) {
	$title = CTR::$post->get('title');
} else {
	$title = FALSE;
}

if (CTR::$get->exist('content')) {
	$content = CTR::$get->get('content');
} else if (CTR::$post->exist('content')) {
	$content = CTR::$post->get('content');
} else {
	$content = FALSE;
}

if (CTR::$get->exist('rforum')) {
	$rForum = CTR::$get->get('rforum');
} else if (CTR::$post->exist('rforum')) {
	$rForum = CTR::$post->get('rforum');
} else {
	$rForum = FALSE;
}

if ($title AND $content AND $rForum) {
	$topic = new ForumTopic();
	$topic->title = $title;
	$topic->rForum = $rForum;
	$topic->rPlayer = CTR::$data->get('playerId');
	$topic->rColor = CTR::$data->get('playerInfo')->get('color');

	$rTopic = ASM::$tom->add($topic);

	$message = new ForumMessage();
	$message->rPlayer = CTR::$data->get('playerId');
	$message->rTopic = $rTopic;
	$message->edit($content);

	ASM::$fmm->add($message);

	CTR::redirect('faction/view-forum/forum-' . $topic->rForum . '/topic-' . $topic->id . '/sftr-2');
	CTR::$alert->add('Topic créé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Manque d\information.', ALERT_STD_FILLFORM);
}