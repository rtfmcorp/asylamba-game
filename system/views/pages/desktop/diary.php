<?php
# background paralax
# [a modifier]
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'profilElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ZEUS;
	include_once ATHENA;
	include_once HERMES;

	$player = CTR::$get->exist('player')
		? CTR::$get->get('player')
		: CTR::$data->get('playerId');
	$ishim = CTR::$get->exist('player') || CTR::$get->get('player') !== CTR::$data->get('playerId')
		? FALSE
		: TRUE;

	# loading des objets
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array(
		'id' => $player,
		'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY, PAM_BANNED)
	));

	$S_MSM1 = ASM::$msm->getCurrentSession();
	ASM::$msm->newSession();
	ASM::$msm->loadByRequest(
		'WHERE ((rPlayerWriter = ? AND rPlayerReader = ?) OR (rPlayerWriter = ? AND rPlayerReader = ?)) ORDER BY dSending DESC',
		array($player, CTR::$data->get('playerId'), CTR::$data->get('playerId'), $player)
	);

	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => $player), array('dCreation', 'ASC'));

	if (ASM::$pam->size() == 1) {
		$player_selected = ASM::$pam->get(0);
		$player_ishim = $ishim;
		$ob_selected = ASM::$obm->getAll();
		include COMPONENT . 'player/diary/search.php';

		// # diaryRoplay component
		// $player_diaryRoplay = ASM::$pam->get(0);
		// include COMPONENT . 'player/diary/rolplay.php';

		// # diaryBases component
		// $ob_diaryBases = ASM::$obm->getAll();
		// include COMPONENT . 'player/diary/bases.php';

		// if (ASM::$msm->size() > 0) {
		// 	$threadId_thread = ASM::$msm->get()->getThread();
		// 	$messages_thread = array();
		// 	for ($i = 0; $i < ASM::$msm->size(); $i++) {
		// 		$messages_thread[] = ASM::$msm->get($i);
		// 	}
		// 	include COMPONENT . 'message/thread.php';
		// }
	} else {
		CTR::$alert->add('Le joueur a supprimé son compte ou a été défait.');
		CTR::redirect('profil');
	}

	include COMPONENT . 'default.php';

	ASM::$obm->changeSession($S_OBM1);
	ASM::$msm->changeSession($S_MSM1);
	ASM::$pam->changeSession($S_PAM1);
echo '</div>';
?>