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
include_once ARES;

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
		'butcherDestroyedPEV' => 0,
		'butcherLostPEV' => 0,
		'trader' => 0);
}

const COEF_RESOURCE = 0.001;

#-------------------------------- RESOURCES --------------------------------#
$qr = $db->prepare('SELECT 
		p.id AS player,
		ob.levelRefinery AS levelRefinery,
		pl.coefResources AS coefResources
	FROM orbitalBase AS ob 
	LEFT JOIN place AS pl
		ON pl.id = ob.rPlace
	LEFT JOIN player AS p
		on p.id = ob.rPlayer
	WHERE p.statement = ? OR p.statement = ? OR p.statement = ?');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$resourcesProd = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $aw['levelRefinery'], 'refiningCoefficient'), $aw['coefResources']);
		$list[$aw['player']]['resources'] += $resourcesProd;
	}
}


#-------------------------------- GENERAL & ARMIES RANKING --------------------------------#
# load the bases
$qr = $db->prepare('SELECT 
		p.id AS player,
		SUM(ob.points) AS points,
		SUM(ob.resourcesStorage) AS resources,
		SUM(ob.pegaseStorage) AS s0,
		SUM(ob.satyreStorage) AS s1,
		SUM(ob.sireneStorage) AS s3,
		SUM(ob.dryadeStorage) AS s4,
		SUM(ob.chimereStorage) AS s2,
		SUM(ob.meduseStorage) AS s5,
		SUM(ob.griffonStorage) AS s6,
		SUM(ob.cyclopeStorage) AS s7,
		SUM(ob.minotaureStorage) AS s8,
		SUM(ob.hydreStorage) AS s9,
		SUM(ob.cerbereStorage) AS s10,
		SUM(ob.phenixStorage) AS s11
	FROM orbitalBase AS ob 
	LEFT JOIN player AS p
		ON p.id = ob.rPlayer

	WHERE p.statement = ? OR p.statement = ? OR p.statement = ?
	GROUP BY p.id');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$shipPrice = 0;
		$shipPrice += ShipResource::getInfo(0, 'resourcePrice') * $aw['s0'];
		$shipPrice += ShipResource::getInfo(1, 'resourcePrice') * $aw['s1'];
		$shipPrice += ShipResource::getInfo(2, 'resourcePrice') * $aw['s2'];
		$shipPrice += ShipResource::getInfo(3, 'resourcePrice') * $aw['s3'];
		$shipPrice += ShipResource::getInfo(4, 'resourcePrice') * $aw['s4'];
		$shipPrice += ShipResource::getInfo(5, 'resourcePrice') * $aw['s5'];
		$shipPrice += ShipResource::getInfo(6, 'resourcePrice') * $aw['s6'];
		$shipPrice += ShipResource::getInfo(7, 'resourcePrice') * $aw['s7'];
		$shipPrice += ShipResource::getInfo(8, 'resourcePrice') * $aw['s8'];
		$shipPrice += ShipResource::getInfo(9, 'resourcePrice') * $aw['s9'];
		$shipPrice += ShipResource::getInfo(10, 'resourcePrice') * $aw['s10'];
		$shipPrice += ShipResource::getInfo(11, 'resourcePrice') * $aw['s11'];
		$points = round($shipPrice * COEF_RESOURCE);
		$points += $aw['points'];
		$points += round($aw['resources'] * COEF_RESOURCE);
		$list[$aw['player']]['general'] += $points;

		$pevQuantity = 0;
		$pevQuantity += ShipResource::getInfo(0, 'pev') * $aw['s0'];
		$pevQuantity += ShipResource::getInfo(1, 'pev') * $aw['s1'];
		$pevQuantity += ShipResource::getInfo(2, 'pev') * $aw['s2'];
		$pevQuantity += ShipResource::getInfo(3, 'pev') * $aw['s3'];
		$pevQuantity += ShipResource::getInfo(4, 'pev') * $aw['s4'];
		$pevQuantity += ShipResource::getInfo(5, 'pev') * $aw['s5'];
		$pevQuantity += ShipResource::getInfo(6, 'pev') * $aw['s6'];
		$pevQuantity += ShipResource::getInfo(7, 'pev') * $aw['s7'];
		$pevQuantity += ShipResource::getInfo(8, 'pev') * $aw['s8'];
		$pevQuantity += ShipResource::getInfo(9, 'pev') * $aw['s9'];
		$pevQuantity += ShipResource::getInfo(10, 'pev') * $aw['s10'];
		$pevQuantity += ShipResource::getInfo(11, 'pev') * $aw['s11'];
		$list[$aw['player']]['armies'] += $pevQuantity;
	}
}

