<?php

/**
 * Commander
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Ares
 * @update 13.02.14_
*/

class Commander {
	const COEFFEARNEDEXP 			= 50;
	const COEFFEXPPLAYER			= 100;
	const CMDBASELVL 				= 100;
	
	const FLEETSPEED 				= 35;

	const COEFFMOVEINSYSTEM 		= 584;
	const COEFFMOVEOUTOFSYSTEM 		= 600;
	const COEFFMOVEINTERSYSTEM 		= 50000;

	const LVLINCOMECOMMANDER 		= 100;

	const CREDITCOEFFTOCOLONIZE		= 100000;
	const CREDITCOEFFTOCONQUER		= 250000;

	# loot const
	const LIMITTOLOOT 				= 5000;
	const COEFFLOOT 				= 250;

	# Commander statements
	const INSCHOOL 					= 0; # dans l'école
	const AFFECTED 					= 1; # autour de la base
	const MOVING 					= 2; # en déplacement
	const DEAD 						= 3; # mort
	const DESERT 					= 4; # déserté
	const RETIRED 					= 5; # à la retraite
	const ONSALE 					= 6; # dans le marché

	# types of travel
	const MOVE						= 0; # déplacement
	const LOOT						= 1; # pillage
	const COLO						= 2; # colo ou conquete
	const BACK						= 3; # retour après une action

#	const MAXTRAVELTIME				= 57600;
	const MAXTRAVELTIME				= 120000;

	# attributes
	public $id 						= 0;
	public $name 					= '';
	public $experience 				= 0;
	public $avatar 					= '';
	public $rPlayer 				= 0;
	public $rBase 					= 0;
	public $comment 				= '';
	public $sexe 					= 0;
	public $age 					= 0;
	public $level 					= 0;
	public $uExperience 			= 0;
	public $palmares 				= 0;
	public $statement 				= COM_INSCHOOL;
	public $line 					= 1;
	public $dCreation 				= '';
	public $dAffectation 			= '';
	public $dDeath 					= '';

	# variables de jointure quelconque
	public $oBName					= '';
	public $playerName				= '';
	public $playerColor				= '';

	# variables de combat
	public $squadronsIds			= array();
	public $armyInBegin 			= array();
	public $armyAtEnd 				= array();
	public $pevInBegin 				= 0;
	public $earnedExperience 		= 0;
	public $winner					= FALSE;
	public $isAttacker 				= NULL;

	# variables de déplacement
	public $dStart					= '';
	public $dArrival				= '';
	public $resources 				= 0;
	public $travelType				= 0;
	public $travelLength			= 0;
	public $rStartPlace				= 0;
	public $rDestinationPlace		= 0;
	public $startPlaceName			= '';
	public $destinationPlaceName	= '';


	public $uCommander				= '';
	public $hasToU					= TRUE;
	public $hasArmySetted			= FALSE;


	# Tableau d'objets squadron       
	public $army = array();

	# Const de lineCoord
	private static $LINECOORD = array(1, 1, 1, 2, 2, 1, 2, 3, 3, 1, 2, 3, 4, 4, 2, 3, 4, 5, 5, 3, 4, 5, 6, 6, 4, 5, 6, 7, 7, 5, 6, 7);

	# GETTER
	public function getId() 					{ return $this->id; }
	public function getName() 					{ return $this->name; }
	public function getAvatar() 				{ return $this->avatar; }
	public function getRPlayer() 				{ return $this->rPlayer; }
	public function getPlayerName() 			{ return $this->playerName; }
	public function getPlayerColor() 			{ return $this->playerColor; }
	public function getRBase() 					{ return $this->rBase; }
	public function getBaseName() 				{ return $this->oBName; }
	public function getComment() 				{ return $this->comment; }
	public function getSexe() 					{ return $this->sexe; }
	public function getAge() 					{ return $this->age; }
	public function getLevel() 					{ return $this->level; }
	public function getExperience() 			{ return $this->experience; }
	public function getUMethod() 				{ return $this->uMethod; }
	public function getPalmares() 				{ return $this->palmares; }
	public function getTypeOfMove() 			{ return $this->travelType; }
	public function getTravelType() 			{ return $this->travelType; }
	public function getRPlaceDestination() 		{ return $this->rDestinationPlace; }
	public function getArrivalDate() 			{ return $this->dArrival; }
	public function getDArrival()	 			{ return $this->dArrival; }
	public function getResourcesTransported() 	{ return $this->resources; }
	public function getResources()			 	{ return $this->resources; }
	public function getUTravel() 				{ return $this->uTravel; }
	public function getStatement() 				{ return $this->statement; }
	public function getDCreation() 				{ return $this->dCreation; }
	public function getDAffectation() 			{ return $this->dAffectation; }
	public function getDDeath() 				{ return $this->dDeath; }
	public function getLengthTravel()			{ return $this->lengthTravel; }
	public function getOBName()					{ return $this->oBName; }
	public function getArmyInBegin()			{ return $this->armyInBegin; }
	public function getArmyAtEnd()				{ return $this->armyAtEnd; }
	public function getEarnedExperience()		{ return $this->earnedExperience; }
	public function getPevInBegin()				{ return $this->pevInBegin; }
	public function getIsAttacker()				{ return $this->isAttacker; }
	public function getDestinationPlaceName()	{ return $this->destinationPlaceName; }
	public function getSquadronsIds()			{ return $this->squadronsIds; }
	public function getArmy()					{ $this->setArmy(); return $this->army; }

