<?php
# daily cron
# call at x am. every day

include_once ATHENA;
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

function cmpPoints($a, $b) {
    if ($a['points'] == $b['points']) {
        return 0;
    }
    return ($a['points'] > $b['points']) ? -1 : 1;
}

# load the factions (colors)
ASM::$clm->load(array('isInGame' => 1));

# create an array with all the factions
$gameover = FALSE;
$list = [];
for ($i = 0; $i < ASM::$clm->size(); $i++) {
	$list[ASM::$clm->get($i)->id] = array(
		'general' => 0, 
		'wealth' => 0, 
		'territorial' => 0,
		'points' => ASM::$clm->get($i)->rankingPoints);
	if (ASM::$clm->get($i)->isWinner == 1) {
		$gameover = TRUE;
	}
}
const COEF_RESOURCE = 0.001;

#-------------------------------- GENERAL RANKING --------------------------------#
# sum of general player ranking
# load all the players
ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

for ($i = 0; $i < ASM::$prm->size(); $i++) {
	$playerRank = ASM::$prm->get($i);

	$player = ASM::$pam->getById($playerRank->rPlayer);

	if (isset($player->rColor)) {
		$list[$player->rColor]['general'] += $playerRank->general;
	}
}

#-------------------------------- WEALTH RANKING ----------------------------------#
$db = DataBase::getInstance();

for ($i = 0; $i < ASM::$clm->size(); $i++) { 
	$color = ASM::$clm->get($i)->id;
	$qr = $db->prepare('SELECT
		COUNT(cr.id) AS nb,
		SUM(cr.income) AS income
		FROM commercialRoute AS cr
		LEFT JOIN orbitalBase AS ob1
			ON cr.rOrbitalBase = ob1.rPlace
			LEFT JOIN player AS pl1
				ON ob1.rPlayer = pl1.id
		LEFT JOIN orbitalBase AS ob2
			ON cr.rOrbitalBaseLinked = ob2.rPlace
			LEFT JOIN player AS pl2
				ON ob2.rPlayer = pl2.id
		WHERE (pl1.rColor = ? OR pl2.rColor = ?) AND cr.statement = ?
	');
	# hint : en fait ça compte qu'une fois une route interfaction, mais chut
	$qr->execute([$color, $color, CRM_ACTIVE]);
	$aw = $qr->fetch();
	if ($aw['income'] == NULL) {
		$income = 0;
	} else {
		$income = $aw['income'];
	}
	$list[$color]['wealth'] = $income;
}

#-------------------------------- TERRITORIAL RANKING -----------------------------#

$sectorManager = new SectorManager();
$sectorManager->load();
for ($i = 0; $i < $sectorManager->size(); $i++) {
	$sector = $sectorManager->get($i);
	if ($sector->rColor != 0) {
		$list[$sector->rColor]['territorial'] += $sector->points;
	}
}

#---------------- COMPUTING -------------------#

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

#-------------------------------- POINTS RANKING -----------------------------#

# faire ce classement uniquement après x jours de jeu
if (Utils::interval(SERVER_START_TIME, Utils::now(), 'h') > HOURS_BEFORE_START_OF_RANKING) {
	# points qu'on gagne en fonction de sa place dans le classement
	$pointsToEarn = [40, 20, 10, 0, 0, 0, 0, 0, 0, 0, 0];
	$coefG = 0.3; # 12 6 3 0 0 0 ...
	$coefW = 0.2; # 8 4 2 0 0 0 ...
	$coefT = 0.5; # 20 10 5 0 0 0 ...

	for ($i = 0; $i < ASM::$clm->size(); $i++) {
		$faction = ASM::$clm->get($i)->id;
		$additionalPoints = 0;

		# general
		$additionalPoints += intval($pointsToEarn[$listG[$faction]['position'] - 1] * $coefG);

		# wealth
		$additionalPoints += intval($pointsToEarn[$listW[$faction]['position'] - 1] * $coefW);

		# territorial
		$additionalPoints += intval($pointsToEarn[$listT[$faction]['position'] - 1] * $coefT);

		$list[$faction]['points'] += $additionalPoints;
	}
}


#---------------- LAST COMPUTING -------------------#

$listP = $list;
uasort($listP, 'cmpPoints');

$position = 1;
foreach ($listP as $key => $value) { $listP[$key]['position'] = $position++;}

#---------------- SAVING -------------------#

$rankings = [];

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

	if ($gameover) {
		$fr->points = $oldRanking->points;
		$fr->pointsPosition = $oldRanking->pointsPosition;
		$fr->pointsVariation = 0;
		$fr->newPoints = 0;
	} else {
		$fr->points = $listP[$faction]['points'];
		$fr->pointsPosition = $listP[$faction]['position'];
		$fr->pointsVariation = $firstRanking ? 0 : $oldRanking->pointsPosition - $fr->pointsPosition;
		$fr->newPoints = $firstRanking ? $fr->points : $fr->points - $oldRanking->points;
	}

	# update faction infos
	ASM::$clm->getById($faction)->rankingPoints = $listP[$faction]['points'];
	ASM::$clm->getById($faction)->points = $listG[$faction]['general'];
	ASM::$clm->getById($faction)->sectors = $listT[$faction]['territorial'];
	ASM::$clm->updateInfos($faction);

	$rankings[] = $fr;
	ASM::$frm->add($fr);
}