# load the commanders
$qr = $db->prepare('SELECT 
		p.id AS player,
		SUM(sq.ship0) as s0,
		SUM(sq.ship1) as s1,
		SUM(sq.ship2) as s2,
		SUM(sq.ship3) as s3,
		SUM(sq.ship4) as s4,
		SUM(sq.ship5) as s5,
		SUM(sq.ship6) as s6,
		SUM(sq.ship7) as s7,
		SUM(sq.ship8) as s8,
		SUM(sq.ship9) as s9,
		SUM(sq.ship10) as s10,
		SUM(sq.ship11) as s11
	FROM squadron AS sq 
	LEFT JOIN commander AS c
		ON c.id = sq.rCommander
	LEFT JOIN player AS p
		ON p.id = c.rPlayer
	WHERE c.statement = ? || c.statement = ?
	GROUP BY p.id');
$qr->execute(array(Commander::AFFECTED, Commander::MOVING));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$shipPrice = 0;
		$shipPrice += ShipResource::getInfo(0, 'resourcePrice') * $aw['s0'];
		$shipPrice += ShipResource::getInfo(1, 'resourcePrice') * $aw['s1'];
		$shipPrice += ShipResource::getInfo(2, 'resourcePrice') * $aw['s2'];
		$shipPrice += ShipResource::getInfo(3, 'resourcePrice') * $aw['s3'];
		$shipPrice += ShipResource::getInfo(4, 'resourcePrice') * $aw['s4'];
		$shipPrice += ShipResource::getInfo(5, 'resourcePrice') * $aw['s5'];
		$shipPrice += ShipResource::getInfo(6, 'resourcePrice') * $aw['s6'];
		$shipPrice += ShipResource::getInfo(7, 'resourcePrice') * $aw['s7'];
		$shipPrice += ShipResource::getInfo(8, 'resourcePrice') * $aw['s8'];
		$shipPrice += ShipResource::getInfo(9, 'resourcePrice') * $aw['s9'];
		$shipPrice += ShipResource::getInfo(10, 'resourcePrice') * $aw['s10'];
		$shipPrice += ShipResource::getInfo(11, 'resourcePrice') * $aw['s11'];
		$points = round($shipPrice * COEF_RESOURCE);
		$list[$aw['player']]['general'] += $points;

		$pevQuantity = 0;
		$pevQuantity += ShipResource::getInfo(0, 'pev') * $aw['s0'];
		$pevQuantity += ShipResource::getInfo(1, 'pev') * $aw['s1'];
		$pevQuantity += ShipResource::getInfo(2, 'pev') * $aw['s2'];
		$pevQuantity += ShipResource::getInfo(3, 'pev') * $aw['s3'];
		$pevQuantity += ShipResource::getInfo(4, 'pev') * $aw['s4'];
		$pevQuantity += ShipResource::getInfo(5, 'pev') * $aw['s5'];
		$pevQuantity += ShipResource::getInfo(6, 'pev') * $aw['s6'];
		$pevQuantity += ShipResource::getInfo(7, 'pev') * $aw['s7'];
		$pevQuantity += ShipResource::getInfo(8, 'pev') * $aw['s8'];
		$pevQuantity += ShipResource::getInfo(9, 'pev') * $aw['s9'];
		$pevQuantity += ShipResource::getInfo(10, 'pev') * $aw['s10'];
		$pevQuantity += ShipResource::getInfo(11, 'pev') * $aw['s11'];
		$list[$aw['player']]['armies'] += $pevQuantity;
	}
}

