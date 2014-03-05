<?php
include_once HERMES;
# write message action

# int id 			id du destinataire
# int thread 		id du thread
# int name 			nom du destinataire
# string message 	message à envoyer

if (CTR::$get->exist('id')) {
	$id = CTR::$get->get('id');
} elseif (CTR::$post->exist('id')) {
	$id = CTR::$post->get('id');
} else {
	$id = FALSE;
}
if (CTR::$get->exist('thread')) {
	$thread = CTR::$get->get('thread');
} elseif (CTR::$post->exist('thread')) {
	$thread = CTR::$post->get('thread');
} else {
	$thread = FALSE;
}
if (CTR::$get->exist('name')) {
	$name = CTR::$get->get('name');
} elseif (CTR::$post->exist('name')) {
	$name = CTR::$post->get('name');
} else {
	$name = FALSE;
}
if (CTR::$get->exist('message')) {
	$message = CTR::$get->get('message');
} elseif (CTR::$post->exist('message')) {
	$message = CTR::$post->get('message');
} else {
	$message = FALSE;
}

// protection des inputs
$p = new Parser();
$name = $p->protect($name);
$message = $p->parse($message);

if (($id OR $thread OR $name) AND $message !== '') {
	$m = new Message();
	$m->setRPlayerWriter(CTR::$data->get('playerId'));
	$m->setDSending(Utils::now());
	$m->setContent($message);

	$S_MSM1 = ASM::$msm->getCurrentSession();
	ASM::$msm->newSession(ASM_UMODE);

	if ($thread) {
		ASM::$msm->load(array('thread' => $thread), array(), array(0, 1));
		if (ASM::$msm->get()) {
			if ($id) {
				$m->setRPlayerReader($id);
			} else {
				if (ASM::$msm->get()->getRPlayerReader() == CTR::$data->get('playerId')) {
					$m->setRPlayerReader(ASM::$msm->get()->getRPlayerWriter());
				} else {
					$m->setRPlayerReader(ASM::$msm->get()->getRPlayerReader());
				}
			}
			$m->setThread($thread);
			ASM::$msm->add($m);
			CTR::$alert->add('message envoyé', ALERT_STD_SUCCESS);
		} else {
			CTR::$alert->add('création de message impossible', ALERT_STD_ERROR);
			CTR::$alert->add('thread inexistant', ALERT_BUG_ERROR);
		}
	} else {
		$cancel = FALSE;
		if (!$id AND $name) {
			include_once ZEUS;
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(ASM_UMODE);
			ASM::$pam->load(array('name' => $name));
			if (ASM::$pam->get()) {
				$id = ASM::$pam->get()->getId();
			} else {
				$cancel = TRUE;
				CTR::$alert->add('création de message impossible - joueur inconnu', ALERT_STD_ERROR);
			}
			ASM::$pam->changeSession($S_PAM1);
		}
		if (!$cancel) {
			ASM::$msm->load(array('rPlayerReader' => $id, 'rPlayerWriter' => CTR::$data->get('playerId')));
			if (ASM::$msm->get()) {
				$m->setThread(ASM::$msm->get()->getThread());
				$m->setRPlayerReader($id);
				ASM::$msm->add($m);
				CTR::$alert->add('message envoyé', ALERT_STD_SUCCESS);
			} else {
				ASM::$msm->load(array('rPlayerWriter' => $id, 'rPlayerReader' => CTR::$data->get('playerId')));
				if (ASM::$msm->get()) {
					$m->setThread(ASM::$msm->get()->getThread());
					$m->setRPlayerReader($id);
					ASM::$msm->add($m);
					CTR::$alert->add('message envoyé', ALERT_STD_SUCCESS);
				} else {
					//création d'n nouveau thread
					$db = DataBase::getInstance();
					$qr = $db->prepare('SELECT MAX(thread) AS maxThread FROM message');
					$qr->execute();
					if ($aw = $qr->fetch()) {
						$m->setThread($aw['maxThread'] + 1);
						$m->setRPlayerReader($id);
						ASM::$msm->add($m);
						CTR::$alert->add('message envoyé', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('création de message impossible', ALERT_STD_ERROR);
						CTR::$alert->add('problème avec MAX(thread)', ALERT_BUG_ERROR);
					}
				}
			}
		}
	}
	ASM::$msm->changeSession($S_MSM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour écrire un message', ALERT_STD_FILLFORM);
}
?>