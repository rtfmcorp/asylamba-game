<?php
include HERMES;

if (CTR::$get->exist('thread')) {
	$thread = CTR::$get->get('thread');
} elseif (CTR::$post->exist('thread')) {
	$thread = CTR::$post->get('thread');
} else {
	$thread = FALSE;
}

if (CTR::$get->exist('page')) {
	$page = CTR::$get->get('page');
} elseif (CTR::$post->exist('page')) {
	$page = CTR::$post->get('page');
} else {
	$page = FALSE;
}

if ($page !== FALSE && $thread !== FALSE) {
	$S_MSM1 = ASM::$msm->getCurrentSession();
	ASM::$msm->newSession();
	ASM::$msm->load(array('thread' => $thread), array('dSending', 'DESC'), array(($page - 1) * MSM_STEPMESSAGE, MSM_STEPMESSAGE + 1));

	if (ASM::$msm->size() > 0) {
		$i = 0;
		for ($i; $i < ASM::$msm->size(); $i++) {
			$m = ASM::$msm->get($i);

			if ($m->getRPlayerReader() == 0 || $m->getRPlayerWriter() == 0) {
				echo '<div class="message left">';
					echo '<em class="name">Jean-Mi —</em>';
					echo $m->getContent();
					echo '<em class="option">— ' . Chronos::transform($m->getDSending()) . '</em>';
				echo '</div>';
			} else {
				$side  = ($m->getRPlayerWriter() == CTR::$data->get('playerId')) ? 'left' : 'right';
				echo '<div class="message color' . $m->getWriterColor() . ' ' . $side . '">';
					echo '<em class="name">' . $m->getWriterName() . ' —</em>';
					echo $m->getContent();
					echo '<em class="option">— ' . Chronos::transform($m->getDSending()) . '</em>';
				echo '</div>';
			}

			if ($i == MSM_STEPMESSAGE - 1) {
				break;
			}
		}

		if ($i < ASM::$msm->size() - 1) {
			echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-moremessage/thread-' . $thread . '/page-' . ($page + 1) . '">afficher plus de messages</a>';
		}
	}

	ASM::$msm->changeSession($S_MSM1);
}
?>