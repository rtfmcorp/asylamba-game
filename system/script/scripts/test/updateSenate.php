<?php
include_once DEMETER;
$db = DataBaseAdmin::getInstance();

echo '<h2>Mise à jour du sénat d\'Aphera</h2>';

$factionColor = 6;

$_PAM = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);
ASM::$pam->load(['rColor' => $factionColor], ['factionPoint', 'DESC']);
$token_pam = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($_PAM);

$color = new Color();
$color->updateStatus($token_pam);