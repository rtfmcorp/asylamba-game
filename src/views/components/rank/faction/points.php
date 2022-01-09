<?php
# rankVictory component
# in rank package

# classement faction en fonction de la somme des points des joueurs de chaque faction

# require
	# _T PRM 		FACTION_RANKING_POINTS

use App\Classes\Library\Utils;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$factionRankingManager = $this->getContainer()->get(\Asylamba\Modules\Atlas\Manager\FactionRankingManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$pointsToWin = $container->getParameter('points_to_win');
$serverStartTime = $container->getParameter('server_start_time');
$hoursBeforeStartOfRanking = $container->getParameter('hours_before_start_of_ranking');

$factionRankingManager->changeSession($FACTION_RANKING_POINTS);

echo '<div class="component player profil rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . $mediaPath . 'rank/cup.png">';
		echo '<h2>Classement de victoire</h2>';
		echo '<em>Classement cumulatif. La victoire est remportée à ' . $pointsToWin . ' points.</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (Utils::interval($serverStartTime, Utils::now(), 'h') > $hoursBeforeStartOfRanking) {
				for ($i = 0; $i < $factionRankingManager->size(); $i++) {
					echo $factionRankingManager->get($i)->commonRender($session->get('playerInfo'), $mediaPath, 'points');
				}
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">La classement de victoire n\'est pas encore activé. Il le sera à partir du </span>';
					echo '<span class="value">' . date("d.m.Y à H:i:s", strtotime(Utils::addSecondsToDate($serverStartTime, $hoursBeforeStartOfRanking * 3600))) . '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
