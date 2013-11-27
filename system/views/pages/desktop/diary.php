<?php
# background paralax
# [a modifier]
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'profilElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ZEUS;
	include_once ATHENA;
	include_once HERMES;

	# loading des objets
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array(
		'id' => CTR::$get->get('player'),
		'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY, PAM_BANNED)
	));

	$S_MSM1 = ASM::$msm->getCurrentSession();
	ASM::$msm->newSession();
	ASM::$msm->loadByRequest(
		'WHERE ((rPlayerWriter = ? AND rPlayerReader = ?) OR (rPlayerWriter = ? AND rPlayerReader = ?)) ORDER BY dSending DESC',
		array(CTR::$get->get('player'), CTR::$data->get('playerId'), CTR::$data->get('playerId'), CTR::$get->get('player'))
	);

	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => CTR::$get->get('player')), array('dCreation', 'ASC'));

	if (ASM::$pam->size() == 1) {
		# diaryRoplay component
		$player_diaryRoplay = ASM::$pam->get(0);
		include COMPONENT . 'zeus/diaryRoplay.php';

		# diaryBases component
		$ob_diaryBases = ASM::$obm->getAll();
		include COMPONENT . 'zeus/diaryBases.php';

		if (ASM::$msm->size() > 0) {
			$threadId_thread = ASM::$msm->get()->getThread();
			$messages_thread = array();
			for ($i = 0; $i < ASM::$msm->size(); $i++) {
				$messages_thread[] = ASM::$msm->get($i);
			}
			include COMPONENT . 'hermes/thread.php';
		}
	} else {
		CTR::redirect('profil');
	}

	ASM::$obm->changeSession($S_OBM1);
	ASM::$msm->changeSession($S_MSM1);
	ASM::$pam->changeSession($S_PAM1);
echo '</div>';
?>