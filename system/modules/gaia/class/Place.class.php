<?php
/**
 * Place
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 21.04.13
*/

class Place { 
	# CONSTANTS
	const TYP_EMPTY = 0;
	const TYP_MS1 = 1;
	const TYP_MS2 = 2;
	const TYP_MS3 = 3;
	const TYP_ORBITALBASE = 4;
	const COEFFMAXRESOURCE = 600;
	const COEFFRESOURCE = 2;
	const REPOPDANGER = 2;

	# typeOfPlace
	const TERRESTRIAL = 1;
	const EMPTYZONE = 6; # zone vide

	# CONST PNJ COMMANDER
	const LEVELMAXVCOMMANDER = 15;
	const POPMAX 			 = 250;

	# CONST RESULT BATTLE
	const CHANGESUCCESS 						= 10;
	const CHANGEFAIL							= 11;
	const CHANGELOST							= 12;

	const LOOTEMPTYSSUCCESS 					= 20;
	const LOOTEMPTYFAIL							= 21;
	const LOOTPLAYERWHITBATTLESUCCESS			= 22;
	const LOOTPLAYERWHITBATTLEFAIL				= 23;
	const LOOTPLAYERWHITOUTBATTLESUCCESS		= 24;
	const LOOTLOST								= 27;

	const CONQUEREMPTYSSUCCESS 					= 30;
	const CONQUEREMPTYFAIL						= 31;
	const CONQUERPLAYERWHITBATTLESUCCESS		= 32;
	const CONQUERPLAYERWHITBATTLEFAIL			= 33;
	const CONQUERPLAYERWHITOUTBATTLESUCCESS		= 34;
	const CONQUERLOST							= 37;

	const COMEBACK 								= 40;

	# constante de danger
	const DNG_CASUAL 							= 10;
	const DNG_EASY 								= 20;
	const DNG_MEDIUM 							= 50;
	const DNG_HARD 								= 75;
	const DNG_VERY_HARD 						= 100;

	// PLACE
	public $id = 0;
	public $rPlayer = NULL;
	public $rSystem = 0;
	public $typeOfPlace = 0;
	public $position = 0;
	public $population = 0;
	public $coefResources = 0;
	public $coefHistory = 0;
	public $resources = 0; 						# de la place si $typeOfBase = 0, sinon de la base
	public $danger = 0;							# danger actuel de la place (force des flottes rebelles)
	public $maxDanger = 0;							# danger max de la place (force des flottes rebelles)
	public $uPlace = '';

	// SYSTEM
	public $rSector = 0;
	public $xSystem = 0;
	public $ySystem = 0;
	public $typeOfSystem = 0;

	// SECTOR
	public $tax = 0;
	public $sectorColor = 0;

	// PLAYER
	public $playerColor = 0;
	public $playerName = '';
	public $playerAvatar = '';
	public $playerStatus = 0;
	public $playerLevel = 0;

	// BASE
	public $typeOfBase = 0; // 0=empty, 1=ms1, 2=ms2, 3=ms3, 4=ob
	public $typeOfOrbitalBase;
	public $baseName = '';
	public $points = '';

	// OB
	public $levelCommercialPlateforme = 0;
	public $levelSpatioport = 0;
	public $antiSpyInvest = 0;

	// COMMANDER 
	public  $commanders = array();

	//uMode
	public $uMode = TRUE;

	public function getId() 							{ return $this->id; }
	public function getRPlayer() 						{ return $this->rPlayer; }
	public function getRSystem() 						{ return $this->rSystem; }
	public function getTypeOfPlace() 					{ return $this->typeOfPlace; }
	public function getPosition() 						{ return $this->position; }
	public function getPopulation() 					{ return $this->population; }
	public function getCoefResources() 					{ return $this->coefResources; }
	public function getCoefHistory() 					{ return $this->coefHistory; }
	public function getResources() 						{ return $this->resources; }
	public function getRSector() 						{ return $this->rSector; }
	public function getXSystem() 						{ return $this->xSystem; }
	public function getYSystem() 						{ return $this->ySystem; }
	public function getTypeOfSystem() 					{ return $this->typeOfSystem; }
	public function getTax() 							{ return $this->tax; }
	public function getSectorColor() 					{ return $this->sectorColor; }
	public function getPlayerColor() 					{ return $this->playerColor; }
	public function getPlayerName() 					{ return $this->playerName; }
	public function getPlayerAvatar() 					{ return $this->playerAvatar; }
	public function getPlayerStatus() 					{ return $this->playerStatus; }
	public function getTypeOfBase() 					{ return $this->typeOfBase; }
	public function getBaseName() 						{ return $this->baseName; }
	public function getPoints() 						{ return $this->points; }
	public function getLevelCommercialPlateforme() 		{ return $this->levelCommercialPlateforme; }
	public function getLevelSpatioport() 				{ return $this->levelSpatioport; }
	public function getAntiSpyInvest()					{ return $this->antiSpyInvest; }

