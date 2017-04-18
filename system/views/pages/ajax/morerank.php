<?php

use Asylamba\Modules\Atlas\Model\PlayerRanking;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerRankingManager = $this->getContainer()->get('atlas.player_ranking_manager');

$direction		= $request->query->get('dir');
$current		= $request->query->get('current');
$type			= $request->query->get('type');

if ($direction !== FALSE && $current !== FALSE && $type !== FALSE) {
	if (in_array($direction, array('next', 'prev'))) {
		if (in_array($type, array('general', 'resources', 'xp', 'fight', 'armies', 'butcher', 'trader'))) {
			# var
			$fty = ($type == 'xp')
				? 'experience'
				: $type;

			$bot = ($direction == 'next')
				? (($current - PlayerRanking::PAGE > 1) ? $current - PlayerRanking::PAGE : 1)
				: $current + 1;

			$size = ($bot == 1)
				? $current - 1
				: PlayerRanking::PAGE;

			$S_PRM1 = $playerRankingManager->getCurrentSession();
			$playerRankingManager->newSession();
			$playerRankingManager->loadLastContext(array(), array($fty . 'Position', 'ASC'), array($bot - 1, $size));

			if ($direction == 'next' && $bot > 1) {
				echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-next/type-' . $type . '/current-' . $bot . '" data-dir="top">';
					echo 'afficher les joueurs précédents';
				echo '</a>';
			}

			for ($i = 0; $i < $playerRankingManager->size(); $i++) {
				echo $playerRankingManager->get($i)->commonRender($session->get('playerId'), $type);
			}

			if ($direction == 'prev' && $playerRankingManager->size() == PlayerRanking::PAGE) {
				echo '<a class="more-item" href="' . APP_ROOT . 'ajax/a-morerank/dir-prev/type-' . $type . '/current-' . ($current + PlayerRanking::PAGE) . '">';
					echo 'afficher les joueurs suivants';
				echo '</a>';
			}

			$playerRankingManager->changeSession($S_PRM1);
		}
	}
}