ASM::$clm->changeSession($S_CLM1);
ASM::$pam->changeSession($S_PAM1);
ASM::$prm->changeSession($S_PRM1);
ASM::$frm->changeSession($S_FRM1);

if ($gameover == FALSE) {
	# check if a faction wins the game
	$winRanking = NULL;
	foreach ($rankings as $ranking) {
		if ($ranking->points >= POINTS_TO_WIN) {
			if ($winRanking !== NULL) {
				if ($winRanking->points < $ranking->points) {
					$winRanking = $ranking;
				}
			} else {
				$winRanking = $ranking;
			}
		}
	}
	if ($winRanking !== NULL) {
		# there is a winner !!!
		$S_CLM2 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession(FALSE);
		ASM::$clm->load(array('id' => $winRanking->rFaction));

		ASM::$clm->get()->isWinner = Color::WIN;

		# envoyer un message de Jean-Mi
		$winnerName = ColorResource::getInfo(ASM::$clm->get()->id, 'officialName');
		$content = 'Salut,<br /><br />La victoire vient d\'être remportée par : <br /><strong>' . $winnerName . '</strong><br />';
		$content .= 'Cette faction a atteint les ' . POINTS_TO_WIN . ' points, la partie est donc terminée.<br /><br />Bravo et un grand merci à tous les participants !';

		include_once HERMES;
		$S_CVM1 = ASM::$cvm->getCurrentSession();
		ASM::$cvm->newSession();
		ASM::$cvm->load(
			['cu.rPlayer' => ID_JEANMI]
		);

		if (ASM::$cvm->size() == 1) {
			$conv = ASM::$cvm->get();

			$conv->messages++;
			$conv->dLastMessage = Utils::now();

			# désarchiver tous les users
			$users = $conv->players;
			foreach ($users as $user) {
				$user->convStatement = ConversationUser::CS_DISPLAY;
			}

			# création du message
			$message = new ConversationMessage();

			$message->rConversation = $conv->id;
			$message->rPlayer = ID_JEANMI;
			$message->type = ConversationMessage::TY_STD;
			$message->content = $content;
			$message->dCreation = Utils::now();
			$message->dLastModification = NULL;

			ASM::$cme->add($message);
		} else {
			CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
		}

		ASM::$clm->changeSession($S_CLM2);
		ASM::$cvm->changeSession($S_CVM1);
	}
}

?>