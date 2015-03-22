<?php
# background paralax
echo '<div id="background-paralax" class="embassy"></div>';

# inclusion des elements
include 'embassyElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	if (CTR::$get->exist('faction')) {
		if (CTR::$get->exist('faction') && in_array(CTR::$get->get('faction'), [1, 2, 3, 4, 5, 6, 7])) {
			# load module
			include_once DEMETER;
			include_once ZEUS;

			# load data
			$S_COL_1 = ASM::$clm->getCurrentSession();
			ASM::$clm->newSession();
			ASM::$clm->load(array('id' => CTR::$get->get('faction')));
			$faction = ASM::$clm->get(0);

			$S_PAM_1 = ASM::$pam->getCurrentSession();
			$FACTION_GOV_TOKEN = ASM::$pam->newSession(FALSE);
			ASM::$pam->load(
				array('rColor' => $faction->id, 'status' => array(6, 5, 4, 3)),
				array('status', 'DESC')
			);

			# include component
			include COMPONENT . 'embassy/faction/nav.php';
			
			include COMPONENT . 'embassy/faction/infos.php';
			include COMPONENT . 'embassy/faction/government.php';

			$eraseColor = $faction->id;
			include COMPONENT . 'faction/data/diplomacy/main.php';

			# close session
			ASM::$pam->changeSession($S_PAM_1);
			ASM::$clm->changeSession($S_COL_1);
		} else {
			CTR::redirect('404');
		}
	} else {
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
	}
echo '</div>';
?>