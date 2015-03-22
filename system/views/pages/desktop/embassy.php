<?php
# background paralax
echo '<div id="background-paralax" class="embassy"></div>';

# inclusion des elements
include 'embassyElement/subnav.php';
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
		include COMPONENT . 'embassy/diary/search.php';

		# diaryBases component
		$ob_diaryBases = ASM::$obm->getAll();
		include COMPONENT . 'embassy/diary/bases.php';
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