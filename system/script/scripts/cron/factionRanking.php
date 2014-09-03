<?php
# daily cron
# call at x am. every day

include_once ATLAS;
$S_FRM1 = ASM::$frm->getCurrentSession();
ASM::$frm->newSession();
ASM::$frm->loadLastContext();

$S_PRM1 = ASM::$prm->getCurrentSession();
ASM::$prm->newSession();
ASM::$prm->loadLastContext();

include_once ZEUS;
$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);

include_once DEMETER;
$S_CLM1 = ASM::$clm->getCurrentSession();
ASM::$clm->newSession(FALSE);

include_once GAIA; # for Sector and SectorManager

# create a new ranking
$db = DataBase::getInstance();
$qr = $db->prepare('INSERT INTO ranking(dRanking, player, faction) VALUES (?, 0, 1)');
$qr->execute(array(Utils::now()));

$rRanking = $db->lastInsertId();

echo 'Numéro du ranking : ' . $rRanking . '<br /><br />';


function cmpGeneral($a, $b) {
    if ($a['general'] == $b['general']) {
        return 0;
    }
    return ($a['general'] > $b['general']) ? -1 : 1;
}

function cmpPower($a, $b) {
    if ($a['power'] == $b['power']) {
        return 0;
    }
    return ($a['power'] > $b['power']) ? -1 : 1;
}

function cmpDomination($a, $b) {
    if ($a['domination'] == $b['domination']) {
        return 0;
    }
    return ($a['domination'] > $b['domination']) ? -1 : 1;
}

# load the factions (colors)
ASM::$clm->load();

# create an array with all the factions
$list = array();
for ($i = 0; $i < ASM::$clm->size(); $i++) {
	$list[ASM::$clm->get($i)->id] = array(
		'general' => 0, 
		'power' => 0, 
		'domination' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL RANKING --------------------------------#
for ($i = 0; $i < ASM::$clm->size(); $i++) { 
	$faction = ASM::$clm->get($i);
	$list[$faction->id]['general'] = $faction->credits;
}

#-------------------------------- POWER RANKING ----------------------------------#
# sum of general player ranking
# load all the players
ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

for ($i = 0; $i < ASM::$prm->size(); $i++) {
	$playerRank = ASM::$prm->get($i);

	$player = ASM::$pam->getById($playerRank->rPlayer);

	$list[$player->rColor]['power'] += $playerRank->general;
}

#-------------------------------- DOMINATION RANKING -----------------------------#
$sectorManager = new SectorManager();
$sectorManager->load();
for ($i = 0; $i < $sectorManager->size(); $i++) {
	$sector = $sectorManager->get($i);
	if ($sector->rColor != 0) {
		$list[$sector->rColor]['domination'] += $sector->population;
	}
}

# copy the arrays
$listG = $list;
$listP = $list;
$listD = $list;

# sort all the copies
uasort($listG, 'cmpGeneral');
uasort($listP, 'cmpPower');
uasort($listD, 'cmpDomination');

/*foreach ($list as $key => $value) {
	echo $key . ' => ' . $value['general'] . '<br/>';
}*/

# put the position in each array
$position = 1;
foreach ($listG as $key => $value) { $listG[$key]['position'] = $position++;}
$position = 1;
foreach ($listP as $key => $value) { $listP[$key]['position'] = $position++;}
$position = 1;
foreach ($listD as $key => $value) { $listD[$key]['position'] = $position++;}

foreach ($list as $faction => $value) {
	$fr = new FactionRanking();
	$fr->rRanking = $rRanking;
	$fr->rFaction = $faction; 

	$firstRanking = true;
	for ($i = 0; $i < ASM::$frm->size(); $i++) {
		if (ASM::$frm->get($i)->rFaction == $faction) {
			$oldRanking = ASM::$frm->get($i);
			$firstRanking = false;
			break;
		}
	}

	$fr->general = $listG[$faction]['general'];
	$fr->generalPosition = $listG[$faction]['position'];
	$fr->generalVariation = $firstRanking ? 0 : $oldRanking->generalPosition - $fr->generalPosition;

	$fr->power = $listP[$faction]['power'];
	$fr->powerPosition = $listP[$faction]['position'];
	$fr->powerVariation = $firstRanking ? 0 : $oldRanking->powerPosition - $fr->powerPosition;

	$fr->domination = $listD[$faction]['domination'];
	$fr->dominationPosition = $listD[$faction]['position'];
	$fr->dominationVariation = $firstRanking ? 0 : $oldRanking->dominationPosition - $fr->dominationPosition;

	ASM::$frm->add($fr);
}

ASM::$clm->changeSession($S_CLM1);
ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);
ASM::$frm->changeSession($S_FRM1);

?>