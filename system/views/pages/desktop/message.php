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

	if (!(CTR::$get->equal('mode', 'create') || CTR::$get->exist('thread'))) {
		include COMPONENT . 'default.php';
	}
echo '</div>';
?>