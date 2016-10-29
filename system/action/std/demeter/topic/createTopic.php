<?php
# créer un nouveau topic
# title
# content
# rforum

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Demeter\Model\Forum\ForumTopic;
use Asylamba\Modules\Demeter\Model\Forum\ForumMessage;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

$title = Utils::getHTTPData('title');
$content = Utils::getHTTPData('content');
$rForum = Utils::getHTTPData('rforum');


if ($title !== FALSE AND $content !== FALSE AND $rForum !== FALSE) {
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

	# tutorial
	if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
		switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
			case TutorialResource::FACTION_FORUM :
				TutorialHelper::setStepDone();						
				break;
		}
	}

	if (DATA_ANALYSIS) {
		$db = Database::getInstance();
		$qr = $db->prepare('INSERT INTO 
			DA_SocialRelation(`from`, type, message, dAction)
			VALUES(?, ?, ?, ?)'
		);
		$qr->execute([CTR::$data->get('playerId'), 1, $content, Utils::now()]);
	}

	CTR::redirect('faction/view-forum/forum-' . $topic->rForum . '/topic-' . $topic->id . '/sftr-2');
	CTR::$alert->add('Topic créé.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('Manque d\information.', ALERT_STD_FILLFORM);
}