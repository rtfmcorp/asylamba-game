<?php
include HERMES;

$thread 	= Utils::getHTTPData('thread');
$page 		= Utils::getHTTPData('page');

if ($page !== FALSE && $thread !== FALSE) {
	$S_MSM1 = ASM::$msm->getCurrentSession();
	ASM::$msm->newSession();
	ASM::$msm->load(array('thread' => $thread), array('dSending', 'DESC'), array(($page - 1) * MSM_STEPMESSAGE, MSM_STEPMESSAGE));

	if (ASM::$msm->size() > 0) {
		$i = 0;
		for ($i = 0; $i < ASM::$msm->size(); $i++) {
			$t = ASM::$msm->get($i);

			if ($t->getRPlayerReader() == CTR::$data->get('playerId')) {
				$t->setReaded(TRUE);
			}

			echo '<div class="message">';
				echo '<img src="' . MEDIA . 'avatar/medium/' . $t->getWriterAvatar() . '.png" alt="' . $t->getWriterName() . '" class="avatar" />';
				echo '<div class="content">';
					echo '<p class="text">';
						echo '≡ ' . $t->getWriterName() . '<br /><br />';
						echo $t->getContent();
					echo '</p>';
					echo '<p class="footer">';
						echo '— ' . Chronos::transform($t->getDSending());
					echo '</p>';
				echo '</div>';
			echo '</div>';
		}

		if (ASM::$msm->size() == MSM_STEPMESSAGE) {
			echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-moremessage/thread-' . $thread . '/page-' . ($page + 1) . '">';
				echo 'afficher plus de messages';
			echo '</a>';
		}
	}

	ASM::$msm->changeSession($S_MSM1);
}
?>