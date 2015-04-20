<?php
echo '<h1>Mise à jour des missions de recyclage</h1>';
echo '<p>Augmentation du temps de recyclage de 5h à 8h pour toutes les missions</p>';

include_once ATHENA;
$S_REM1 = ASM::$rem->getCurrentSession();
ASM::$rem->newSession(ASM_UMODE);
ASM::$rem->load();

echo '<p>Nombre de missions : ' . ASM::$rem->size() . '</p>';

// 5h = 10800 secondes
// 8h = 28800 secondes
// --> ajouter 10800 secondes sur le champ 'cycleTime' de la table 'recyclingMission'

for ($i = 0; $i < ASM::$rem->size(); $i++) {
	ASM::$rem->get($i)->cycleTime += 10800;
}
echo '<p>Tout s\'est bien passé</p>';
ASM::$rem->changeSession($S_REM1);
?>