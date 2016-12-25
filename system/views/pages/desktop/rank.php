<?php

use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Classes\Library\Utils;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerRankingManager = $this->getContainer()->get('atlas.player_ranking_manager');
$factionRankingManager = $this->getContainer()->get('atlas.faction_ranking_manager');

# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'rankElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if (!$request->query->has('view') OR $request->query->get('view') == 'player') {
		$S_PRM1 = $playerRankingManager->getCurrentSession();

		# load current player
		$playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array('rPlayer' => $session->get('playerId')));
		$p = $playerRankingManager->get();
		
		$generalPosition 	= ($p === FALSE || $request->query->get('mode') === 'top' || $p->generalPosition - PlayerRanking::PREV < 0) ? 0 : $p->generalPosition - PlayerRanking::PREV;
		$resourcesPosition 	= ($p === FALSE || $request->query->get('mode') === 'top' || $p->resourcesPosition - PlayerRanking::PREV < 0) ? 0 : $p->resourcesPosition - PlayerRanking::PREV;
		$experiencePosition = ($p === FALSE || $request->query->get('mode') === 'top' || $p->experiencePosition - PlayerRanking::PREV < 0) ? 0 : $p->experiencePosition - PlayerRanking::PREV;
		$fightPosition 		= ($p === FALSE || $request->query->get('mode') === 'top' || $p->fightPosition - PlayerRanking::PREV < 0) ? 0 : $p->fightPosition - PlayerRanking::PREV;
		$armiesPosition 	= ($p === FALSE || $request->query->get('mode') === 'top' || $p->armiesPosition - PlayerRanking::PREV < 0) ? 0 : $p->armiesPosition - PlayerRanking::PREV;
		$butcherPosition 	= ($p === FALSE || $request->query->get('mode') === 'top' || $p->butcherPosition - PlayerRanking::PREV < 0) ? 0 : $p->butcherPosition - PlayerRanking::PREV;
		$traderPosition 	= ($p === FALSE || $request->query->get('mode') === 'top' || $p->traderPosition - PlayerRanking::PREV < 0) ? 0 : $p->traderPosition - PlayerRanking::PREV;

		# include part
		$PLAYER_RANKING_FRONT = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('generalPosition', 'ASC'), array(0, 1));
		include COMPONENT . 'rank/player/front.php';

		$PLAYER_RANKING_GENERAL = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('generalPosition', 'ASC'), array($generalPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/general.php';

		$PLAYER_RANKING_XP = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('experiencePosition', 'ASC'), array($experiencePosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/xp.php';

		$PLAYER_RANKING_FIGHT = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('fightPosition', 'ASC'), array($fightPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/fight.php';

		$PLAYER_RANKING_RESOURCES = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('resourcesPosition', 'ASC'), array($resourcesPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/resources.php';

		$PLAYER_RANKING_ARMIES = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('armiesPosition', 'ASC'), array($armiesPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/armies.php';

		$PLAYER_RANKING_BUTCHER = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('butcherPosition', 'ASC'), array($butcherPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/butcher.php';

		$PLAYER_RANKING_TRADER = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('traderPosition', 'ASC'), array($traderPosition, PlayerRanking::STEP));
		include COMPONENT . 'rank/player/trader.php';

		include COMPONENT . 'rank/player/stats.php';

		$playerRankingManager->changeSession($S_PRM1);
	} elseif ($request->query->get('view') == 'faction') {
		$S_FRM1 = $factionRankingManager->getCurrentSession();

		# include part
		$FACTION_RANKING_FRONT = $factionRankingManager->newSession();

		if (Utils::interval(SERVER_START_TIME, Utils::now(), 'h') > HOURS_BEFORE_START_OF_RANKING) {
			$factionRankingManager->loadLastContext([], ['pointsPosition', 'ASC'], [0, 1]);
		} else {
			$factionRankingManager->loadLastContext([], ['generalPosition', 'ASC'], [0, 1]);
		}

		include COMPONENT . 'rank/faction/front.php';

		$FACTION_RANKING_POINTS = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['pointsPosition', 'ASC']);
		include COMPONENT . 'rank/faction/points.php';

		$FACTION_RANKING_GENERAL = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['generalPosition', 'ASC']);
		include COMPONENT . 'rank/faction/general.php';

		$FACTION_RANKING_WEALTH = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['wealthPosition', 'ASC']);
		include COMPONENT . 'rank/faction/wealth.php';

		$FACTION_RANKING_TERRITORIAL = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['territorialPosition', 'ASC']);
		include COMPONENT . 'rank/faction/territorial.php';

		include COMPONENT . 'rank/faction/info-victory.php';

		$factionRankingManager->changeSession($S_FRM1);
	} else {
		$this->getContainer()->get('app.response')->redirect('404');
	}
echo '</div>';
