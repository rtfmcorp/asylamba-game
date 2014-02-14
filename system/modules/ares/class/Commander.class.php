<?php

/**
 * Commander
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Ares
 * @update 13.02.14
*/

class Commander {
	const COEFFEARNEDEXP 			= 50;
	const COEFFEXPPLAYER			= 100;
	const CMDBASELVL 				= 100;

	const COEFFMOVEINSYSTEM 		= 584;
	const COEFFMOVEOUTOFSYSTEM 		= 600;
	const COEFFMOVEINTERSYSTEM 		= 1000;

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

	# travel statements
	const DONE 						= 0; # déplacement fini dans le passé
	const INPROGRESS 				= 1; # déplacement en cours
	const COMPLETED					= 2; # déplacement prévu dans l'avenir (ne sera peut-être jamais implémenté)

	# types of travel
	const MOVE						= 0; # déplacement
	const LOOT						= 1; # pillage
	const COLO						= 2; # colo ou conquete
	const BACK						= 3; # retour après une action

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
	public $dCreation 				= '';
	public $dAffectation 			= '';
	public $dDeath 					= '';

	# variable de jointure quelconque
	public $oBName					= '';
	public $playerName				='';
	public $playerColor				='';

	# variables de trajet
	public $dStart					= '';
	public $dArrival	 			= '';
	public $resourcesTransported 	= 0;
	public $typeOfMove 				= 0;
	public $travelLength			= 0;
	public $rStartPlace 			= 0;
	public $rDestinationPlace		= 0;
	public $startPlaceName			= '';
	public $destinationPlaceName	= '';

	#variables de ocmbat
	public $squadronsIds			= array();
	public $armyInBegin 			= array();
	public $armyAtEnd 				= array();
	public $pevInBegin 				= 0;
	public $earnedExperience 		= 0;
	public $winner					= FALSE;
	public $isAttacker 				= NULL;

	public $hasToU					= TRUE;


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
	public function getUExperience() 			{ return $this->uExperience; }
	public function getPalmares() 				{ return $this->palmares; }
	public function getTypeOfMove() 			{ return $this->typeOfMove; }
	public function getRPlaceDestination() 		{ return $this->rPlaceDestination; }
	public function getArrivalDate() 			{ return $this->arrivalDate; }
	public function getResourcesTransported() 	{ return $this->resourcesTransported; }
	public function getUTravel() 				{ return $this->uTravel; }
	public function getStatement() 				{ return $this->statement; }
	public function getDCreation() 				{ return $this->dCreation; }
	public function getDAffectation() 			{ return $this->dAffectation; }
	public function getDDeath() 				{ return $this->dDeath; }
	public function getLengthTravel()			{ return $this->lengthTravel; }
	public function getOBName()					{ return $this->oBName; }
	public function getArmyInBegin()			{ return $this->armyInBegin; }
	public function setIsAttacker($isAttacker)	{ $this->isAttacker = $isAttacker; } 			  

	public function getDestinationPlaceName()	{
		return ($this->destinationPlaceName == NULL) ? 'planète rebelle' : $this->destinationPlaceName;
	}
	public function getSquadronsIds()			{ return $this->squadronsIds; }
	public function getArmy()					{ return $this->army; }

	public function getFormatLineCoord() {
		$return = array();

		for ($i = 0; $i < ($this->level + 1); $i++) { 
			$return[] = self::$LINECOORD[$i];
		}

		return $return;
	}
	public function getSizeArmy() { return count($this->army); }

	public function getPev() {
		$pev = 0;
		foreach ($this->army AS $squadron) {
			$pev += $squadron->getPev();
		}
		return $pev;
	}
	