	public function getFormatLineCoord() {
		$return = array();

		for ($i = 0; $i < ($this->level + 1); $i++) { 
			$return[] = self::$LINECOORD[$i];
		}

		return $return;
	}
	public function getSizeArmy() { return count($this->squadronsIds); }


	public function getPev() {
		$pev = 0;
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$pev += $squadron[$i] * ShipResource::getInfo($i, 'pev');
			}
		}
		return $pev;
	}
	
	public function getSquadron($i)	{
		$this->setArmy();
		if (!empty($this->army[$i])) {
			return $this->army[$i]; 
		} else {
			return FALSE;
		}
	}

	# renvoie un tableau de nombre de vaisseaux
	public function getNbrShipByType() {
		$array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$array[$i] += $squadron[$i];
			}
		}

		return $array;
	}
	
	//-----------------SETTER---------------
	public function setId($id) 										{ $this->id = $id; } 					
	public function setName($name) 									{ $this->name = $name; } 				
	public function setAvatar($avatar) 								{ $this->avatar = $avatar; } 			          
	public function setRPlayer($rPlayer) 							{ $this->rPlayer = $rPlayer; }
	public function setPlayerName($playerName) 						{ $this->playerName = $playerName; }
	public function setPlayerColor($playerColor) 					{ $this->playerColor = $playerColor; }
	public function setRBase($rBase) 								{ $this->rBase = $rBase; } 				      	            
	public function setComment($comment) 							{ $this->comment = $comment; } 			  
	public function setSexe($sexe) 									{ $this->sexe = $sexe; } 				      
	public function setAge($age) 									{ $this->age = $age; } 					  
	public function setLevel($level) 								{ $this->level = $level; } 				      
	public function setExperience($experience) 						{ $this->experience = $experience; } 	      
	public function setUCommander($uCommander) 						{ $this->uMethod = $uCommander; } 	      
	public function setPalmares($palmares) 							{ $this->palmares = $palmares; } 		      
	public function setTypeOfMove($typeOfMove) 						{ $this->typeOfMove = $typeOfMove; } 	      
	public function setrPlaceDestination($rPlaceDestination) 		{ $this->rPlaceDestination = $rPlaceDestination; } 	
	public function setArrivalDate($arrivalDate) 					{ $this->arrivalDate = $arrivalDate; } 	        
	public function setResourcesTransported($resourcesTransported)	{ $this->resourcesTransported = $resourcesTransported; }
	public function setUTravel($uTravel) 							{ $this->uTravel = $uTravel; }
	public function setStatement($statement) 						{ $this->statement = $statement; } 	       
	public function setDCreation($dCreation) 						{ $this->dCreation = $dCreation; } 	       
	public function setDAffectation($dAffectation) 					{ $this->dAffectation = $dAffectation; }	    
	public function setDDeath($dDeath) 								{ $this->dDeath = $dDeath; }
	public function setLengthTravel($lengthTravel)					{ $this->lengthTravel = $lengthTravel; }
	public function setOBName($oBName)								{ $this->oBName = $oBName; }
	public function setDestinationPlaceName($doName)				{ $this->destinationPlaceName = $doName; }
	public function setSquadronsIds($squadronsIds)					{ $this->squadronsIds = $squadronsIds; }
	public function setArmyInBegin($armyInBegin)					{ $this->armyInBegin = $armyInBegin; }
	public function setIsAttacker($isAttacker)						{ $this->isAttacker = $isAttacker; }		  

	public function setArmy() {
		if (!$this->hasArmySetted) {
			for($i = 0; $i < count($this->squadronsIds) AND $i < 25; $i++) {
				$this->army[$i] = new Squadron(
					$this->armyInBegin[$i], 
					$this->squadronsIds[$i], 
					self::$LINECOORD[$i], 
					$i, 
					$this->id);
			}
			$this->hasArmySetted = TRUE;
		}
	}

