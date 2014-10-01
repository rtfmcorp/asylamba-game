<?php
# daily cron
# call at x am. every day

include_once ATLAS;
$S_PRM1 = ASM::$prm->getCurrentSession();
ASM::$prm->newSession();
ASM::$prm->loadLastContext();

include_once ZEUS;
$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);

include_once ATHENA;
$S_OBM1 = ASM::$obm->getCurrentSession();
ASM::$obm->newSession(FALSE);

include_once ARES;
$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession(FALSE);

# create a new ranking
$db = DataBase::getInstance();
$qr = $db->prepare('INSERT INTO ranking(dRanking, player, faction) VALUES (?, 1, 0)');
$qr->execute(array(Utils::now()));

$rRanking = $db->lastInsertId();

echo 'Numéro du ranking : ' . $rRanking . '<br /><br />';


function cmpGeneral($a, $b) {
    if($a['general'] == $b['general']) {
        return 0;
    }
    return ($a['general'] > $b['general']) ? -1 : 1;
}

function cmpExperience($a, $b) {
    if($a['experience'] == $b['experience']) {
        return 0;
    }
    return ($a['experience'] > $b['experience']) ? -1 : 1;
}

function cmpVictory($a, $b) {
    if($a['victory'] == $b['victory']) {
        return 0;
    }
    return ($a['victory'] > $b['victory']) ? -1 : 1;
}

function cmpDefeat($a, $b) {
    if($a['defeat'] == $b['defeat']) {
        return 0;
    }
    return ($a['defeat'] > $b['defeat']) ? -1 : 1;
}

function cmpRatio($a, $b) {
    if($a['ratio'] == $b['ratio']) {
        return 0;
    }
    return ($a['ratio'] > $b['ratio']) ? -1 : 1;
}

ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

# create an array with all the players
$list = array();
for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$list[ASM::$pam->get($i)->id] = array(
		'general' => 0, 
		'experience' => 0, 
		'victory' => 0,
		'defeat' => 0,
		'ratio' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL RANKING --------------------------------#
# load all the bases
ASM::$obm->load();
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
$start = 0;
$qty = 250;

while (true) {
	ASM::$com->load(array('c.statement' => array(Commander::AFFECTED, Commander::MOVING)), array(), array($start, $qty));
	#ASM::$com->load(array('c.statement' => array(Commander::INSCHOOL, Commander::AFFECTED, Commander::MOVING, Commander::ONSALE)));
	
	# exit when all the commanders are loaded
	if (ASM::$com->size() == 0) { break; }
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
	}
	$start += $qty;
	
	ASM::$com->emptySession();
};

#-------------------------------- OTHER RANKINGs --------------------------------#
for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$pl = ASM::$pam->get($i);
	if (isset($list[$pl->id])) {
		# add the points to the list
		$list[$pl->id]['experience'] += $pl->experience;
		$list[$pl->id]['victory'] += $pl->victory;
		$list[$pl->id]['defeat'] += $pl->defeat;
		$list[$pl->id]['ratio'] += $pl->victory - $pl->defeat;
	}
}

# copy the arrays
$listG = $list;
$listE = $list;
$listV = $list;
$listD = $list;
$listR = $list;

# sort all the copies
uasort($listG, 'cmpGeneral');
uasort($listE, 'cmpExperience');
uasort($listV, 'cmpVictory');
uasort($listD, 'cmpDefeat');
uasort($listR, 'cmpRatio');

/*foreach ($list as $key => $value) {
	echo $key . ' => ' . $value['general'] . '<br/>';
}*/

# put the position in each array
$position = 1;
foreach ($listG as $key => $value) { $listG[$key]['position'] = $position++;}
$position = 1;
foreach ($listE as $key => $value) { $listE[$key]['position'] = $position++;}
$position = 1;
foreach ($listV as $key => $value) { $listV[$key]['position'] = $position++;}
$position = 1;
foreach ($listD as $key => $value) { $listD[$key]['position'] = $position++;}
$position = 1;
foreach ($listR as $key => $value) { $listR[$key]['position'] = $position++;}

foreach ($list as $player => $value) {
	$pr = new PlayerRanking();
	$pr->rRanking = $rRanking;
	$pr->rPlayer = $player; 

	# voir s'il faut améliorer (p.ex. : stocker le tableau des objets et supprimer chaque objet utilisé pour que la liste se rapetisse)
	$firstRanking = true;
	for ($i = 0; $i < ASM::$prm->size(); $i++) {
		if (ASM::$prm->get($i)->rPlayer == $player) {
			$firstRanking = false;
			$oldRanking = ASM::$prm->get($i);
			break;
		}
	}

	$pr->general = $listG[$player]['general'];
	$pr->generalPosition = $listG[$player]['position'];
	$pr->generalVariation = $firstRanking ? 0 : $oldRanking->generalPosition - $pr->generalPosition;

	$pr->experience = $listE[$player]['experience'];
	$pr->experiencePosition = $listE[$player]['position'];
	$pr->experienceVariation = $firstRanking ? 0 : $oldRanking->experiencePosition - $pr->experiencePosition;

	$pr->victory = $listV[$player]['victory'];
	$pr->victoryPosition = $listV[$player]['position'];
	$pr->victoryVariation = $firstRanking ? 0 : $oldRanking->victoryPosition - $pr->victoryPosition;

	$pr->defeat = $listD[$player]['defeat'];
	$pr->defeatPosition = $listD[$player]['position'];
	$pr->defeatVariation = $firstRanking ? 0 : $oldRanking->defeatPosition - $pr->defeatPosition;

	$pr->ratio = $listR[$player]['ratio'];
	$pr->ratioPosition = $listR[$player]['position'];
	$pr->ratioVariation = $firstRanking ? 0 : $oldRanking->ratioPosition - $pr->ratioPosition;

	ASM::$prm->add($pr);
}

ASM::$com->changeSession($S_COM1);
ASM::$obm->changeSession($S_OBM1);
ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);

?>