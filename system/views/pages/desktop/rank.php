<?php
# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include_once ATLAS;

	$S_PRM1 = ASM::$prm->getCurrentSession();

	# load current player
	ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array('rPlayer' => CTR::$data->get('playerId')));
	$p = ASM::$prm->get();
	
	$generalPosition 	= ($p === FALSE || $p->generalPosition - PlayerRanking::PREV < 0) ? 0 : $p->generalPosition - PlayerRanking::PREV;
	$experiencePosition = ($p === FALSE || $p->experiencePosition - PlayerRanking::PREV < 0) ? 0 : $p->experiencePosition - PlayerRanking::PREV;
	$victoryPosition 	= ($p === FALSE || $p->victoryPosition - PlayerRanking::PREV < 0) ? 0 : $p->victoryPosition - PlayerRanking::PREV;
	$defeatPosition 	= ($p === FALSE || $p->defeatPosition - PlayerRanking::PREV < 0) ? 0 : $p->defeatPosition - PlayerRanking::PREV;
	$ratioPosition 		= ($p === FALSE || $p->ratioPosition - PlayerRanking::PREV < 0) ? 0 : $p->ratioPosition - PlayerRanking::PREV;

	# include part
	$PLAYER_RANKING_FRONT = ASM::$prm->getCurrentSession();
	include COMPONENT . 'rank/player/front.php';

	$PLAYER_RANKING_GENERAL = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('generalPosition', 'ASC'), array($generalPosition, PlayerRanking::STEP));
	include COMPONENT . 'rank/player/general.php';

	$PLAYER_RANKING_XP = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('experiencePosition', 'ASC'), array($experiencePosition, PlayerRanking::STEP));
	include COMPONENT . 'rank/player/xp.php';

	$PLAYER_RANKING_VICTORY = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('victoryPosition', 'ASC'), array($victoryPosition, PlayerRanking::STEP));
	include COMPONENT . 'rank/player/victory.php';

	$PLAYER_RANKING_DEFEAT = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('defeatPosition', 'ASC'), array($defeatPosition, PlayerRanking::STEP));
	include COMPONENT . 'rank/player/defeat.php';

	$PLAYER_RANKING_RATIO = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('ratioPosition', 'ASC'), array($ratioPosition, PlayerRanking::STEP));
	include COMPONENT . 'rank/player/ratio.php';

	ASM::$prm->changeSession($S_PRM1);
echo '</div>';
?>