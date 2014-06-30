<?php
# daily cron
# call at x am. every day


include_once ATLAS;
$S_FRM1 = ASM::$frm->getCurrentSession();
ASM::$frm->newSession();
ASM::$frm->loadLastContext();

include_once DEMETER;
$S_CLM1 = ASM::$clm->getCurrentSession();
ASM::$clm->newSession(FALSE);


/*include_once ZEUS;
$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);

include_once ATHENA;
$S_OBM1 = ASM::$obm->getCurrentSession();
ASM::$obm->newSession(FALSE);

include_once ARES;
$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession(FALSE);*/

# create a new ranking
$db = DataBase::getInstance();
$qr = $db->prepare('INSERT INTO ranking(dRanking, player, faction) VALUES (?, 0, 1)');
$qr->execute(array(Utils::now()));

$rRanking = $db->lastInsertId();

echo 'Numéro du ranking : ' . $rRanking . '<br /><br />';


function cmpGeneral($a, $b) {
    if($a['general'] == $b['general']) {
        return 0;
    }
    return ($a['general'] > $b['general']) ? -1 : 1;
}

function cmpPower($a, $b) {
    if($a['power'] == $b['power']) {
        return 0;
    }
    return ($a['power'] > $b['power']) ? -1 : 1;
}

function cmpDomination($a, $b) {
    if($a['domination'] == $b['domination']) {
        return 0;
    }
    return ($a['domination'] > $b['domination']) ? -1 : 1;
}


#ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));
ASM::$clm->load();

for ($i=0; $i < ASM::$clm->size(); $i++) { 
	//echo '<br/> id : ' . ASM::$clm->get($i)->id;
	//echo ', points : ' . ASM::$clm->get($i)->points;
	Bug::pre(ASM::$clm->get($i));
	echo '------------------------------------------------------------------------------------------';
}

# create an array with all the players
$list = array();
for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$list[ASM::$pam->get($i)->id] = array(
		'general' => 0, 
		'power' => 0, 
		'domination' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL RANKING --------------------------------#
# load all the bases
/*ASM::$obm->load();
for ($i = 0; $i < ASM::$obm->size(); $i++) {
	$orbitalBase = ASM::$obm->get($i);
	if (isset($list[$orbitalBase->rPlayer])) {
		# count the points of a base
		$points = 0;
		$points += $orbitalBase->points;

		$points += round($orbitalBase->resourcesStorage * COEF_RESOURCE);

		$shipPrice = 0;
		for ($j = 0; $j < 12; $j++) {
			$shipPrice += ShipResource::getInfo($j, 'resourcePrice') * $orbitalBase->getShipStorage($j);
		}
		$points += round($shipPrice * COEF_RESOURCE);
		# add the points to the list
		$list[$orbitalBase->rPlayer]['general'] += $points;
	}
}

# load the commanders
ASM::$com->load(array('c.statement' => array(Commander::INSCHOOL, Commander::AFFECTED, Commander::MOVING, Commander::ONSALE)));
for ($i = 0; $i < ASM::$com->size(); $i++) {
	$commander = ASM::$com->get($i);
	if (isset($list[$commander->rPlayer])) {
		# count the points of a commander
		$points = 0;
		$shipList = $commander->getNbrShipByType();
		$shipPrice = 0;
		for ($j = 0; $j < 12; $j++) {
			$shipPrice += ShipResource::getInfo($j, 'resourcePrice') * $shipList[$j];
		}
		$points += round($shipPrice * COEF_RESOURCE);

		$list[$commander->rPlayer]['general'] += $points;
	}
}*/

#-------------------------------- OTHER RANKINGs --------------------------------#
/*for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$pl = ASM::$pam->get($i);
	if (isset($list[$pl->id])) {
		# add the points to the list
		$list[$pl->id]['power'] += $pl->power;
		$list[$pl->id]['domination'] += $pl->domination;
	}
}*/

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

#Bug::pre($listR);


foreach ($list as $player => $value) {
	$fr = new FactionRanking();
	$fr->rRanking = $rRanking;
	$fr->rPlayer = $player; 

	for ($i = 0; $i < ASM::$frm->size(); $i++) {
		if (ASM::$frm->get($i)->rPlayer == $player) {
			$oldRanking = ASM::$frm->get($i);
			break;
		}
	}

	$fr->general = $listG[$player]['general'];
	$fr->generalPosition = $listG[$player]['position'];
	$fr->generalVariation = $oldRanking->generalPosition - $fr->generalPosition;

	$fr->power = $listP[$player]['power'];
	$fr->powerPosition = $listP[$player]['position'];
	$fr->powerVariation = $oldRanking->powerPosition - $fr->powerPosition;

	$fr->domination = $listD[$player]['domination'];
	$fr->dominationPosition = $listD[$player]['position'];
	$fr->dominationVariation = $oldRanking->dominationPosition - $fr->dominationPosition;


	//ASM::$frm->add($fr);
}

//ASM::$pam->changeSession($S_COM1);
//ASM::$pam->changeSession($S_OBM1);
//ASM::$pam->changeSession($S_PAM1);
ASM::$clm->changeSession($S_CLM1);
ASM::$frm->changeSession($S_FRM1);

?>