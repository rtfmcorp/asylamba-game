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

function cmpResources($a, $b) {
    if($a['resources'] == $b['resources']) {
        return 0;
    }
    return ($a['resources'] > $b['resources']) ? -1 : 1;
}

function cmpExperience($a, $b) {
    if($a['experience'] == $b['experience']) {
        return 0;
    }
    return ($a['experience'] > $b['experience']) ? -1 : 1;
}

function cmpFight($a, $b) {
    if($a['fight'] == $b['fight']) {
        return 0;
    }
    return ($a['fight'] > $b['fight']) ? -1 : 1;
}

function cmpArmies($a, $b) {
    if($a['armies'] == $b['armies']) {
        return 0;
    }
    return ($a['armies'] > $b['armies']) ? -1 : 1;
}

function cmpButcher($a, $b) {
    if($a['butcher'] == $b['butcher']) {
        return 0;
    }
    return ($a['butcher'] > $b['butcher']) ? -1 : 1;
}

function cmpTrader($a, $b) {
    if($a['trader'] == $b['trader']) {
        return 0;
    }
    return ($a['trader'] > $b['trader']) ? -1 : 1;
}

ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

# create an array with all the players
$list = array();
for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$list[ASM::$pam->get($i)->id] = array(
		'general' => 0, 
		'resources' => 0,
		'experience' => 0, 
		'victory' => 0,
		'defeat' => 0,
		'fight' => 0,
		'armies' => 0,
		'butcher' => 0,
		'trader' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL & RESOURCES & ARMIES RANKING --------------------------------#
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
		$pevQuantity = 0;
		for ($j = 0; $j < 12; $j++) {
			$shipPrice += ShipResource::getInfo($j, 'resourcePrice') * $orbitalBase->getShipStorage($j);
			$pevQuantity += ShipResource::getInfo($j, 'pev') * $orbitalBase->getShipStorage($j);
		}
		$points += round($shipPrice * COEF_RESOURCE);
		# add the points to the list
		$list[$orbitalBase->rPlayer]['general'] += $points;
		$list[$orbitalBase->rPlayer]['armies'] += $pevQuantity;

		$resourcesProd = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $orbitalBase->levelRefinery, 'refiningCoefficient'), $orbitalBase->getPlanetResources());
		$list[$orbitalBase->rPlayer]['resources'] += $resourcesProd;

	}
}

# load the commanders
$start = 0;
$qty = 250;

while (true) {
	ASM::$com->load(array('c.statement' => array(Commander::AFFECTED, Commander::MOVING)), array(), array($start, $qty));
	
	# exit when all the commanders are loaded
	if (ASM::$com->size() == 0) { break; }
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		$commander = ASM::$com->get($i);
		if (isset($list[$commander->rPlayer])) {
			# count the points of a commander
			$points = 0;
			$shipList = $commander->getNbrShipByType();
			$shipPrice = 0;
			$pevQuantity = 0;
			for ($j = 0; $j < 12; $j++) {
				$shipPrice += ShipResource::getInfo($j, 'resourcePrice') * $shipList[$j];
				$pevQuantity += ShipResource::getInfo($j, 'pev') * $shipList[$j];
			}
			$points += round($shipPrice * COEF_RESOURCE);

			$list[$commander->rPlayer]['general'] += $points;
			$list[$commander->rPlayer]['armies'] += $pevQuantity;
		}
	}
	$start += $qty;
	
	ASM::$com->emptySession();
};

#-------------------------------- FIGHT & EXPERIENCE RANKING --------------------------------#
for ($i = 0; $i < ASM::$pam->size(); $i++) {
	$pl = ASM::$pam->get($i);
	if (isset($list[$pl->id])) {
		# add the points to the list
		$list[$pl->id]['experience'] += $pl->experience;
		$list[$pl->id]['victory'] += $pl->victory;
		$list[$pl->id]['defeat'] += $pl->defeat;
		$list[$pl->id]['fight'] += $pl->victory - $pl->defeat;
	}
}

# copy the arrays
$listG = $list;
$listR = $list;
$listE = $list;
$listF = $list;
$listA = $list;
$listB = $list;
$listT = $list;

# sort all the copies
uasort($listG, 'cmpGeneral');
uasort($listR, 'cmpResources');
uasort($listE, 'cmpExperience');
uasort($listF, 'cmpFight');
uasort($listA, 'cmpArmies');
uasort($listB, 'cmpButcher');
uasort($listT, 'cmpTrader');

/*foreach ($list as $key => $value) {
	echo $key . ' => ' . $value['general'] . '<br/>';
}*/

# put the position in each array
$position = 1;
foreach ($listG as $key => $value) { $listG[$key]['position'] = $position++;}
$position = 1;
foreach ($listR as $key => $value) { $listR[$key]['position'] = $position++;}
$position = 1;
foreach ($listE as $key => $value) { $listE[$key]['position'] = $position++;}
$position = 1;
foreach ($listF as $key => $value) { $listF[$key]['position'] = $position++;}
$position = 1;
foreach ($listA as $key => $value) { $listA[$key]['position'] = $position++;}
$position = 1;
foreach ($listB as $key => $value) { $listB[$key]['position'] = $position++;}
$position = 1;
foreach ($listT as $key => $value) { $listT[$key]['position'] = $position++;}

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

	$pr->resources = $listR[$player]['resources'];
	$pr->resourcesPosition = $listR[$player]['position'];
	$pr->resourcesVariation = $firstRanking ? 0 : $oldRanking->resourcesPosition - $pr->resourcesPosition;

	$pr->experience = $listE[$player]['experience'];
	$pr->experiencePosition = $listE[$player]['position'];
	$pr->experienceVariation = $firstRanking ? 0 : $oldRanking->experiencePosition - $pr->experiencePosition;

	$pr->fight = $listF[$player]['fight'];
	$pr->victories = $listF[$player]['victory'];
	$pr->defeat = $listF[$player]['defeat'];
	$pr->fightPosition = $listF[$player]['position'];
	$pr->fightVariation = $firstRanking ? 0 : $oldRanking->fightPosition - $pr->fightPosition;

	$pr->armies = $listA[$player]['armies'];
	$pr->armiesPosition = $listA[$player]['position'];
	$pr->armiesVariation = $firstRanking ? 0 : $oldRanking->armiesPosition - $pr->armiesPosition;

	$pr->butcher = $listB[$player]['butcher'];
	$pr->butcherDestroyedPEV = 0;//$listB[$player]['butcherDestroyedPEV'];
	$pr->butcherLostPEV = 0;//$listB[$player]['butcherLostPEV'];
	$pr->butcherPosition = $listB[$player]['position'];
	$pr->butcherVariation = $firstRanking ? 0 : $oldRanking->butcherPosition - $pr->butcherPosition;

	$pr->trader = $listT[$player]['trader'];
	$pr->traderPosition = $listT[$player]['position'];
	$pr->traderVariation = $firstRanking ? 0 : $oldRanking->traderPosition - $pr->traderPosition;


	ASM::$prm->add($pr);
}

ASM::$com->changeSession($S_COM1);
ASM::$obm->changeSession($S_OBM1);
ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);

?>