	public function setId($v) 							{ $this->id = $v; }
	public function setRPlayer($v) 						{ $this->rPlayer = $v; }
	public function setRSystem($v) 						{ $this->rSystem = $v; }
	public function setTypeOfPlace($v) 					{ $this->typeOfPlace = $v; }
	public function setPosition($v) 					{ $this->position = $v; }
	public function setPopulation($v) 					{ $this->population = $v; }
	public function setCoefResources($v) 				{ $this->coefResources = $v; }
	public function setCoefHistory($v) 					{ $this->coefHistory = $v; }
	public function setResources($v) 					{ $this->resources = $v; }
	public function setRSector($v) 						{ $this->rSector = $v; }
	public function setXSystem($v) 						{ $this->xSystem = $v; }
	public function setYSystem($v) 						{ $this->ySystem = $v; }
	public function setTypeOfSystem($v) 				{ $this->typeOfSystem = $v; }
	public function setTax($v) 							{ $this->tax = $v; }
	public function setSectorColor($v) 					{ $this->sectorColor = $v; }
	public function setPlayerColor($v) 					{ $this->playerColor = $v; }
	public function setPlayerName($v) 					{ $this->playerName = $v; }
	public function setPlayerAvatar($v) 				{ $this->playerAvatar = $v; }
	public function setPlayerStatus($v) 				{ $this->playerStatus = $v; }
	public function setTypeOfBase($v) 					{ $this->typeOfBase = $v; }
	public function setBaseName($v) 					{ $this->baseName = $v; }
	public function setPoints($v) 						{ $this->points = $v; }
	public function setLevelCommercialPlateforme($v) 	{ $this->levelCommercialPlateforme = $v; }
	public function setLevelSpatioport($v) 				{ $this->levelSpatioport = $v; }
	public function setAntiSpyInvest($v)				{ $this->antiSpyInvest = $v; }

	public function uMethod() {
		$token = CTC::createContext('place');
		$now   = Utils::now();

		if (Utils::interval($this->uPlace, $now, 's') > 0) {
			# update time
			$days = Utils::intervalDates($now, $this->uPlace, 'd');
			$hours = Utils::intervalDates($now, $this->uPlace);
			$this->uPlace = $now;

			# RESOURCE
			if ($this->typeOfBase == self::TYP_EMPTY && $this->typeOfPlace == self::TERRESTRIAL) {
				foreach ($days as $key => $day) {
					CTC::add($day, $this, 'uResources', array());
				}
			}

			if ($this->rPlayer == NULL) {
				foreach ($hours as $hour) {
					CTC::add($hour, $this, 'uDanger', array());
				}
			}

			include_once ARES;

			$S_COM_PLACE1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(
				array(
					'c.rDestinationPlace' => $this->id,
					'c.statement' => 2
				),
				array('c.dArrival', 'ASC')
			);

			if (ASM::$com->size() > 0) {
				include_once ATHENA;
				include_once ZEUS;
				include_once DEMETER;

				$places = array();
				$playerBonuses = array();
				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					$c = ASM::$com->get($i);
					# fill the places
					$places[] = $c->getRBase();
					# fill&load the bonuses if needed
					if (!array_key_exists($c->rPlayer, $playerBonuses)) {
						$bonus = new PlayerBonus($c->rPlayer);
						$bonus->load();
						$playerBonuses[$c->rPlayer] = $bonus;
					}
				}

				# load all the places at the same time
				$S_PLM1 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $places));

				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					$commander = ASM::$com->get($i);

