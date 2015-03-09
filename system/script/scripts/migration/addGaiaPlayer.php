<?php
include_once ZEUS;

echo '<h1>Ajout du joueur GAIA</h1>';

$p = new Player();

$p->bind = Utils::generateString(25);
$p->rColor = 0;
$p->name = 'Rebelle';
$p->avatar = '000-1';
$p->status = 1;
$p->credit = 10000000;
$p->uPlayer = Utils::now();
$p->experience = 15000;
$p->factionPoint = 0;
$p->level = 5;
$p->victory = 0;
$p->defeat = 0;
$p->stepTutorial = 0;
$p->stepDone = 0;
$p->iUniversity = 0;
$p->partNaturalSciences = 25;
$p->partLifeSciences = 25;
$p->partSocialPoliticalSciences = 25;
$p->partInformaticEngineering = 25;
$p->dInscription = Utils::now();
$p->dLastConnection = Utils::now();
$p->dLastActivity = Utils::now();
$p->premium = 0;
$p->statement = PAM_DEAD;

ASM::$pam->add($p);

echo 'id du joueur : ' . $p->id;
?>