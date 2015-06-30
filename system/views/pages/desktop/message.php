<?php
# background paralax
echo '<div id="background-paralax" class="message"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once HERMES;

	# liste des conv's
	$limit = [0, Conversation::CONVERSATION_BY_PAGE];
	$display = CTR::$get->equal('mode', 'archive')
		? ConversationUser::CS_ARCHIVED
		: ConversationUser::CS_DISPLAY;

	# chargement de toutes les conversations
	ASM::$cvm->newSession();
	ASM::$cvm->load(
		['cu.rPlayer' => CTR::$data->get('playerId'), 'cu.convStatement' => $display],
		['c.dLastMessage', 'DESC'],
		$limit
	);

	include COMPONENT . 'conversation/list.php';

	if (CTR::$get->exist('conversation')) {
		if (CTR::$get->equal('conversation', 'new')) {
			include COMPONENT . 'conversation/create.php';
		} else {
			# chargement d'une conversation
			ASM::$cvm->newSession();
			ASM::$cvm->load(
				['c.id' => CTR::$get->get('conversation'), 'cu.rPlayer' => CTR::$data->get('playerId')]
			);

			if (ASM::$cvm->size() == 1) {
				# chargement des infos d'une conversation
				ASM::$cum->newSession();
				ASM::$cum->load(['c.rConversation' => CTR::$get->get('conversation')]);

				# dernière vue

				# mis à jour de l'heure de la dernière vue
				for ($i = 0; $i < ASM::$cum->size(); $i++) { 
					if (ASM::$cum->get($i)->rPlayer == CTR::$data->get('playerId')) {
						$dPlayerLastMessage = ASM::$cum->get($i)->dLastView;
						$currentUser = ASM::$cum->get($i);
						
						ASM::$cum->get($i)->dLastView = Utils::now();
					}
				}

				# chargement des messages
				ASM::$cme->newSession();
				ASM::$cme->load(
					['c.rConversation' => CTR::$get->get('conversation')],
					['c.dCreation', 'DESC'],
					[0, ConversationMessage::MESSAGE_BY_PAGE]
				);

				include COMPONENT . 'conversation/messages.php';
				include COMPONENT . 'conversation/manage.php';
			} else {
				CTR::redirect('message');
			}
		}
	} else {
		include COMPONENT . 'conversation/new.php';
	}


	# NOTIFICATION
	$S_NTM1 = ASM::$ntm->getCurrentSession();

	$C_NTM1 = ASM::$ntm->newSession();
	ASM::$ntm->load(
		array('rPlayer' => CTR::$data->get('playerId'), 'archived' => 0),
		array('dSending', 'DESC'),
		array(0, 50)
	);
	include COMPONENT . 'notif/last.php';

	$C_NTM2 = ASM::$ntm->newSession();
	ASM::$ntm->load(
		array('rPlayer' => CTR::$data->get('playerId'), 'archived' => 1),
		array('dSending', 'DESC'),
		array(0, 50)
	);

	if (ASM::$ntm->size() > 0) {
		include COMPONENT . 'notif/archived.php';
	}

	ASM::$ntm->changeSession($S_NTM1);
echo '</div>';

/*
	# MESSAGE
	$S_MSM1 = ASM::$msm->getCurrentSession();

	$C_MSM1 = ASM::$msm->newSession();
	ASM::$msm->loadByRequest(
		'WHERE (rPlayerWriter = ? OR rPlayerReader = ?) ORDER BY dSending DESC',
		array(CTR::$data->get('playerId'), CTR::$data->get('playerId'))
	);

	if (ASM::$msm->size() > 0) {
		$thread = array();
		for ($i = 0; $i < ASM::$msm->size(); $i++) {
			$message = ASM::$msm->get($i);

			if (!in_array($message->getThread(), array_keys($thread))) {
				$thread[$message->getThread()] = $message->getDSending();
			} else {
				if (strtotime($thread[$message->getThread()]) < strtotime($message->getDSending())) {
					$thread[$message->getThread()] = $message->getDSending();
				}
			}
		}
		uasort($thread, function($a, $b) {
			if (strtotime($a) == strtotime($b)) { return 0; }
			return (strtotime($a) > strtotime($b)) ? -1 : 1;
		});

		$threads = array();
		$j = 0;

		foreach ($thread as $k => $v) {
			$threads[$j]['id'] = $k;
			$threads[$j]['last'] = $v;

			if (!isset($threads[$j]['nb'])) {
				$threads[$j]['nb'] = 0;
			}

			if (!isset($threads[$j]['readed'])) {
				$threads[$j]['readed'] = TRUE;
			}

			for ($i = 0; $i < ASM::$msm->size(); $i++) {
				if (ASM::$msm->get($i)->getThread() == $k) {
					$threads[$j]['content'] = ASM::$msm->get($i);
					$threads[$j]['nb']++;

					if ($threads[$j]['readed'] && ASM::$msm->get($i)->getRPlayerReader() == CTR::$data->get('playerId')) {
						$threads[$j]['readed'] = ASM::$msm->get($i)->getReaded();
					}
				}
			}

			$j++;
		}
		
		include COMPONENT . 'message/last.php';
	}

	if (CTR::$get->equal('mode', 'create')) {
		$sendToId = NULL;
		$sendToName = NULL;

		if (CTR::$get->exist('sendto')) {
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => CTR::$get->get('sendto')));

			if (ASM::$pam->size() == 1) {
				$sendToId   = ASM::$pam->get()->id;
				$sendToName = ASM::$pam->get()->name;
			}

			ASM::$pam->changeSession($S_PAM1);
		}

		include COMPONENT . 'message/new.php';
	}

	if (CTR::$get->exist('thread')) {
		$C_MSM2 = ASM::$msm->newSession();
		ASM::$msm->loadByRequest(
			'WHERE thread = ? AND (rPlayerWriter = ? OR rPlayerReader = ?) ORDER BY dSending DESC LIMIT 0, ?',
			array(CTR::$get->get('thread'), CTR::$data->get('playerId'), CTR::$data->get('playerId'), MSM_STEPMESSAGE)
		);

		if (ASM::$msm->size() >= 1) {
			include COMPONENT . 'message/thread.php';
		} else {
			CTR::$alert->add('Cette conversation ne vous appartient pas ou n\'existe pas');
			CTR::redirect('message');
		}
	}

	ASM::$msm->changeSession($S_MSM1);

*/