					switch ($commander->travelType) {
						case Commander::MOVE: 
							if ($commander->dArrival <= $now AND $commander->rDestinationPlace != NULL) {					
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];
								CTC::add($commander->dArrival, $this, 'uChangeBase', array($commander, $place, $bonus));
							}
							break;

						case Commander::LOOT: 
							if ($commander->dArrival <= $now AND $commander->rDestinationPlace != NULL) {	
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];

								$S_PAM1 = ASM::$pam->getCurrentSession();
								ASM::$pam->newSession();
								ASM::$pam->load(array('id' => $commander->rPlayer));
								$commanderPlayer = ASM::$pam->get();
								ASM::$pam->changeSession($S_PAM1);

								if ($this->rPlayer != NULL) {
									$S_PAM1 = ASM::$pam->getCurrentSession();
									ASM::$pam->newSession();
									ASM::$pam->load(array('id' => $this->rPlayer));
									$placePlayer = ASM::$pam->get();
									ASM::$pam->changeSession($S_PAM1);

									$S_OBM1 = ASM::$obm->getCurrentSession();
									ASM::$obm->newSession();
									ASM::$obm->load(array('rPlace' => $this->id));
									$placeBase = ASM::$obm->get();
									ASM::$obm->changeSession($S_OBM1);
								} else {
									$placePlayer = NULL;
									$placeBase = NULL;
								}

								$S_CLM = ASM::$clm->getCurrentSession();
								ASM::$clm->newSession();
								ASM::$clm->load(array('id' => $commander->playerColor));
								$commanderColor = ASM::$clm->get();
								ASM::$clm->changeSession($S_CLM);

