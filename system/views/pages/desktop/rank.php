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

	$PLAYER_RANKING_GENERAL = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('generalPosition', 'ASC'), array(0, 50));

	$PLAYER_RANKING_XP = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('experiencePosition', 'ASC'), array(0, 50));

	$PLAYER_RANKING_VICTORY = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('victoryPosition', 'ASC'), array(0, 50));

	$PLAYER_RANKING_DEFEAT = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('defeatPosition', 'ASC'), array(0, 50));

	$PLAYER_RANKING_RATIO = ASM::$prm->newSession();
	ASM::$prm->loadLastContext(array(), array('ratioPosition', 'ASC'), array(0, 50));

	include COMPONENT . 'rank/player/general.php';
	include COMPONENT . 'rank/player/xp.php';
	include COMPONENT . 'rank/player/victory.php';
	include COMPONENT . 'rank/player/defeat.php';
	include COMPONENT . 'rank/player/ratio.php';

	ASM::$prm->changeSession($S_PRM1);
echo '</div>';
?>