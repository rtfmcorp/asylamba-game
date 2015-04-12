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

function cmpWealth($a, $b) {
    if ($a['wealth'] == $b['wealth']) {
        return 0;
    }
    return ($a['wealth'] > $b['wealth']) ? -1 : 1;
}

function cmpTerritorial($a, $b) {
    if ($a['territorial'] == $b['territorial']) {
        return 0;
    }
    return ($a['territorial'] > $b['territorial']) ? -1 : 1;
}

# load the factions (colors)
ASM::$clm->load(array('id' => array(1,2,3,4,5,6,7)));

# create an array with all the factions
$list = array();
for ($i = 0; $i < ASM::$clm->size(); $i++) {
	$list[ASM::$clm->get($i)->id] = array(
		'general' => 0, 
		'wealth' => 0, 
		'territorial' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL RANKING --------------------------------#
# sum of general player ranking
# load all the players
ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

for ($i = 0; $i < ASM::$prm->size(); $i++) {
	$playerRank = ASM::$prm->get($i);

	$player = ASM::$pam->getById($playerRank->rPlayer);

	$list[$player->rColor]['general'] += $playerRank->general;
}

#-------------------------------- WEALTH RANKING ----------------------------------#
for ($i = 0; $i < ASM::$clm->size(); $i++) { 
	$faction = ASM::$clm->get($i);
	$list[$faction->id]['wealth'] = $faction->credits;
}

#-------------------------------- TERRITORIAL RANKING -----------------------------#

$sectorManager = new SectorManager();
$sectorManager->load();
for ($i = 0; $i < $sectorManager->size(); $i++) {
	$sector = $sectorManager->get($i);
	if ($sector->rColor != 0) {
		$list[$sector->rColor]['territorial'] += 1;
	}
}

# copy the arrays
$listG = $list;
$listW = $list;
$listT = $list;

# sort all the copies
uasort($listG, 'cmpGeneral');
uasort($listW, 'cmpWealth');
uasort($listT, 'cmpTerritorial');

/*foreach ($list as $key => $value) {
	echo $key . ' => ' . $value['general'] . '<br/>';
}*/

# put the position in each array
$position = 1;
foreach ($listG as $key => $value) { $listG[$key]['position'] = $position++;}
$position = 1;
foreach ($listW as $key => $value) { $listW[$key]['position'] = $position++;}
$position = 1;
foreach ($listT as $key => $value) { $listT[$key]['position'] = $position++;}

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

	$fr->wealth = $listW[$faction]['wealth'];
	$fr->wealthPosition = $listW[$faction]['position'];
	$fr->wealthVariation = $firstRanking ? 0 : $oldRanking->wealthPosition - $fr->wealthPosition;

	$fr->territorial = $listT[$faction]['territorial'];
	$fr->territorialPosition = $listT[$faction]['position'];
	$fr->territorialVariation = $firstRanking ? 0 : $oldRanking->territorialPosition - $fr->territorialPosition;

	# update faction infos
	ASM::$clm->getById($faction)->points = $listG[$faction]['general'];
	ASM::$clm->getById($faction)->sectors = $listT[$faction]['territorial'];
	ASM::$clm->updateInfos($faction);

	ASM::$frm->add($fr);
}

ASM::$clm->changeSession($S_CLM1);
ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);
ASM::$frm->changeSession($S_FRM1);

?>