<?php
# rankVictory component
# in rank package

# classement faction en fonction de la richesse totale en crédits

# require
	# _T PRM 		FACTION_RANKING_WEALTH

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$factionRankingManager = $this->getContainer()->get(\Asylamba\Modules\Atlas\Manager\FactionRankingManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$factionRankingManager->changeSession($FACTION_RANKING_WEALTH);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . $mediaPath . 'rank/cup.png">';
		echo '<h2>Richesse</h2>';
		echo '<em>Revenu des routes commerciales de la faction</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < $factionRankingManager->size(); $i++) {
				echo $factionRankingManager->get($i)->commonRender($session->get('playerInfo'), 'wealth');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
