<?php

echo '<h2>Mise à jour du sénat d\'Aphera</h2>';

$factionColor = 6;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');

$factionPlayers = $playerManager->getFactionPlayersByRanking($factionColor);

$CLM_1 = $colorManager->getCurrentSession();
$colorManager->newSession();
$colorManager->load(['id' => $factionColor]);
$colorManager->updateStatus($colorManager->get(), $factionPlayers);
$colorManager->changeSession($CLM_1);