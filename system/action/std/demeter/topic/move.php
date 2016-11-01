<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Promethee\Resource\ForumResources;

$rForum = Utils::getHTTPData('rforum');
$id = Utils::getHTTPData('id');

if ($rForum !== FALSE && $id !== FALSE) {
	$_TOM = ASM::$tom->getCurrentSession();
	ASM::$tom->newSession();
	ASM::$tom->load(array('id' => $id));

	if (ASM::$tom->size() > 0) {
		if (CTR::$data->get('playerInfo')->get('status') > 2) {
			$isOk = FALSE;

			for ($i = 1; $i < ForumResources::size() + 1; $i++) { 
				if (ForumResources::getInfo($i, 'id') == $rForum) {
					$isOk = TRUE;
					break;
				}
			}

			if ($isOk) {
				ASM::$tom->get()->rForum = $rForum;
				ASM::$tom->get()->dLastModification = Utils::now();

				CTR::redirect('faction/view-forum/forum-' . $rForum . '/topic-' . ASM::$tom->get()->id);
			} else {
				CTR::$alert->add('Le forum de destination n\'existe pas', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('Vous n\'avez pas les droits pour cette opération', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('Ce sujet n\'existe pas', ALERT_STD_FILLFORM);	
	}
	ASM::$tom->changeSession($_TOM);
} else {
	CTR::$alert->add('Manque d\'information', ALERT_STD_FILLFORM);
}