								CTC::add($commander->dArrival, $this, 'uLoot', array($commander, $place, $bonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor));
							}
							break;

						case Commander::COLO: 
							if ($commander->dArrival <= $now AND $commander->rDestinationPlace != NULL) {					
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];

								$S_PAM1 = ASM::$pam->getCurrentSession();
								ASM::$pam->newSession();
								ASM::$pam->load(array('id' => $commander->rPlayer));
								$commanderPlayer = ASM::$pam->get();
								ASM::$pam->changeSession($S_PAM1);

								if ($this->rPlayer != NULL) {
									$S_PAM2 = ASM::$pam->getCurrentSession();
									ASM::$pam->newSession();
									ASM::$pam->load(array('id' => $this->rPlayer));
									$placePlayer = ASM::$pam->get();
									ASM::$pam->changeSession($S_PAM2);

									$S_OBM1 = ASM::$obm->getCurrentSession();
									ASM::$obm->newSession();
									ASM::$obm->load(array('rPlace' => $this->id));
									$placeBase = ASM::$obm->get();
									ASM::$obm->changeSession($S_OBM1);
								} else {
									$placePlayer = NULL;
									$placeBase = NULL;
								}

								$S_CLM = ASM::$clm->getCurrentSession();
								ASM::$clm->newSession();
								ASM::$clm->load(array('id' => $commander->playerColor));
								$commanderColor = ASM::$clm->get();
								ASM::$clm->changeSession($S_CLM);

								CTC::add($commander->dArrival, $this, 'uConquer', array($commander, $place, $bonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor));
							}
							break;

						case Commander::BACK: 
							if ($commander->dArrival <= $now AND $commander->rDestinationPlace != NULL) {					
								$S_OBM1 = ASM::$obm->getCurrentSession();

								ASM::$obm->newSession(FALSE);
								ASM::$obm->load(array('rPlace' => $commander->getRBase()));
								$base = ASM::$obm->get();
								ASM::$obm->changeSession($S_OBM1);

								CTC::add($commander->dArrival, $this, 'uComeBackHome', array($commander, $base));
							}
							break;
						default: 
							CTR::$alert->add('Cette action n\'existe pas.', ALT_BUG_INFO);
					}
				}
				ASM::$plm->changeSession($S_PLM1);
			}
			ASM::$com->changeSession($S_COM_PLACE1);
		}
		CTC::applyContext($token);
	}

	public function uDanger() {
		$this->danger += 2;

		if ($this->danger > $this->maxDanger) {
			$this->danger = $this->maxDanger;
		}
	}

	public function uResources() {
		$maxResources = ($this->population / 50) * self::COEFFMAXRESOURCE * $this->maxDanger;
		$this->resources += floor(self::COEFFRESOURCE * $this->population * 24);

		if ($this->resources > $maxResources) {
			$this->resources = $maxResources;
		}
	}

	# se poser
	public function uChangeBase($commander, $commanderPlace, $playerBonus) {
		# si la place et le commander ont le même joueur
		if ($this->playerColor == $commander->playerColor AND $this->typeOfBase == 4) {
			$maxCom = OrbitalBase::MAXCOMMANDERSTANDARD;
			if ($this->typeOfOrbitalBase == OrbitalBase::TYP_MILITARY || $this->typeOfOrbitalBase == OrbitalBase::TYP_CAPITAL) {
				$maxCom = OrbitalBase::MAXCOMMANDERMILITARY;
			}
			# si place a assez de case libre :
			if (count($this->commanders) < $maxCom) {
				$comLine2 = 0;

				foreach ($this->commanders as $com) {
					if ($com->line == 2) {
						$comLine2++;
					}
				}

				if ($comLine2 < 2) {
					$commander->line = 2;
				} else {
					$commander->line = 1;
				}

				# instance de la place d'envoie + suppr commandant de ses flottes
				# enlever à rBase le commandant
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->id == $commander->id) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
				
				# changer rBase commander
				$commander->rBase = $this->id;
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				// $commander->rStartPlace = NULL;
				// $commander->dArrival = NULL;

				#modifier le rPlayer (ne se modifie pas si c'est le même)
				$commander->rPlayer = $this->rPlayer;

				$commander->statement = Commander::AFFECTED;

				# ajouter à $this le commandant
				$this->commanders[] = $commander;

				# envoie de notif
				$this->sendNotif(self::CHANGESUCCESS, $commander);
			} else {
				# instance de la place d'envoie + suppr commandant de ses flottes
				# enlever à rBase le commandant
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->id == $commander->id) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
				
				# changer rBase commander
				$commander->rBase = $this->id;
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				// $commander->rStartPlace = NULL;
				// $commander->dArrival = NULL;

				$commander->emptySquadrons();

				$commander->statement = Commander::INSCHOOL;

				# envoie de notif
				$this->sendNotif(self::CHANGEFAIL, $commander);
			}
		} else {
			$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());

			$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
			$commander->startPlaceName = $this->baseName;
			$commander->destinationPlaceName = $commander->oBName;
			$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
			

			$this->sendNotif(self::CHANGELOST, $commander);
		}
	}

	# piller
	public function uLoot($commander, $commanderPlace, $playerBonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor) {
		LiveReport::$type = Commander::LOOT;
		LiveReport::$dFight = $commander->dArrival;

		if ($this->rPlayer == NULL) {
			LiveReport::$isLegal = Report::LEGAL;
			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			// $commander->dArrival = NULL;

			# planète vide -> faire un combat
			$this->startFight($commander, $commanderPlayer);

			# si gagné
			if ($commander->getStatement() != Commander::DEAD) {
				# piller la planète
				$this->lootAnEmptyPlace($commander, $playerBonus);
				# comeBackToHome
				
				$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());

				$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
				$commander->startPlaceName = $this->baseName;
				$commander->destinationPlaceName = $commander->oBName;
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
				
				#création du rapport
				$report = $this->createReport();
				$percentage = (($report->PevAtEndD + 1) / ($report->setPevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->sendNotif(self::LOOTEMPTYSSUCCESS, $commander, $report->id);
			} else {
				# si il est mort
				# enlever le commandant de la session
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
				
				#création du rapport
				$report = $this->createReport();
				$percentage = (($report->PevAtEndD + 1) / ($report->setPevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->sendNotif(self::LOOTEMPTYFAIL, $commander, $report->id);
			}
		# si il y a une base
		} else {
			if ($commanderColor->colorLink[$this->playerColor] == Color::ALLY) {
				LiveReport::$isLegal = Report::ILLEGAL;
			} else {
				LiveReport::$isLegal = Report::LEGAL;
			}
			# planète à joueur: si $this->rColor != commandant->rColor
			if ($this->playerColor != $commander->getPlayerColor() && $this->playerLevel > 1) {
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				$commander->travelLength = NULL;
				// $commander->rStartPlace = NULL;
				// $commander->dArrival = NULL;

				$dCommanders = array();
				foreach ($this->commanders AS $dCommander) {
					if ($dCommander->statement == Commander::AFFECTED && $dCommander->line == 1) {
						$dCommanders[] = $dCommander;
					}
				}

				if (count($dCommanders) != 0) {
				# il y a des commandants en défense : faire un combat avec un des commandants
					$aleaNbr = rand(0, count($dCommanders) - 1);
					$this->startFight($commander, $commanderPlayer, $dCommanders[$aleaNbr], $placePlayer, TRUE);

					# si il gagne
					if ($commander->getStatement() != COM_DEAD) {
						// piller la planète
						$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);
						// comeBackToHome
						
						$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());

						$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
						$commander->startPlaceName = $this->baseName;
						$commander->destinationPlaceName = $commander->oBName;
						$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
						
						unset($this->commanders[$aleaNbr]);
						$this->commanders = array_merge($this->commanders);

						# création du rapport
						$report = $this->createReport();

						$this->sendNotif(self::LOOTPLAYERWHITBATTLESUCCESS, $commander, $report->id);

					} else {
					# s'il est mort
						# enlever le commandant de la session
						for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
							if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
								unset($commanderPlace->commanders[$i]);
								$commanderPlace->commanders = array_merge($commanderPlace->commanders);
							}
						}

						# ajouter du prestige au défenseur synelectique
						if ($this->playerColor == ColorResource::SYNELLE) {
							$placePlayer->factionPoint += Color::POINTDEFEND;
						}

						# création du rapport
						$report = $this->createReport();

						$this->sendNotif(self::LOOTPLAYERWHITBATTLEFAIL, $commander, $report->id);
					}
				} else {
					$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);

					$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());

					$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
					$commander->startPlaceName = $this->baseName;
					$commander->destinationPlaceName = $commander->oBName;
					$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);

					$this->sendNotif(self::LOOTPLAYERWHITOUTBATTLESUCCESS, $commander);
				}
			# si c'est a même couleur
			} else {
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				$commander->travelLength = NULL;
				// $commander->rStartPlace = NULL;
				
				$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());

				$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
				$commander->startPlaceName = $this->baseName;
				$commander->destinationPlaceName = $commander->oBName;
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
				
				$this->sendNotif(self::LOOTLOST, $commander);
			}
		}
	}

	# conquest
	public function uConquer($commander, $commanderPlace, $playerBonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor) {

		if ($this->rPlayer != NULL) {
			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			// $commander->dArrival = NULL;

			if ($this->playerColor != $commander->getPlayerColor() && $this->playerLevel > 3) {
				for ($i = 0; $i < count($this->commanders) - 1; $i++) {
					if ($this->commanders[$i + 1]->line < $this->commanders[$i]->line) {
						$tempCom = $this->commanders[$i];
						$this->commanders[$i] = $this->commanders[$i + 1];
						$this->commanders[$i + 1] = $tempCom;
					}
				}

				$nbrBattle = 0;
				$reportIds = array();
				$reportArray = array();
				while ($nbrBattle < count($this->commanders)) {
					if ($this->commanders[$nbrBattle]->statement == Commander::AFFECTED) {
						LiveReport::$type = Commander::COLO;
						LiveReport::$dFight = $commander->dArrival;

						if ($commanderColor->colorLink[$this->playerColor] == Color::ALLY) {
							LiveReport::$isLegal = Report::ILLEGAL;
						} else {
							LiveReport::$isLegal = Report::LEGAL;
						}

						$this->startFight($commander, $commanderPlayer, $this->commanders[$nbrBattle], $placePlayer, TRUE);

						# mort du commandant
						if ($commander->getStatement() == COM_DEAD) {
							$report = $this->createReport();
							$reportArray[] = $report;
							$reportIds[] = $report->id;
							$nbrBattle++;
							break;
						}
					}
					#création du rapport
					$report = $this->createReport();
					$reportArray[] = $report;
					$reportIds[] = $report->id;
					
					$nbrBattle++;
				}

				# victoire
				if ($commander->getStatement() != COM_DEAD) {
					include_once ATHENA;

					if ($nbrBattle == 0) {
						$this->sendNotif(self::CONQUERPLAYERWHITOUTBATTLESUCCESS, $commander, NULL);
					} else {
						$this->sendNotifForConquest(self::CONQUERPLAYERWHITBATTLESUCCESS, $commander, $reportIds);
					}

					# attribuer le prestige au joueur
					if (in_array($commander->playerColor, array(ColorResource::EMPIRE, ColorResource::CARDAN, ColorResource::NERVE))) {
						$points = 0;
						switch ($commander->playerColor) {
							case ColorResource::EMPIRE:
								$points = Color::POINTCONQUER;
								break;
							case ColorResource::CARDAN:
								if ($this->sectorColor == ColorResource::CARDAN) {
									$points = round($this->population);
								} else {
									$points = round($this->population) + Color::BONUSOUTOFSECTOR;
								}
								break;
							case ColorResource::NERVE:
								$points = ($this->coefResources - 45) * Color::COEFFPOINTCONQUER;
								break;
							default:
								$points = 0;
								break;
						}
						$commanderPlayer->factionPoint += $points;
					}

					if (in_array($this->playerColor, array(ColorResource::EMPIRE, ColorResource::CARDAN, ColorResource::NERVE))) {
						$points = 0;
						switch ($commander->playerColor) {
							case ColorResource::EMPIRE:
								$points = Color::POINTCONQUER;
								break;
							case ColorResource::CARDAN:
								if ($this->sectorColor == ColorResource::CARDAN) {
									$points = round($this->population);
								} else {
									$points = round($this->population) + Color::BONUSOUTOFSECTOR;
								}
								break;
							case ColorResource::NERVE:
								$points = ($this->coefResources - 44) * Color::COEFFPOINTCONQUER;
								break;
							default:
								$points = 0;
								break;
						}
						$placePlayer->factionPoint -= $points;
					}

					#attribuer le joueur à la place
					$this->commanders = array();
					$this->rColor = $commander->playerColor;
					$this->rPlayer = $commander->rPlayer;
					# changer l'appartenance de la base (et de la place)
					ASM::$obm->changeOwnerById($this->id, $placeBase, $commander->getRPlayer());

					$this->commanders[] = $commander;

					$commander->rBase = $this->id;
					$commander->statement = Commander::AFFECTED;
					$commander->line = 1;

				# s'il est mort
				} else {
					for ($i = 0; $i < count($this->commanders); $i++) {
						if ($this->commanders[$i]->statement == COM_DEAD) {
							unset($this->commanders[$i]);
							$this->commanders = array_merge($this->commanders);
						}
					}
					
					# ajouter du prestige au défenseur synelectique
					if ($this->playerColor == ColorResource::SYNELLE) {
						$placePlayer->factionPoint += Color::POINTDEFEND;
					}

					$this->sendNotifForConquest(self::CONQUERPLAYERWHITBATTLEFAIL, $commander, $reportIds);
				}
			} else {
				$length = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());
				
				$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);
				$commander->startPlaceName = $this->baseName;
				$commander->destinationPlaceName = $commander->oBName;
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);

				$this->sendNotif(self::CONQUERLOST, $commander);
			}
		# planète rebelle
		} else {
			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			// $commander->dArrival = NULL;

			# faire un combat
			LiveReport::$type = Commander::COLO;
			LiveReport::$dFight = $commander->dArrival;
			LiveReport::$isLegal = Report::LEGAL;

			$this->startFight($commander, $commanderPlayer);

			if ($commander->getStatement() !== COM_DEAD) {
				
				# attribuer le rPlayer à la Place !
				$this->rPlayer = $commander->rPlayer;
				$this->commanders[] = $commander;

				#attibuer le commander à la place
				$commander->rBase = $this->id;
				$commander->statement = COM_AFFECTED;
				$commander->line = 1;

				# créer une Base
				include_once ATHENA;
				$ob = new OrbitalBase();
				$ob->rPlace = $this->id;
				$ob->setRPlayer($commander->getRPlayer());
				$ob->setName('Base de ' . $commander->getPlayerName());
				$ob->iSchool = 500;
				$ob->iAntiSpy = 500;
				$ob->resourcesStorage = 2000;
				$ob->uOrbitalBase = Utils::now();
				$ob->dCreation = Utils::now();
				$ob->updatePoints();

				$_OBM = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession();
				ASM::$obm->add($ob);
				ASM::$obm->changeSession($_OBM);

				if (in_array($commander->playerColor, array(ColorResource::CARDAN, ColorResource::NERVE))) {
					$points = 0;
					switch ($commander->playerColor) {
						case ColorResource::CARDAN:
							if ($this->sectorColor == ColorResource::CARDAN) {
								$points = round($this->population);
							} else {
								$points = round($this->population) + Color::BONUSOUTOFSECTOR;
							}
							break;
						case ColorResource::NERVE:
							$points = ($this->coefResources - 44) * Color::COEFFPOINTCONQUER;
							break;
						default:
							$points = 0;
							break;
					}
					$commanderPlayer->factionPoint += $points;
				}

				if (CTR::$data->get('playerId') == $commander->getRPlayer()) { 
					CTR::$data->addBase('ob', 
						$ob->getId(), 
						$ob->getName(), 
						$this->rSector, 
						$this->rSystem,
						'1-' . Game::getSizeOfPlanet($this->population),
						OrbitalBase::TYP_NEUTRAL);
				}
				
				#création du rapport
				$report = $this->createReport();
				$percentage = (($report->PevAtEndD + 1) / ($report->setPevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->sendNotif(self::CONQUEREMPTYSSUCCESS, $commander, $report->id);
			# s'il est mort
			} else {
				#création du rapport
				$report = $this->createReport();
				$percentage = (($report->PevAtEndD + 1) / ($report->setPevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->sendNotif(self::CONQUEREMPTYFAIL, $commander);
				# enlever le commandant de la session
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
			}
		}
	}

	# retour à la maison
	public function uComeBackHome($commander, $commanderBase) {
		// $commander->rDestinationPlace = NULL;
		$commander->travelType = NULL;
		$commander->travelLength = NULL;
		// $commander->rStartPlace = NULL;
		$commander->dArrival = NULL;

		$commander->statement = Commander::AFFECTED;

		$this->sendNotif(self::COMEBACK, $commander);

		if ($commander->getResourcesTransported() > 0) {
			$commanderBase->increaseResources($commander->resources);
			$commander->resources = 0;
		}
	}

	private function lootAnEmptyPlace($commander, $playerBonus) {

		$bonus = 0;
		if ($commander->rPlayer != CTR::$data->get('playerId')) {
			$bonus = $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER);
		} else {
			$bonus = CTR::$data->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);
		}

		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));
		$resourcesLooted = 0;

		if ($storage > $this->resources) {
			$ressouresLooted = $this->resources;
		} else {
			$ressouresLooted = $storage;
		}

		$this->resources -= $ressouresLooted;
		$commander->resources = $ressouresLooted;
		LiveReport::$resources = $ressouresLooted;
	}

	private function lootAPlayerPlace($commander, $playerBonus, $placeBase) {

		$bonus = 0;
		if ($commander->rPlayer != CTR::$data->get('playerId')) {
			$bonus = $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER);
		} else {
			$bonus = CTR::$data->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);
		}

		$resourcesToLoot = $placeBase->getResourcesStorage() - Commander::LIMITTOLOOT;

		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));
		$resourcesLooted = 0;

		$resourcesLooted = ($storage > $resourcesToLoot) ? $resourcesToLoot : $storage;

		if ($resourcesLooted > 0) {
			$placeBase->decreaseResources($resourcesLooted);
			$commander->resources = $resourcesLooted;
			LiveReport::$resources = $resourcesLooted;
		}
	}

	private function startFight($commander, $player, $enemyCommander = NULL, $enemyPlayer = NULL, $pvp = FALSE) {
		if ($pvp == TRUE) {
			$commander->setArmy();
			$enemyCommander->setArmy();
			$fc = new FightController();
			$fc->startFight($commander, $player, $enemyCommander, $enemyPlayer);
		} else {
			$commander->setArmy();
			$computerCommander = $this->createVirtualCommander();
			$fc = new FightController();
			$fc->startFight($commander, $player, $computerCommander);
		}
	}

	private function createReport() {
		$report = new Report();

		$report->rPlayerAttacker = LiveReport::$rPlayerAttacker;
		$report->rPlayerDefender =  LiveReport::$rPlayerDefender;
		$report->rPlayerWinner = LiveReport::$rPlayerWinner;
		$report->avatarA = LiveReport::$avatarA;
		$report->avatarD = LiveReport::$avatarD;
		$report->nameA = LiveReport::$nameA;
		$report->nameD = LiveReport::$nameD;
		$report->levelA = LiveReport::$levelA;
		$report->levelD = LiveReport::$levelD;
		$report->experienceA = LiveReport::$experienceA;
		$report->experienceD = LiveReport::$experienceD;
		$report->palmaresA = LiveReport::$palmaresA;
		$report->palmaresD = LiveReport::$palmaresD;
		$report->resources = LiveReport::$resources;
		$report->expCom = LiveReport::$expCom;
		$report->expPlayerA = LiveReport::$expPlayerA;
		$report->expPlayerD = LiveReport::$expPlayerD;
		$report->rPlace = $this->id;
		$report->type = LiveReport::$type;
		$report->round = LiveReport::$round;
		$report->importance = LiveReport::$importance;
		$report->squadrons = LiveReport::$squadrons;
		$report->dFight = LiveReport::$dFight;
		$report->placeName = ($this->baseName == '') ? 'planète rebelle' : $this->baseName;
		$report->setArmies();
		$report->setPev();
		$id = ASM::$rpm->add($report);
		LiveReport::clear();

		return $report;
	}

	private function sendNotif($case, $commander, $report = NULL) {
		include_once HERMES;

		switch ($case) {
			case self::CHANGESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' est arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;

			case self::CHANGEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' s\'est posé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('. Il est en garnison car il n\'y avait pas assez de place en orbite.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CHANGELOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' n\'est pas arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('. Cette base ne vous appartient pas. Elle a pu être conquise entre temps.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors du pillage de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a attaqué votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('. Aucune flotte n\'était en position pour la défendre. ')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car son joueur est de votre faction ou sous la protection débutant.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a colonisé la planète rebelle située aux coordonnées ')  
					->addLnk('map/place-' . $this->id , Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector) . '.')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Votre empire s\'étend, administrez votre ')
					->addLnk('bases/base-' . $this->id, 'nouvelle planète')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Elle est désormais votre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car le joueur est dans votre faction ou sous la protection débutant.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::COMEBACK:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de retour');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' est de retour sur votre base ')
					->addLnk('map/place-' . $commander->getRBase(), $commander->getBaseName())
					->addTxt(' et rapporte ')
					->addStg(Format::number($commander->getResourcesTransported()))
					->addTxt(' ressources à vos entrepôts.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			
			default: break;
		}
	}

	private function sendNotifForConquest($case, $commander, $reports = array()) {
		$nbrBattle = count($reports);
		switch($case) {
			case self::CONQUERPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addTxt('Elle est désormais vôtre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial/', $commander->getName())
					->addTxt(' est tombé lors de la tentive de conquête de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Il a désormais rejoint de Mémorial. Que son âme traverse l\'Univers dans la paix.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a tenté de conquérir votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès. Bravo !');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);
				break;

			default: break;
		}
	}

	public function createVirtualCommander() {
		$population = $this->population;
		$vCommander = new Commander();
		$vCommander->id = 'Null';
		$vCommander->rPlayer = ID_GAIA;
		$vCommander->name = 'rebelle';
		$vCommander->avatar = 't3-c4';
		$vCommander->sexe = 1;
		$vCommander->age = 42;
		$vCommander->statement = 1;
		$vCommander->level = round($this->population / (self::POPMAX / self::LEVELMAXVCOMMANDER));

		if ($vCommander->level == 0) {
			$vCommander->level = 1;
		}

		$nbrsquadron = round($vCommander->level * ($this->resources / (($this->population + 1) * self::COEFFMAXRESOURCE)));
		if ($nbrsquadron == 0) {
			$nbrsquadron = 1;
		}

		$army = array();
		$squadronsIds = array();

		for ($i = 0; $i < $nbrsquadron; $i++) {
			$aleaNbr = ($this->coefHistory * $this->coefResources * $this->position * $i) % SquadronResource::size();
			$army[] = SquadronResource::get($vCommander->level, $aleaNbr);
			$squadronsIds[] = 0;
		}

		for ($i = $vCommander->level - 1; $i >= $nbrsquadron; $i--) {
			$army[$i] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, Utils::now());
			$squadronsIds[] = 0;
		}

		$vCommander->setSquadronsIds($squadronsIds);
		$vCommander->setArmyInBegin($army);
		$vCommander->setArmy();
		$vCommander->setPevInBegin();
		return $vCommander;
	}
}
?>