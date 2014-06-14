<?php
# daily cron
# call at x am. every day


include_once ATLAS;
$S_PRM1 = ASM::$prm->getCurrentSession();
include_once ZEUS;
$S_PAM1 = ASM::$pam->getCurrentSession();

# create a new ranking
$db = DataBase::getInstance();
$qr = $db->prepare('INSERT INTO ranking(dRanking, player, faction) VALUES (?, 1, 0)');
$qr->execute(array(Utils::now()));

$rRanking = $db->lastInsertId();

echo 'Numéro du ranking : ' . $rRanking;

/*
$pr->rRanking = $rRanking;
$pr->rPlayer = 0; 

$pr->general = 0;			# pts des bases + flottes + commandants
$pr->generalPosition = 0;
$pr->generalVariation = 0;

$pr->experience = 0;
$pr->experiencePosition = 0;
$pr->experienceVariation = 0;

$pr->victory = 0;
$pr->victoryPosition = 0;
$pr->victoryVariation = 0;

$pr->defeat = 0;
$pr->defeatPosition = 0;
$pr->defeatVariation = 0;

$pr->ratio = 0; 				# ratio victory - defeat 
$pr->ratioPosition = 0;
$pr->ratioVariation = 0;
*/

//ASM::$ntm->newSession();
//ASM::$ntm->load(array('readed' => 0, 'archived' => 0));

?>