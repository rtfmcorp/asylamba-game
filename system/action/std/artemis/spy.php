<?php
# send a spy

# int rplace 		id of the place to spy
# int price			credit price for spying

include_once GAIA;
include_once ARTEMIS;
include_once ZEUS;

$rPlace = Utils::getHTTPData('rplace');
$price 	= Utils::getHTTPData('price');

if ($rPlace !== FALSE AND $price !== FALSE) {
	$price = intval($price);
	$price = $price > 0 ? $price : 0;
	$price = $price < 1000000 ? $price : 0;
	
	if (CTR::$data->get('playerInfo')->get('credit') >= $price && $price > 0) {
		# place
		$S_PLM1 = ASM::$plm->getCurrentSession();
		ASM::$plm->newSession();
		ASM::$plm->load(array('id' => $rPlace));
		$place = ASM::$plm->get();

		if ($place->typeOfPlace == Place::TERRESTRIAL && $place->playerColor != CTR::$data->get('playerInfo')->get('color')) {
			# débit des crédits au joueur
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
			ASM::$pam->get()->decreaseCredit($price);
			ASM::$pam->changeSession($S_PAM1);

			# espionnage
			$sr = new SpyReport();
			$sr->rPlayer = CTR::$data->get('playerId');
			$sr->rPlace = $rPlace;
			$sr->price = $price;
			$sr->placeColor = $place->playerColor;
			$sr->typeOfBase = $place->typeOfBase;
			$sr->placeName = $place->baseName;
			$sr->points = $place->points;
			$sr->shipsInStorage = serialize([]);
			$sr->antiSpyInvest = 0;
			$sr->commercialRouteIncome = 0;

			$sr->dSpying = Utils::now();

			switch ($place->typeOfBase) {
				case Place::TYP_EMPTY:
					$sr->resources = $place->resources;

					$sr->typeOfOrbitalBase = OrbitalBase::TYP_NEUTRAL;
					$sr->rEnemy = 0;
					$sr->enemyName = 'Rebelle';
					$sr->enemyAvatar = '';
					$sr->enemyLevel = 1;

					# generate a commander for the place
					$commander = $place->createVirtualCommander();

					$commandersArray = array();
					$commandersArray[0]['name'] = $commander->name;
					$commandersArray[0]['avatar'] = $commander->avatar;
					$commandersArray[0]['level'] = $commander->level;
					$commandersArray[0]['line'] = $commander->line;
					$commandersArray[0]['statement'] = $commander->statement;
					$commandersArray[0]['pev'] = $commander->getPev();
					$commandersArray[0]['army'] = $commander->getNbrShipByType();
					
					$sr->commanders = serialize($commandersArray);

					$antiSpy = $place->population * 20; // population : entre 0 et 250
					$sr->success = Game::getSpySuccess($antiSpy, $price);
					$sr->type = SpyReport::TYP_NOT_CAUGHT;

					break;
				case Place::TYP_ORBITALBASE:
					# orbitalBase
					$S_OBM1 = ASM::$obm->getCurrentSession();
					ASM::$obm->newSession();
					ASM::$obm->load(array('rPlace' => $rPlace));
					$orbitalBase = ASM::$obm->get();

					# enemy
					ASM::$pam->newSession();
					ASM::$pam->load(array('id' => $orbitalBase->rPlayer));
					$enemy = ASM::$pam->get();

					# rc
					$S_CRM1 = ASM::$crm->getCurrentSession();
					ASM::$crm->changeSession($orbitalBase->routeManager);
					$RCIncome = 0;
					for ($i = 0; $i < ASM::$crm->size(); $i++) {
						if (ASM::$crm->get($i)->getStatement() == CRM_ACTIVE) {
							$RCIncome += ASM::$crm->get($i)->getIncome();
						} 
					}
					
					$sr->resources = $orbitalBase->resourcesStorage;

					$sr->typeOfOrbitalBase = $orbitalBase->typeOfBase;
					$sr->rEnemy = $orbitalBase->rPlayer;
					$sr->enemyName = $enemy->name;
					$sr->enemyAvatar = $enemy->avatar;
					$sr->enemyLevel = $enemy->level;

					$sr->shipsInStorage = serialize($orbitalBase->shipStorage);
					$sr->antiSpyInvest = $orbitalBase->iAntiSpy;
					$sr->commercialRouteIncome = $RCIncome;

					$commandersArray = array();
					$S_COM1 = ASM::$com->getCurrentSession();
					ASM::$com->newSession();
					ASM::$com->load(array('rBase' => $rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));

					for ($i = 0; $i < ASM::$com->size(); $i++) { 
						$commandersArray[$i]['name'] = ASM::$com->get($i)->name;
						$commandersArray[$i]['avatar'] = ASM::$com->get($i)->avatar;
						$commandersArray[$i]['level'] = ASM::$com->get($i)->level;
						$commandersArray[$i]['line'] = ASM::$com->get($i)->line;
						$commandersArray[$i]['statement'] = ASM::$com->get($i)->statement;
						$commandersArray[$i]['pev'] = ASM::$com->get($i)->getPev();
						$commandersArray[$i]['army'] = ASM::$com->get($i)->getNbrShipByType();
					}
					$sr->commanders = serialize($commandersArray);
					
					$antiSpy = $orbitalBase->antiSpyAverage; // entre 100 et 4000
					$sr->success = Game::getSpySuccess($antiSpy, $price);
					$sr->type = Game::getTypeOfSpy($sr->success);

					switch ($sr->type) {
						case SpyReport::TYP_ANONYMOUSLY_CAUGHT:
							$n = new Notification();
							$n->setRPlayer($orbitalBase->rPlayer);
							$n->setTitle('Espionnage détecté');
							$n->addBeg();
							$n->addTxt('Un joueur a espionné votre base ');
							$n->addLnk('map/base-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
							$n->addBrk()->addTxt('Malheureusement, nous n\'avons pas pu connaître l\'identité de l\'espion.');
							$n->addEnd();
							ASM::$ntm->add($n);
							break;
						case SpyReport::TYP_CAUGHT:
							$n = new Notification();
							$n->setRPlayer($orbitalBase->rPlayer);
							$n->setTitle('Espionnage intercepté');
							$n->addBeg();
							$n->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'))->addTxt(' a espionné votre base ');
							$n->addLnk('map/base-' . $orbitalBase->rPlace, $orbitalBase->name)->addTxt('.');
							$n->addBrk()->addTxt('L\'espion s\'est fait attrapé en train de fouiller dans vos affaires.');
							$n->addEnd();
							ASM::$ntm->add($n);
							break;
						default:
							break;
					}

					ASM::$crm->changeSession($S_CRM1);
					ASM::$com->changeSession($S_COM1);
					ASM::$obm->changeSession($S_OBM1);

					break;
				default:
					CTR::$alert->add('espionnage pour vaisseau-mère pas encore implémenté', ALERT_STD_ERROR);
			}

			ASM::$srm->add($sr);

			# tutorial
			if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
				switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
					case TutorialResource::SPY_PLANET:
						TutorialHelper::setStepDone();
						break;
				}
			}

			CTR::$alert->add('Espionnage effectué.', ALERT_STD_SUCCESS);
			CTR::redirect('fleet/view-spyreport/report-' . $sr->id);
			
			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('Impossible de lancer un espionnage', ALERT_STD_ERROR);
		}

		ASM::$plm->changeSession($S_PLM1);
	} else {
		CTR::$alert->add('Impossible de lancer un espionnage avec le montant proposé', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour espionner', ALERT_STD_FILLFORM);
}