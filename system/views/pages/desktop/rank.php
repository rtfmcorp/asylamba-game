<?php
# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'rankElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

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

		if (Utils::interval(SERVER_START_TIME, Utils::now(), 'h') > HOURS_BEFORE_START_OF_RANKING) {
			ASM::$frm->loadLastContext([], ['pointsPosition', 'ASC'], [0, 1]);
		} else {
			ASM::$frm->loadLastContext([], ['generalPosition', 'ASC'], [0, 1]);
		}

		include COMPONENT . 'rank/faction/front.php';

		$FACTION_RANKING_POINTS = ASM::$frm->newSession();
		ASM::$frm->loadLastContext([], ['pointsPosition', 'ASC']);
		include COMPONENT . 'rank/faction/points.php';

		$FACTION_RANKING_GENERAL = ASM::$frm->newSession();
		ASM::$frm->loadLastContext([], ['generalPosition', 'ASC']);
		include COMPONENT . 'rank/faction/general.php';

		$FACTION_RANKING_WEALTH = ASM::$frm->newSession();
		ASM::$frm->loadLastContext([], ['wealthPosition', 'ASC']);
		include COMPONENT . 'rank/faction/wealth.php';

		$FACTION_RANKING_TERRITORIAL = ASM::$frm->newSession();
		ASM::$frm->loadLastContext([], ['territorialPosition', 'ASC']);
		include COMPONENT . 'rank/faction/territorial.php';

		include COMPONENT . 'rank/faction/info-victory.php';

		ASM::$frm->changeSession($S_FRM1);
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>