	public function getSquadron($i)	{
		if (!empty($this->army[$i])) {
			return $this->army[$i]; 
		} else {
			return FALSE;
		}
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
	public function setUExperience($uExperience) 					{ $this->uExperience = $uExperience; } 	      
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

	public function setArmy() {
		for($i = 0; $i < count($this->squadronsIds) AND $i < 25; $i++) {
			$this->army[$i] = new Squadron(
				$this->armyInBegin[$i], 
				$this->squadronsIds[$i], 
				self::$LINECOORD[$i], 
				$i, 
				$this->id);
		}
	}

	public function setPevInBegin() {
		foreach ($this->army AS $squadron) {
			foreach ($squadron->getSquadron() AS $ship) {
				$this->pevInBegin += $ship->getPev();
			}
		}
	}

	private function setArmyAtEnd() {
		$i = 0;
		foreach ($this->army AS $squadron) {
			$this->armyAtEnd[$i] = $squadron->getArrayOfShips();
			$i++;
		}
	}

	private function setEarnedExperience($enemyCommander) {
		include_once ZEUS;
		$finalOwnPev = 0;

		foreach ($this->army AS $squadron) {
			foreach ($squadron->getSquadron() AS $ship) {
				$finalOwnPev += $ship->getPev();
			}
		}
		$importance = ($finalOwnPev + 1) * ($enemyCommander->getPevInBegin() / ($this->pevInBegin + 1)) * ($enemyCommander->getLevel() / $this->level);
		$this->earnedExperience = $importance * COM_COEFFEARNEDEXP;
		if($this->winner) {
			LiveReport::$importance = $importance;
		}
		
		if ($this->rPlayer > 0) {
			$S_PLM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(TRUE, FALSE);
			ASM::$pam->load(array('id' => $this->rPlayer));
			ASM::$pam->get(0)->increaseExperience(round($this->earnedExperience / COEFFEXPPLAYER));
			ASM::$pam->changeSession($S_PLM1);
		}
	}

	public function setBonus() {
		$playerBonus = new PlayerBonus($this->rPlayer);
		$playerBonus->load();

		foreach ($this->army AS $squadron) {
			foreach ($squadron->squadron AS $ship) {
				$ship->setBonus($playerBonus->bonus);
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

	public function uExperienceInSchool($invest) {
		include_once ZEUS;

		if ($this->statement == COM_INSCHOOL) {
				$this->uExperience = $newDate;
				
				// load bonus
				$playerBonus = new PlayerBonus($this->rPlayer);
				$playerBonus->load();

				$invest += $invest * $playerBonus->bonus->get(PlayerBonus::COMMANDER_INVEST) / 100;
				$coeff = $invest / 100;
				$earnedExperience  = round(log($coeff + 1) / log(2) * 20);
				$earnedExperience += rand(-23, 23);
				$earnedExperience = round($earnedExperience / 15);
				$earnedExperience  = ($earnedExperience < 0) ? 0 : $earnedExperience;
				
				$this->upExperience($earnedExperience);
		}
	}

	/*public function uTravel() {
		include_once GAIA;

		if ($this->hasToU == TRUE) {
			if ($this->statement == 2 AND $this->typeOfMove != 3) {
				if (Utils::now() >= $this->arrivalDate AND $this->rPlaceDestination != NULL) {
					$this->hasToU = FALSE;

					$S_PLM10 = ASM::$plm->getCurrentSession();
					ASM::$plm->newSession(ASM_UMODE);
					ASM::$plm->load(array('id' => $this->rPlaceDestination));
					ASM::$plm->changeSession($S_PLM10);	
					}
			} else if ($this->statement == 2 AND $this->typeOfMove == 3) {
				if (Utils::now() >= $this->arrivalDate AND $this->rPlaceDestination != NULL) {
					$this->hasToU = FALSE;

					$S_PLM11 = ASM::$plm->getCurrentSession();
					ASM::$plm->newSession(ASM_UMODE);
					ASM::$plm->load(array('id' => $this->rPlaceDestination));
					ASM::$plm->changeSession($S_PLM11);
					}
			}
		}
	}*/

	public function move($destination, $typeOfMove, $duration) {
		if ($typeOfMove == 3) {
			$startPoint = $this->rPlaceDestination;
			$this->rPlaceDestination = $destination;
			$this->typeOfMove = $typeOfMove;
			$this->statement = 2;
			$date = new DateTime($this->arrivalDate);
			$date->modify('+' . $duration . 'second');
			$arrivalDate = $date->format('Y-m-d H:i:s');
			$this->arrivalDate = $arrivalDate;

			// ajout de l'event dans le contrôleur
			if (CTR::$data->exist('playerEvent')) {
				CTR::$data->get('playerEvent')->add($this->arrivalDate, EVENT_OUTGOING_ATTACK, $this->id);
			}

			return TRUE;
		} else {
			if ($this->statement == 1) {
				$this->rPlaceDestination = $destination;
				$this->typeOfMove = $typeOfMove;
				$this->statement = 2;

				$date = new DateTime(Utils::now());
				$date->modify('+' . $duration . 'second');
				$this->arrivalDate = $date->format('Y-m-d H:i:s');

				// ajout de l'event dans le contrôleur
				if (CTR::$data->exist('playerEvent')) {
					CTR::$data->get('playerEvent')->add($this->arrivalDate, EVENT_OUTGOING_ATTACK, $this->id);
				}

				return TRUE;
			} else {
				CTR::$alert->add('Ce commandant est déjà en déplacement.', ALERT_STD_ERROR);
				CTR::$alert->add('dans move de Commander', ALERT_BUG_ERROR);
				return FALSE;
			}
		}
	}
	
	public function resultOfFight($isWinner, $enemyCommander = NULL) {
		if ($isWinner == TRUE) {
			$this->setEarnedExperience($enemyCommander);

			$this->winner = TRUE;
			$this->palmares++;
			$this->setArmyAtEnd();
			$this->upExperience($this->earnedExperience);
			$this->hasChanged = TRUE;
		} else {
			$this->winner = FALSE;
			$this->setArmyAtEnd();
			$this->upExperience($this->earnedExperience);
			$this->hasChanged = TRUE;
		}
	}

	# ENGAGE UN COMBAT ENTRE CHAQUE SQUADRON CONTRE UN COMMANDANT
	public function engage($enemyCommander, $thisCommander) {
		$idSquadron = 0;
		foreach ($this->army as $squadron) {
			if ($squadron->getNbrOfShips() != 0 AND $squadron->getLineCoord() * 3 <= FightController::getCurrentLine()) {
				$enemyCommander = $squadron->engage($enemyCommander, $idSquadron, $this->id, $this->name, $thisCommander);
			}
			$idSquadron++;
		}
		return $enemyCommander;
	}
}
?>