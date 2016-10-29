<?php
# écrire un message dans un topic du forum de faction
# content
# rtopic

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Demeter\Model\Forum\ForumMessage;

$content = Utils::getHTTPData('content');
$rTopic  = Utils::getHTTPData('rtopic');

if ($rTopic AND $content) {
	$S_TOM_1 = ASM::$tom->getCurrentSession();
	ASM::$tom->load(array('id' => $rTopic));

	if (ASM::$tom->size() == 1) {
		if (!ASM::$tom->get()->isClosed) {
			$message = new ForumMessage();
			$message->rPlayer = CTR::$data->get('playerId');
			$message->rTopic = $rTopic;
			$message->dCreation = Utils::now();
			$message->dLastMessage = Utils::now();

			$message->edit($content);
			
			ASM::$fmm->add($message);

			ASM::$tom->get()->dLastMessage = Utils::now();

			# tutorial
			if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
				switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
					case TutorialResource::FACTION_FORUM :
						TutorialHelper::setStepDone();						
						break;
				}
			}

			if (ASM::$tom->get()->rForum != 30) {
				CTR::redirect('faction/view-forum/forum-' . ASM::$tom->get()->rForum . '/topic-' . $rTopic . '/sftr-2');
			}

			if (DATA_ANALYSIS) {
				$db = Database::getInstance();
				$qr = $db->prepare('INSERT INTO 
					DA_SocialRelation(`from`, type, message, dAction)
					VALUES(?, ?, ?, ?)'
				);
				$qr->execute([CTR::$data->get('playerId'), 1, $content, Utils::now()]);
			}

			CTR::$alert->add('Message créé.', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('Ce sujet est fermé.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Le topic n\'existe pas.', ALERT_STD_ERROR);
	}

	ASM::$tom->changeSession($S_TOM_1);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}