#YOLO
	public function setPevInBegin() {
		$pev = 0;
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$pev += $squadron[$i] * ShipResource::getInfo($i, 'pev');
			}
		}
		$this->pevInBegin = $pev;
	}

#mettre le setArmy
	private function setArmyAtEnd() {
		$this->setArmy();
		$i = 0;
		foreach ($this->army AS $squadron) {
			$this->armyAtEnd[$i] = $squadron->getArrayOfShips();
			$i++;
		}
	}

	private function setEarnedExperience($enemyCommander) {
		$this->setArmy();
		include_once ZEUS;
		$finalOwnPev = 0;

		foreach ($this->army AS $squadron) {
			foreach ($squadron->getSquadron() AS $ship) {
				$finalOwnPev += $ship->getPev();
			}
		}
		$importance = (($finalOwnPev + 1) * ($enemyCommander->getPevInBegin())) / 
			((($this->pevInBegin + 1) * ($enemyCommander->getLevel() / 
				($this->level + 1))));

		$this->earnedExperience = $importance * COM_COEFFEARNEDEXP;
		if($this->winner) {
			LiveReport::$importance = $importance;
		}
		
		if ($this->rPlayer > 0) {
			$S_PLM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(TRUE, FALSE);
			ASM::$pam->load(array('id' => $this->rPlayer));
			$exp = round($this->earnedExperience / COEFFEXPPLAYER);
			ASM::$pam->get(0)->increaseExperience($exp);

			if ($enemyCommander->isAttacker == TRUE) {
				LiveReport::$expPlayerD = $exp;
			} else {
				LiveReport::$expPlayerA = $exp;
			}
			
			ASM::$pam->changeSession($S_PLM1);
		}
	}

	public function setBonus() {
		$this->setArmy();
		if ($this->rPlayer != CTR::$data->get('playerId')) {
			$playerBonus = new PlayerBonus($this->rPlayer);
			$playerBonus->load();
			
			foreach ($this->army AS $squadron) {
				foreach ($squadron->squadron AS $ship) {
					$ship->setBonus($playerBonus->bonus);
				}
			}
		} else {
			foreach ($this->army AS $squadron) {
				foreach ($squadron->squadron AS $ship) {
					$ship->setBonus(CTR::$data->get('playerBonus'));
				}
			}
		}
	}

	public function upExperience($earnedExperience) {
		$this->experience += $earnedExperience;

		while (1) {
			if ($this->experience >= $this->experienceToLevelUp()) {
				$this->level++;

				/*if ($this->statement != COM_INSCHOOL) {
					CTR::$alert->add('Votre commandant ' . $this->name . ' gagne un niveau', ALERT_GAM_ATTACK);				
				}*/
			} else { break; }
		}
	}

	public static function nbLevelUp($level, $newExperience) {
		$oLevel = $level;
		$nLevel = $level;
		while (1) {
			if ($newExperience >= (pow(2, $nLevel) * COM_CMDBASELVL)) {
				$nLevel++;
			} else {
				break;
			}
		}
		return $nLevel - $oLevel;
	}

	public function experienceToLevelUp() {
		return pow(2, $this->level) * COM_CMDBASELVL;
	}

	public function emptySquadrons() {
		include_once ATHENA;

		$S_OBM = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $this->rBase));

		if (ASM::$obm->size() > 0) {
			for ($i = 0; $i < count($this->squadronsIds); $i++) {
				for ($j = 0; $j < 12; $j++) {
					ASM::$obm->get()->setShipStorage($j, ASM::$obm->get()->getShipStorage($j) + $this->getSquadron($i)->getNbrShipByType($j));
				}
				$this->getSquadron($i)->emptySquadron();
			}
		}

		ASM::$obm->changeSession($S_OBM);
	}

	public function uExperienceInSchool($ob, $playerBonus) {
		if ($this->statement == self::INSCHOOL) {

			$invest = $ob->iSchool;
			
			// load bonus
			$invest += $invest * $playerBonus->get(PlayerBonus::COMMANDER_INVEST) / 100;
			$coeff = $invest / 100;
			$earnedExperience  = round(log($coeff + 1) / log(2) * 20);
			$earnedExperience += rand(-23, 23);
			$earnedExperience = round($earnedExperience / 15);
			$earnedExperience  = ($earnedExperience < 0) ? 0 : $earnedExperience;
			
			$this->upExperience($earnedExperience);
		}
	}

	public function move($rDestinationPlace, $rStartPlace, $travelType, $travelLength, $duration) {
		$this->rDestinationPlace = $rDestinationPlace;
		$this->rStartPlace = $rStartPlace;
		$this->travelType = $travelType;
		$this->travelLength = $travelLength;
		$this->statement = 2;

		$this->dStart = ($travelType != 3) ? Utils::now() : $this->dArrival;
		$date = new DateTime($this->dStart);
		$date->modify('+' . $duration . 'second');
		$this->dArrival = $date->format('Y-m-d H:i:s');

		// ajout de l'event dans le contrôleur // voir avec Jacky s'il faut modifier
		if (CTR::$data->exist('playerEvent')) {
			CTR::$data->get('playerEvent')->add(
				$this->dArrival,
				EVENT_OUTGOING_ATTACK,
				$this->id,
				$this->getEventInfo()
			);
		}

		return TRUE;
	}
	
	public function resultOfFight($isWinner, $enemyCommander) {
		if ($isWinner == TRUE) {
			$this->setEarnedExperience($enemyCommander);
			$this->earnedExperience = round($this->earnedExperience);
			LiveReport::$expCom = $this->earnedExperience;

			$this->winner = TRUE;
			$this->palmares++;
			$this->setArmyAtEnd();
			$this->upExperience($this->earnedExperience);
			$this->hasChanged = TRUE;
		} else {
			$this->setEarnedExperience($enemyCommander);
			$this->earnedExperience = round($this->earnedExperience);

			$this->winner = FALSE;
			$this->setArmyAtEnd();
			$this->upExperience($this->earnedExperience);
			$this->hasChanged = TRUE;
		}
	}

	# ENGAGE UN COMBAT ENTRE CHAQUE SQUADRON CONTRE UN COMMANDANT
	public function engage($enemyCommander, $thisCommander) {
		$this->setArmy();
		$idSquadron = 0;
		foreach ($this->army as $squadron) {
			if ($squadron->getNbrShips() != 0 AND $squadron->getLineCoord() * 3 <= FightController::getCurrentLine()) {
				$enemyCommander = $squadron->engage($enemyCommander, $idSquadron, $this->id, $this->name, $thisCommander);
			}
			$idSquadron++;
		}
		return $enemyCommander;
	}

	public function getPosition ($x1, $y1, $x2, $y2) {
		$x = $x1;
		$y = $y1;
		if ($this->statement == self::MOVING) {
			$parcouredTime = Utils::interval($this->dStart, Utils::now(), 's');
			$totalTime = Utils::interval($this->dStart, $this->dArrival, 's');
			$progression = $parcouredTime / $totalTime;

			$x = $x1 + $progression * ($x2-$x1);
			$y = $y1 + $progression * ($y2-$y1);
		}
		return array($x, $y);
	}

	public function getEventInfo() {
		$info = new ArrayList();
		$info->add('name', $this->name);
		$info->add('avatar', $this->avatar);
		$info->add('level', $this->level);

		$info->add('dStart', $this->dStart);
		$info->add('rStart', $this->rStartPlace);
		$info->add('nStart', $this->startPlaceName);
		$info->add('dArrival', $this->dArrival);
		$info->add('rArrival', $this->rDestinationPlace);
		$info->add('nArrival', $this->destinationPlaceName);

		$info->add('travelType', $this->travelType);
		$info->add('resources', $this->resources);

		return $info;
	}

	public function uCommander() {
		$token = CTC::createContext();
		$now = Utils::now();

		# check s'il gagne de l'exp à l'école
		if (Utils::interval($this->uCommander, $now, 's') > 0 AND $this->statement == self::INSCHOOL) {
			$nbrHours = Utils::intervalDates($now, $this->uCommander);
			$this->uCommander = $now;

			include_once ATHENA;

			$S_OBM = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlace' => $this->rBase));
			$ob = ASM::$obm->get();
			ASM::$obm->changeSession($S_OBM);
				
			include_once ZEUS;
			$playerBonus = 0;
			if ($this->rPlayer != CTR::$data->get('playerId')) {
				$playerBonus = new PlayerBonus($this->rPlayer);
				$playerBonus->load();
				$playerBonus = $playerBonus->bonus;
			} else {
				$playerBonus = CTR::$data->get('playerBonus');
			}

			foreach ($nbrHours as $hour) {
				CTC::add($hour, $this, 'uExperienceInSchool', array($ob, $playerBonus));
			}
		}

		# test si il y a des combats
		if ($this->dArrival <= Utils::now() AND $this->statement == self::MOVING AND $this->hasToU) {
			include_once GAIA;

			$this->hasToU = FALSE;

			$S_PLM = ASM::$plm->getCurrentSession();
			ASM::$plm->newSession();
			ASM::$plm->load(array('id' => $this->rDestinationPlace));
			$pl = ASM::$plm->get();
			ASM::$plm->changeSession($S_PLM);
		}

		CTC::applyContext($token);
	}
}
?>