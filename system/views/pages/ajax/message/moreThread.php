<?php
# inclusion des modules
include_once HERMES;

# loading des objets
ASM::$msm->loadByRequest(
	'WHERE (rPlayerWriter = ? OR rPlayerReader = ?) ORDER BY dSending DESC',
	array(CTR::$data->get('playerId'), CTR::$data->get('playerId'))
);

$page = (CTR::$get->exist('page')) ? CTR::$get->get('page') : 1;

# affichage des messages
if (ASM::$msm->size() > 0) {
	$thread = array();
	for ($i = 0; $i < ASM::$msm->size(); $i++) {
		$message = ASM::$msm->get($i);
		if ($message->getRPlayerWriter() !== CTR::$data->get('playerId')) {
			$message->setReaded(1);
		}
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
	
	# thread component
	$j = 0;
	foreach ($thread as $k => $v) {
		$j++;
		if ($j >= MSM_STEPTHREAD + $page) {
			if ($j > MSM_STEPTHREAD + $page) {
				$moreThread_page = ++$page;
				include COMPONENT . 'hermes/moreThread.php';
				break;
			}

			$threadId_thread = $k;
			$lastMessage_thread = $v;
			$messages_thread = array();
			for ($i = 0; $i < ASM::$msm->size(); $i++) {
				if (ASM::$msm->get($i)->getThread() == $k) {
					$messages_thread[] = ASM::$msm->get($i);
				}
			}
			include COMPONENT . 'hermes/thread.php';

		}
	}
}
?>