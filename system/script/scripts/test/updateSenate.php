<?php

use Asylamba\Modules\Demeter\Model\Color;

echo '<h2>Mise à jour du sénat d\'Aphera</h2>';

$factionColor = 6;

$playerManager = $this->getContainer()->get('zeus.player_manager');

$_PAM = $playerManager->getCurrentSession();
$playerManager->newSession(FALSE);
$playerManager->load(['rColor' => $factionColor], ['factionPoint', 'DESC']);
$token_pam = $playerManager->getCurrentSession();
$playerManager->changeSession($_PAM);

$color = new Color();
$color->updateStatus($token_pam);