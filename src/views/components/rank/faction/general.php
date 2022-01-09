<?php
# rankVictory component
# in rank package

# classement faction en fonction de la somme des points des joueurs de chaque faction

# require
	# _T PRM 		FACTION_RANKING_GENERAL

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$factionRankingManager = $this->getContainer()->get(\App\Modules\Atlas\Manager\FactionRankingManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);

$factionRankingManager->changeSession($FACTION_RANKING_GENERAL);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . $mediaPath . 'rank/cup.png">';
		echo '<h2>Général</h2>';
		echo '<em>Somme des points des joueurs de la faction</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < $factionRankingManager->size(); $i++) {
				echo $factionRankingManager->get($i)->commonRender($session->get('playerInfo'), $mediaPath, 'general');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
