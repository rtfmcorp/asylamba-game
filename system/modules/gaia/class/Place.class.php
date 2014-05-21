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

	const CONQUEREMPTYSSUCCESS 				= 30;
	const CONQUEREMPTYFAIL						= 31;
	const CONQUERPLAYERWHITBATTLESUCCESS		= 32;
	const CONQUERPLAYERWHITBATTLEFAIL			= 33;
	const CONQUERPLAYERWHITOUTBATTLESUCCESS	= 34;
	const CONQUERLOST							= 37;

	const COMEBACK 								= 40;



	// PLACE
	public $id = 0;
	public $rPlayer = 0;
	public $rSystem = 0;
	public $typeOfPlace = 0;
	public $position = 0;
	public $population = 0;
	public $coefResources = 0;
	public $coefHistory = 0;
	public $resources = 0; // de la place si $typeOfBase = 0, sinon de la base
	public $uPlace = '';

	// SYSTEM
	public $rSector = 0;
	public $xSystem = 0;
	public $ySystem = 0;
	public $typeOfSystem = 0;

	// SECTOR
	public $tax = 0;

	// PLAYER
	public $playerColor = 0;
	public $playerName = '';
	public $playerAvatar = '';
	public $playerStatus = 0;

	// BASE
	public $typeOfBase = 0; // 0=empty, 1=ms1, 2=ms2, 3=ms3, 4=ob
	public $typeOfOrbitalBase;
	public $baseName = '';
	public $points = '';

	// OB
	public $levelCommercialPlateforme = 0;
	public $levelGravitationalModule = 0;
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
	public function getPlayerColor() 					{ return $this->playerColor; }
	public function getPlayerName() 					{ return $this->playerName; }
	public function getPlayerAvatar() 					{ return $this->playerAvatar; }
	public function getPlayerStatus() 					{ return $this->playerStatus; }
	public function getTypeOfBase() 					{ return $this->typeOfBase; }
	public function getBaseName() 						{ return $this->baseName; }
	public function getPoints() 						{ return $this->points; }
	public function getLevelCommercialPlateforme() 		{ return $this->levelCommercialPlateforme; }
	public function getLevelGravitationalModule() 		{ return $this->levelGravitationalModule; }
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
	public function setPlayerColor($v) 					{ $this->playerColor = $v; }
	public function setPlayerName($v) 					{ $this->playerName = $v; }
	public function setPlayerAvatar($v) 				{ $this->playerAvatar = $v; }
	public function setPlayerStatus($v) 				{ $this->playerStatus = $v; }
	public function setTypeOfBase($v) 					{ $this->typeOfBase = $v; }
	public function setBaseName($v) 					{ $this->baseName = $v; }
	public function setPoints($v) 						{ $this->points = $v; }
	public function setLevelCommercialPlateforme($v) 	{ $this->levelCommercialPlateforme = $v; }
	public function setLevelGravitationalModule($v) 	{ $this->levelGravitationalModule = $v; }
	public function setAntiSpyInvest($v)				{ $this->antiSpyInvest = $v; }

	public function uMethod() {
		$token = CTC::createContext();
		$now   = Utils::now();

		if (Utils::interval($this->uPlace, $now, 's') > 0) {
			# update time
			$days = Utils::intervalDates($now, $this->uPlace, 'd');
			$this->uPlace = $now;

			# RESOURCE
			if ($this->typeOfBase == 0) {
				foreach ($days as $key => $day) {
					CTC::add($day, $this, 'uResources', array());
				}
			}

			# TRAVEL
			include_once ATHENA;
			$S_OBM_GEN = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();

			$S_COM_PLACE1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();

			ASM::$com->load(array('c.rDestinationPlace' => $this->id, 'c.statement' => 2), array('c.dArrival', 'ASC'));
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				$commander = ASM::$com->get($i);
				if ($commander->dArrival <= $now AND $commander->rDestinationPlace != NULL) {
					CTC::add($commander->dArrival, $this, 'uTravel', array($commander));
				}
			}
			ASM::$obm->changeSession($S_OBM_GEN);
			ASM::$com->changeSession($S_COM_PLACE1);
			
		}
		CTC::applyContext($token);
	}

	public function uResources() {
		$maxResources = $this->population * self::COEFFMAXRESOURCE;
		$this->resources += floor(PLM_RESSOURCECOEFF * $this->population * 24);
		if ($this->resources > $maxResources) {
			$this->resources = $maxResources;
		}
	}

	public function uTravel($commander) {
		include_once ARES;

		switch ($commander->travelType) {
				case Commander::MOVE: $this->tryToChangeBase($commander); break;
				case Commander::LOOT: $this->tryToLoot($commander); break;
				case Commander::COLO: $this->tryToConquer($commander); break;
				case Commander::BACK: $this->comeBackToHome($commander); break;
				default: 
					CTR::$alert->add('Cette action n\'existe pas.', ALT_BUG_INFO);
		}

		$commander->hasToU = TRUE;
		return $commander;
	}

	# se poser
	private function tryToChangeBase($commander) {
		# si la place et le commander ont le même joueur
		if ($this->rPlayer == $commander->getRPlayer() and $this->typeOfBase == 4) {
			$maxCom = ($this->typeOfOrbitalBase == 0) ? 2 : 5;
			# si place a assez de case libre :
			if (count($this->commanders) < $maxCom) {
				$comLine1 = 0;
				$comLine2 = 0;

				foreach ($this->commanders as $com) {
					if ($com->line == 1) {
						$comLine1++;
					} else {
						$comLine2++;
					}
				}

				if ($comLine2 <= $comLine1) {
					$commander->line = 2;
				} else {
					$commander->line = 1;
				}
				# changer rBase commander
				$commander->rBase = $this->id;
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				// $commander->rStartPlace = NULL;
				$commander->dArrival = NULL;
				$commander->length = NULL;
				$commander->statement = Commander::AFFECTED;

				# ajouter à $this le commandant
				$this->commanders[] = $commander;

				# instance de la place d'envoie + suppr commandant de ses flottes
				# enlever à rBase le commandant
				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				for ($i = 0; $i < count(ASM::$plm->get()->commanders); $i++) {
					if (ASM::$plm->get()->commanders[$i]->id == $commander->id) {
						unset(ASM::$plm->get()->commanders[$i]);
						ASM::$plm->get()->commanders = array_merge(ASM::$plm->get()->commanders);
					}
				}
				ASM::$plm->changeSession($S_PLM10);

				# envoie de notif
				$this->sendNotif(self::CHANGESUCCESS, $commander);

			} else {
				# NON : comeBackToHome
				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->get();
				$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
				$duration = Game::getTimeToTravel($home, $this);
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $durationn);
				ASM::$plm->changeSession($S_PLM10);

				$this->sendNotif(self::CHANGEFAIL, $commander);
			}
		} else {
			$S_PLM10 = ASM::$plm->getCurrentSession();
			ASM::$plm->newSession();
			ASM::$plm->load(array('id' => $commander->getRBase()));
			$home = ASM::$plm->get();
			$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
			$duration = Game::getTimeToTravel($home, $this);
			$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
			ASM::$plm->changeSession($S_PLM10);

			$this->sendNotif(self::CHANGELOST, $commander);
		}
	}

	# piller
	private function tryToLoot($commander) {
		include_once ARES;
		if ($this->rPlayer == 0) {
			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			$commander->dArrival = NULL;
			$commander->dstart = NULL;
			$commander->length = NULL;

			# planète vide -> faire un combat
			$this->startFight($commander);

			# si gagné
			if ($commander->getStatement() != Commander::DEAD) {
				# piller la planète
				$this->lootAnEmptyPlace($commander);
				# comeBackToHome
				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->get();
				$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
				$duration = Game::getTimeToTravel($home, $this);
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
				ASM::$plm->changeSession($S_PLM10);

				$this->sendNotif(self::LOOTEMPTYSSUCCESS, $commander);

			} else {

				# si il est mort
				# enlever le commandant de la session
				$S_PLM11= ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				for ($i = 0; $i < count(ASM::$plm->get()->commanders); $i++) {
					if (ASM::$plm->get()->commanders[$i]->getId() == $commander->getId()) {
						unset(ASM::$plm->get()->commanders[$i]);
						ASM::$plm->get()->commanders = array_merge(ASM::$plm->get()->commanders);
					}
				}
				ASM::$plm->changeSession($S_PLM11);

				$this->sendNotif(self::LOOTEMPTYFAIL, $commander);
			}
		# si il y a une base
		} else {
			# planète à joueur: si $this->rColor != commandant->rColor
			if ($this->playerColor != $commander->getPlayerColor()) {
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				$commander->travelLength = NULL;
				// $commander->rStartPlace = NULL;
				$commander->dArrival = NULL;
				$commander->dstart = NULL;
				$commander->length = NULL;

				$dCommanders = array();
				foreach ($this->commanders AS $dCommander) {
					if ($dCommander->statement == Commander::AFFECTED && $dCommander->line == 1) {
						$dCommanders[] = $dCommander;
					}
				}

				if (count($dCommanders) != 0) {
				# il y a des commandants en défense : faire un combat avec un des commandants
					$aleaNbr = rand(0, count($dCommanders) - 1);
					$this->startFight($commander, $dCommanders[$aleaNbr], TRUE);

					# si il gagne
					if ($commander->getStatement() != COM_DEAD) {
						// piller la planète
						$this->lootAPlayerPlace($commander);
						// comeBackToHome
						$S_PLM10 = ASM::$plm->getCurrentSession();
						ASM::$plm->newSession();
						ASM::$plm->load(array('id' => $commander->getRBase()));
						$home = ASM::$plm->get();
						$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
						$duration = Game::getTimeToTravel($home, $this);
						$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
						ASM::$plm->changeSession($S_PLM10);

						unset($this->commanders[$aleaNbr]);
						$this->commanders = array_merge($this->commanders);

						$this->sendNotif(self::LOOTPLAYERWHITBATTLESUCCESS, $commander);


					} else {
					# s'il est mort
						#  enlever le commandant de la session
						$S_PLM10 = ASM::$plm->getCurrentSession();
						ASM::$plm->newSession();
						ASM::$plm->load(array('id' => $commander->getRBase()));
						for ($i = 0; $i < count(ASM::$plm->get()->commanders); $i++) {
							if (ASM::$plm->get()->commanders[$i]->getId() == $commander->getId()) {
								unset(ASM::$plm->get()->commanders[$i]);
								ASM::$plm->get()->commanders = array_merge(ASM::$plm->get()->commanders);
							}
						}
						ASM::$plm->changeSession($S_PLM10);

						$this->sendNotif(self::LOOTPLAYERWHITBATTLEFAIL, $commander);
					}
				} else {
					$this->lootAPlayerPlace($commander);

					$S_PLM10 = ASM::$plm->getCurrentSession();
					ASM::$plm->newSession();
					ASM::$plm->load(array('id' => $commander->getRBase()));
					$home = ASM::$plm->get();
					$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
					$duration = Game::getTimeToTravel($home, $this);
					$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
					ASM::$plm->changeSession($S_PLM10);

					$this->sendNotif(self::LOOTPLAYERWHITOUTBATTLESUCCESS, $commander);
				}
			# si c'est a même couleur
			} else {
				// $commander->rDestinationPlace = NULL;
				$commander->travelType = NULL;
				$commander->travelLength = NULL;
				// $commander->rStartPlace = NULL;
				$commander->dArrival = NULL;
				$commander->dstart = NULL;
				$commander->length = NULL;

				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->get();
				$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
				$duration = Game::getTimeToTravel($home, $this);
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
				ASM::$plm->changeSession($S_PLM10);

				$this->sendNotif(self::LOOTLOST, $commander);
			}
		}
	}

	# conquest
	private function tryToConquer($commander) {
		if ($this->rPlayer != 0) {
			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			$commander->dArrival = NULL;
			$commander->dstart = NULL;
			$commander->length = NULL;

			if ($this->playerColor != $commander->getPlayerColor()) {
				# combat contre les 3 commandants,
				$nbrBattle = 0;
				while ($nbrBattle < count($this->commanders)) {
					if ($this->commanders[$i]->statement == Commander::AFFECTED) {

						$this->startFight($commander, $this->commanders[$i], TRUE);

						# mort du commandant
						if ($commander->getStatement() == COM_DEAD) {
							break;
						}
					}
					$nbrBattle++;
				}
				# victoire
				if ($commander->getStatement() != COM_DEAD) {
					include_once ATHENA;

					if ($nbrBattle == 0) {
						$this->sendNotif(self::CONQUERPLAYERWHITOUTBATTLESUCCESS, $commander);
					} else {
						$this->sendNotif(self::CONQUERPLAYERWHITBATTLESUCCESS, $commander);
					}

					#attribuer le jooeur à la place
					$this->commanders = array();
					$this->rColor = $commander->playerColor;
					$this->rPlayer = $commander->rPlayer;
					# changer l'appartenance de la base (et de la place)
					ASM::$obm->changeOwnerById($this->id, $commander->getRPlayer());

					$this->commanders[] = $commander;

					$commander->rBase = $this->id;
					$commander->statement = Commander::AFFECTED;
					$commander->line = 1;

				# s'il est mort
				} else {
					for ($i = 0; $i < count($this->commanders); $i++) {
						if ($this->commanders[$i]->getStatement == COM_DEAD) {
							unset($this->commanders[$i]);
							$this->commanders = array_merge($this->commanders);
						}
					}

					$this->sendNotif(self::CONQUERPLAYERWHITBATTLEFAIL, $commander);
				}
			} else {
				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				$home = ASM::$plm->get();
				$length = Game::getDistance($this->getXSystem(), $home->getXSystem(), $this->getYSystem(), $home->getYSystem());
				$duration = Game::getTimeToTravel($home, $this);
				$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
				ASM::$plm->changeSession($S_PLM10);

				$this->sendNotif(self::CONQUERLOST, $commander);
			}
		# planète rebelle
		} else {

			// $commander->rDestinationPlace = NULL;
			$commander->travelType = NULL;
			$commander->travelLength = NULL;
			// $commander->rStartPlace = NULL;
			$commander->dArrival = NULL;
			$commander->dstart = NULL;
			$commander->length = NULL;

			# faire un combat
			$this->startFight($commander);

			if ($commander->getStatement() !== COM_DEAD) {
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

				ASM::$obm->add($ob);

				#attibuer le commander à la place
				$commander->setRBase($this->id);
				$commander->setStatement(COM_AFFECTED);
				$commander->line = 1;

				# attribuer le rPlayer à la Place !
				$this->rPlayer = $commander->getRPlayer();
				$this->commanders[] = $commander;

				if (CTR::$data->get('playerId') == $commander->getRPlayer()) { 
					CTRHelper::addBase('ob', 
						$ob->getId(), 
						$ob->getName(), 
						$this->rSector, 
						$this->rSystem,
						'1-' . Game::getSizeOfPlanet($this->population),
						OrbitalBase::TYP_NEUTRAL);
				}

				$this->sendNotif(self::CONQUEREMPTYSSUCCESS, $commander);

				GalaxyColorManager::apply();
			# s'il est mort
			} else {
				$this->sendNotif(self::CONQUEREMPTYFAIL, $commander);
				# enlever le commandant de la session
				$S_PLM10 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => $commander->getRBase()));
				for ($i = 0; $i < count(ASM::$plm->get()->commanders); $i++) {
					if (ASM::$plm->get()->commanders[$i]->getId() == $commander->getId()) {
						unset(ASM::$plm->get()->commanders[$i]);
						ASM::$plm->get()->commanders = array_merge(ASM::$plm->get()->commanders);
					}
				}
				ASM::$plm->changeSession($S_PLM10);
			}
		}

	}

	# retour à la maison
	private function comeBackToHome($commander) {
		include_once ATHENA;
		// $commander->rDestinationPlace = NULL;
		$commander->travelType = NULL;
		$commander->travelLength = NULL;
		// $commander->rStartPlace = NULL;
		$commander->dArrival = NULL;
		$commander->dstart = NULL;
		$commander->length = NULL;

		$commander->statement = Commander::AFFECTED;

		$this->sendNotif(self::COMEBACK, $commander);

		if ($commander->getResourcesTransported() > 0) {
			$S_OBM10 = ASM::$obm->getCurrentSession();

			ASM::$obm->newSession(FALSE);
			ASM::$obm->load(array('rPlace' => $commander->getRBase()));
			ASM::$obm->get()->increaseResources($commander->resources);
			$commander->resources = 0;

			ASM::$obm->changeSession($S_OBM10);
		}
	}

	private function lootAnEmptyPlace($commander) {
		$storage = $commander->getPev() * Commander::COEFFLOOT;
		$resourcesLooted = 0;

		if ($storage > $this->resources) {
			$ressouresLooted = $this->resources;
		} else {
			$ressouresLooted = $storage;
		}

		$this->resources -= $ressouresLooted;
		$commander->resources = $ressouresLooted;
	}

	private function lootAPlayerPlace($commander) {
		include_once ATHENA;
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $this->id));
		$base = ASM::$obm->get();

		$resourcesToLoot = $base->getResourcesStorage() - Commander::LIMITTOLOOT;

		$storage = $commander->getPev() * Commander::COEFFLOOT;
		$resourcesLooted = 0;

		$resourcesLooted = ($storage > $resourcesToLoot) ? $resourcesToLoot : $storage;

		if ($resourcesLooted > 0) {
			$base->decreaseResources($resourcesLooted);
			$commander->resources = $resourcesLooted;
		}
		ASM::$obm->changeSession($S_OBM1);
	}

	private function startFight($commander, $enemyCommander = NULL, $pvp = FALSE) {
		if ($pvp == TRUE) {
			$fc = new FightController();
			$fc->startFight($commander, $enemyCommander, $this);
		} else {
			$computerCommander = $this->createVirtualCommander();
			$fc = new FightController();
			$fc->startFight($commander, $computerCommander, $this);
		}
	}

	private function sendNotif($case, $commander) {
		include_once HERMES;

		switch ($case) {
			case self::CHANGESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de déplacement');
				$notif->addBeg()
					->addTxt('Votre offier ')
					->addStg($commander->getName())
					->addTxt(' est arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;

			case self::CHANGEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de déplacement');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addStg($commander->getName())
					->addTxt(' n\'a pas pu se poser sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt(' car il y a déjà trop d\'officier autour de la planète .')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CHANGELOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de déplacement');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addStg($commander->getName())
					->addTxt(' n\'est pas arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('. Cette base ne vous appartient pas.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('La flotte victorieuse a volé ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources à l\'ennemi.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Victoire lors d\'un pillage');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Votre pillage vous rapporte ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('Le commandant ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Il repart avec ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Défaite lors d\'un pillage');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors du pillage de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('Le commandant ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a attaqué votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Mise à sac');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Votre pillage vous rapporte ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('Le commandant ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('. Aucun commandant n\'était en position pour la défendre. ')
					->addSep()
					->addTxt('Il repart avec ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addStg($commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car elle est dans votre Faction.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Planète colonisée');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a colonisé la planète rebelle située aux coordonnées ')  
					->addLnk('map/place-' . $this->id , Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('. Votre empire s\'étend, administrez votre nouvelle planète ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Défaite lors d\'une colonisationge');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Elle est désormais votre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de conquête');
				$notif->addBeg()
					->addTxt('Le commandant ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Echec de conquête');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-memorial/', $commander->getName())
					->addTxt(' est tombé lors de la tentive de conquête de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint de Mémorial.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('Le commandant ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a tenté de conquérir votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('diary/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Elle est désormais votre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de conquête');
				$notif->addBeg()
					->addTxt('Le commandant ')
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
				$notif->addBeg()
					->setTitle('Erreur de coordonnées')
					->addTxt('Votre commandant ')
					->addStg($commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car elle est de votre faction.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::COMEBACK:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de retour');
				$notif->addBeg()
					->addTxt('Votre commandant ')
					->addLnk('fleet/view-movement/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' est de retour sur votre base ')
					->addLnk('map/place-' . $commander->getRBase(), $commander->getBaseName())
					->addTxt(' et rapporte ')
					->addStg(Format::numberFormat($commander->getResourcesTransported()))
					->addTxt(' ressources à vos entrepôts.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			
			default:
				//do nothing
				break;
		}
	}

	public function createVirtualCommander() {
		$this->uResources();
		$population = $this->population;
		$vCommander = new Commander();
		$vCommander->id = 0;
		$vCommander->rPlayer = 0;
		$vCommander->sexe = 1;
		$vCommander->age = 42;
		$vCommander->statement = 1;
		$vCommander->level = round($this->population / (self::POPMAX / self::LEVELMAXVCOMMANDER));

		$nbrsquadron = $vCommander->level;//round($vCommander->level * ($this->resources / ($this->population * self::COEFFMAXRESOURCE)));
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