#-------------------------------- BUTCHER RANKING --------------------------------#
# load the reports
$qr = $db->prepare('SELECT
		p.id AS player,
		(SUM(pevInBeginA) - SUM(`pevAtEndA`)) AS lostPEV,
		(SUM(pevInBeginD) - SUM(`pevAtEndD`)) AS destroyedPEV
	FROM report AS r
	RIGHT JOIN player AS p
		ON p.id = r.rPlayerAttacker
	WHERE p.statement = ? OR p.statement = ? OR p.statement = ?
	GROUP BY p.id
	ORDER BY p.id');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$list[$aw['player']]['butcherDestroyedPEV'] += $aw['destroyedPEV'];
		$list[$aw['player']]['butcherLostPEV'] += $aw['lostPEV'];
		$list[$aw['player']]['butcher'] += $aw['destroyedPEV'] - $aw['lostPEV'];
	}
}

$qr = $db->prepare('SELECT
		p.id AS player,
		(SUM(pevInBeginD) - SUM(`pevAtEndD`)) AS lostPEV,
		(SUM(pevInBeginA) - SUM(`pevAtEndA`)) AS destroyedPEV,
		((SUM(pevInBeginD) - SUM(`pevAtEndD`)) - (SUM(pevInBeginA) - SUM(`pevAtEndA`))) AS score
	FROM report AS r
	RIGHT JOIN player AS p
		ON p.id = r.rPlayerDefender
	WHERE p.statement = ? OR p.statement = ? OR p.statement = ?
	GROUP BY p.id
	ORDER BY p.id');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$list[$aw['player']]['butcherDestroyedPEV'] += $aw['destroyedPEV'];
		$list[$aw['player']]['butcherLostPEV'] += $aw['lostPEV'];
		$list[$aw['player']]['butcher'] += $aw['destroyedPEV'] - $aw['lostPEV'];
	}
}

#-------------------------------- TRADER RANKING --------------------------------#
# load the commercial routes
$qr = $db->prepare('SELECT 
		p.id AS player,
		SUM(income) AS income
	FROM commercialRoute AS c
	LEFT JOIN orbitalBase AS o
		ON o.rPlace = c.rOrbitalBase
		RIGHT JOIN player AS p
			ON p.id = o.rPlayer
	WHERE (p.statement = ? OR p.statement = ? OR p.statement = ?) AND c.statement = ?
	GROUP BY p.id
	ORDER BY p.id');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY, CRM_ACTIVE));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$list[$aw['player']]['trader'] += $aw['income'];
	}
}

$qr = $db->prepare('SELECT 
		p.id AS player,
		SUM(income) AS income
	FROM `commercialRoute` AS c
	LEFT JOIN orbitalBase AS o
		ON o.rPlace = c.rOrbitalBaseLinked
		RIGHT JOIN player AS p
			ON p.id = o.rPlayer
	WHERE (p.statement = ? OR p.statement = ? OR p.statement = ?) AND c.statement = ?
	GROUP BY p.id
	ORDER BY p.id');
$qr->execute(array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY, CRM_ACTIVE));

while ($aw = $qr->fetch()) {
	if (isset($list[$aw['player']])) {
		$list[$aw['player']]['trader'] += $aw['income'];
	}
}

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
	ASM::$pam->getById($player)->factionPoint = $pr->general;

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
	$pr->butcherDestroyedPEV = $listB[$player]['butcherDestroyedPEV'];
	$pr->butcherLostPEV = $listB[$player]['butcherLostPEV'];
	$pr->butcherPosition = $listB[$player]['position'];
	$pr->butcherVariation = $firstRanking ? 0 : $oldRanking->butcherPosition - $pr->butcherPosition;

	$pr->trader = $listT[$player]['trader'];
	$pr->traderPosition = $listT[$player]['position'];
	$pr->traderVariation = $firstRanking ? 0 : $oldRanking->traderPosition - $pr->traderPosition;

	ASM::$prm->add($pr);
}

ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);
?>