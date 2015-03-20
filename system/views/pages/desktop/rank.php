<?php
# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'rankElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include_once ATLAS;

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'player') {
		$S_PRM1 = ASM::$prm->getCurrentSession();

		# load current player
		ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array('rPlayer' => CTR::$data->get('playerId')));
		$p = ASM::$prm->get();
		
		$generalPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->generalPosition - PlayerRanking::PREV < 0) ? 0 : $p->generalPosition - PlayerRanking::PREV;
		$resourcesPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->resourcesPosition - PlayerRanking::PREV < 0) ? 0 : $p->resourcesPosition - PlayerRanking::PREV;
		$experiencePosition = ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->experiencePosition - PlayerRanking::PREV < 0) ? 0 : $p->experiencePosition - PlayerRanking::PREV;
		$fightPosition 		= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->fightPosition - PlayerRanking::PREV < 0) ? 0 : $p->fightPosition - PlayerRanking::PREV;
		$armiesPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->armiesPosition - PlayerRanking::PREV < 0) ? 0 : $p->armiesPosition - PlayerRanking::PREV;
		$butcherPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->butcherPosition - PlayerRanking::PREV < 0) ? 0 : $p->butcherPosition - PlayerRanking::PREV;
		$traderPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->traderPosition - PlayerRanking::PREV < 0) ? 0 : $p->traderPosition - PlayerRanking::PREV;

		# include part
		$PLAYER_RANKING_FRONT = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('generalPosition', 'ASC'), array(0, 1));
		include COMPONENT . 'rank/player/front.php';

		$PLAYER_RANKING_GENERAL = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('generalPosition', 'ASC'), array($generalPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/general.php';

		$PLAYER_RANKING_XP = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('experiencePosition', 'ASC'), array($experiencePosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/xp.php';

		$PLAYER_RANKING_FIGHT = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('fightPosition', 'ASC'), array($fightPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/fight.php';

		$PLAYER_RANKING_RESOURCES = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('resourcesPosition', 'ASC'), array($resourcesPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/resources.php';

		$PLAYER_RANKING_ARMIES = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('armiesPosition', 'ASC'), array($armiesPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/armies.php';

		$PLAYER_RANKING_BUTCHER = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('butcherPosition', 'ASC'), array($butcherPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/butcher.php';

		$PLAYER_RANKING_TRADER = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('traderPosition', 'ASC'), array($traderPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/trader.php';

		include COMPONENT . 'rank/player/stats.php';

		ASM::$prm->changeSession($S_PRM1);
	} elseif (CTR::$get->get('view') == 'faction') {
		$S_FRM1 = ASM::$frm->getCurrentSession();

		# include part
		$FACTION_RANKING_FRONT = ASM::$frm->newSession();
		ASM::$frm->loadLastContext(array('rFaction' => array(1,2,3,4,5,6,7)), array('generalPosition', 'ASC'), array(0, 1));
		include COMPONENT . 'rank/faction/front.php';

		$FACTION_RANKING_GENERAL = ASM::$frm->newSession();
		ASM::$frm->loadLastContext(array('rFaction' => array(1,2,3,4,5,6,7)), array('generalPosition', 'ASC'));
		include COMPONENT . 'rank/faction/general.php';

		$FACTION_RANKING_WEALTH = ASM::$frm->newSession();
		ASM::$frm->loadLastContext(array('rFaction' => array(1,2,3,4,5,6,7)), array('wealthPosition', 'ASC'));
		include COMPONENT . 'rank/faction/wealth.php';

		$FACTION_RANKING_TERRITORIAL = ASM::$frm->newSession();
		ASM::$frm->loadLastContext(array('rFaction' => array(1,2,3,4,5,6,7)), array('territorialPosition', 'ASC'));
		include COMPONENT . 'rank/faction/territorial.php';

		ASM::$frm->changeSession($S_FRM1);
	} elseif (CTR::$get->get('view') == 'list') {
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
			include COMPONENT . 'public/faction/nav.php';
			
			include COMPONENT . 'public/faction/infos.php';
			include COMPONENT . 'public/faction/government.php';

			$eraseColor = $faction->id;
			include COMPONENT . 'faction/data/diplomacy/main.php';

			# close session
			ASM::$pam->changeSession($S_PAM_1);
			ASM::$clm->changeSession($S_COL_1);
		} else {
			CTR::redirect('404');
		}
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>