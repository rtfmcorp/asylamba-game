<?php
# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
include 'technologyElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once ZEUS;

	# loading des objets
	$S_PAM_TECH = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	$S_RSM_TECH = ASM::$rsm->getCurrentSession();
	ASM::$rsm->newSession();
	ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'university') {
		$player_university = ASM::$pam->get(0);
		$research_university = ASM::$rsm->get(0);
		include COMPONENT . 'tech/university.php';
	} elseif (CTR::$get->get('view') == 'technos') {
		include COMPONENT . 'tech/infoTech.php';
	} else {
		CTR::redirect('404');
	}

	ASM::$pam->changeSession($S_PAM_TECH);
	ASM::$rsm->changeSession($S_RSM_TECH);
echo '</div>';
?>