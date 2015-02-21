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
		$experiencePosition = ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->experiencePosition - PlayerRanking::PREV < 0) ? 0 : $p->experiencePosition - PlayerRanking::PREV;
		//$victoryPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->victoryPosition - PlayerRanking::PREV < 0) ? 0 : $p->victoryPosition - PlayerRanking::PREV;
		//$defeatPosition 	= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->defeatPosition - PlayerRanking::PREV < 0) ? 0 : $p->defeatPosition - PlayerRanking::PREV;
		$fightPosition 		= ($p === FALSE || CTR::$get->equal('mode', 'top') || $p->fightPosition - PlayerRanking::PREV < 0) ? 0 : $p->fightPosition - PlayerRanking::PREV;

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

		/*$PLAYER_RANKING_VICTORY = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('victoryPosition', 'ASC'), array($victoryPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/victory.php';

		$PLAYER_RANKING_DEFEAT = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('defeatPosition', 'ASC'), array($defeatPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/defeat.php';*/

		$PLAYER_RANKING_FIGHT = ASM::$prm->newSession();
		ASM::$prm->loadLastContext(array(), array('fightPosition', 'ASC'), array($fightPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/fight.php';

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
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>