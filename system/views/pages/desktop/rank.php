<?php

use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Classes\Library\Utils;

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerRankingManager = $this->getContainer()->get(\Asylamba\Modules\Atlas\Manager\PlayerRankingManager::class);
$factionRankingManager = $this->getContainer()->get(\Asylamba\Modules\Atlas\Manager\FactionRankingManager::class);

# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'rankElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';

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
		include $componentPath . 'rank/player/front.php';

		$PLAYER_RANKING_GENERAL = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('generalPosition', 'ASC'), array($generalPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/general.php';

		$PLAYER_RANKING_XP = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('experiencePosition', 'ASC'), array($experiencePosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/xp.php';

		$PLAYER_RANKING_FIGHT = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('fightPosition', 'ASC'), array($fightPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/fight.php';

		$PLAYER_RANKING_RESOURCES = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('resourcesPosition', 'ASC'), array($resourcesPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/resources.php';

		$PLAYER_RANKING_ARMIES = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('armiesPosition', 'ASC'), array($armiesPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/armies.php';

		$PLAYER_RANKING_BUTCHER = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('butcherPosition', 'ASC'), array($butcherPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/butcher.php';

		$PLAYER_RANKING_TRADER = $playerRankingManager->newSession();
		$playerRankingManager->loadLastContext(array(), array('traderPosition', 'ASC'), array($traderPosition, PlayerRanking::STEP));
		include $componentPath . 'rank/player/trader.php';

		include $componentPath . 'rank/player/stats.php';

		$playerRankingManager->changeSession($S_PRM1);
	} elseif ($request->query->get('view') == 'faction') {
		$S_FRM1 = $factionRankingManager->getCurrentSession();

		# include part
		$FACTION_RANKING_FRONT = $factionRankingManager->newSession();

		if (Utils::interval($container->getParameter('server_start_time'), Utils::now(), 'h') > $container->getParameter('hours_before_start_of_ranking')) {
			$factionRankingManager->loadLastContext([], ['pointsPosition', 'ASC'], [0, 1]);
		} else {
			$factionRankingManager->loadLastContext([], ['generalPosition', 'ASC'], [0, 1]);
		}

		include $componentPath . 'rank/faction/front.php';

		$FACTION_RANKING_POINTS = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['pointsPosition', 'ASC']);
		include $componentPath . 'rank/faction/points.php';

		$FACTION_RANKING_GENERAL = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['generalPosition', 'ASC']);
		include $componentPath . 'rank/faction/general.php';

		$FACTION_RANKING_WEALTH = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['wealthPosition', 'ASC']);
		include $componentPath . 'rank/faction/wealth.php';

		$FACTION_RANKING_TERRITORIAL = $factionRankingManager->newSession();
		$factionRankingManager->loadLastContext([], ['territorialPosition', 'ASC']);
		include $componentPath . 'rank/faction/territorial.php';

		include $componentPath . 'rank/faction/info-victory.php';

		$factionRankingManager->changeSession($S_FRM1);
	} else {
		$this->getContainer()->get('app.response')->redirect('404');
	}
echo '</div>';
