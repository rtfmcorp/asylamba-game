<?php

/**
*CommanderInFights, septembre 2012
* @author Noé Zufferey
* copyright Expansion
* @package Ares
*/
class CommanderInFight {
	// INFOS COMMANDANT
	
	private $id 					= 0;
	private $name 					= '';
	private $avatar					= '';
	private $experience				= 0;
	private $level 					= 0;
	private $palmares				= 0;
	private $rBase					= 0;
	private $playerName 			= '';
	private $statement  			= NULL;
	private $squadronsIds 			= array();
	private $rPlayer 				= 0;
	private $sexe 					= 0;
	private $age					= 0;
	private $comment 				= ''; 	        
	private $uExperience 			= 0;	        
	private $typeOfMove 			= 0; 	        
	private $rPlaceDestination 		= 0; 	
	private $arrivalDate 			= ''; 	        
	private $resourcesTransported 	= 0;
	private $uTravel 				= ''; 	        
	private $dCreation 				= '';	        
	private $dAffectation 			= '';	    
	private $dDeath 				= '';
	private $oBName					= '';
	private $pevInBegin 			= 0;
	private $armyInBegin 			= array();
	private $armyAtEnd 				= array();
	private $earnedExperience 		= 0;
	private $winner					= FALSE;
	private $isAttacker 			= NULL;


	// TABLEAU D'OBJETS SQUADRON
	private $army = array();

	//const de lineCoord
	private $CONSTLineCoord = array(1, 1, 1, 2, 2, 1, 2, 3, 3, 1, 2, 3, 4, 4, 2, 3, 4, 5, 5, 3, 4, 5, 6, 6, 4, 5, 6, 7, 7, 5, 6, 7);


		// GETTER
	
	public function getId() 					{ return $this->id; }
	public function getName() 					{ return $this->name; }
	public function getAvatar() 				{ return $this->avatar; }
	public function getExperience() 			{ return $this->experience; }
	public function getLevel() 					{ return $this->level; }
	public function getPalmares() 				{ return $this->palmares; }
	public function getRBase() 					{ return $this->rBase; }
	public function getPlayerName() 			{ return $this->playerName; }
	public function getStatement() 				{ return $this->statement; }
	public function getWinner() 				{ return $this->winner; }  
	public function getSquadronsIds() 			{ return $this->squadronsIds;}
	public function getIsAttacker()				{ return $this->isAttacker; }
	public function getRPlayer() 				{ return $this->rPlayer; }
	public function getSexe() 					{ return $this->sexe; }
	public function getAge() 					{ return $this->age; }
	public function getComment() 				{ return $this->comment; } 
	public function getUExperience() 			{ return $this->uExperience; } 	      
	public function getrPlaceDestination() 		{ return $this->rPlaceDestination; } 	
	public function getArrivalDate() 			{ return $this->arrivalDate; } 	        
	public function getResourcesTransported() 	{ return $this->resourcesTransported; }
	public function getUTravel() 				{ return $this->uTravel; } 		   
	public function getDAffectation() 			{ return $this->dAffectation; }	    
	public function getDDeath() 				{ return $this->dDeath; }
	public function getOBName()					{ return $this->oBName; }
	public function getTypeOfMove() 			{ return $this->typeOfMove; }
	public function getDCreation() 				{ return $this->dCreation; }
	public function getArmy()					{ return $this->army; }			  

	public function getPevInBegin() 			{ return $this->pevInBegin; }
	public function getArmyInBegin() 			{ return $this->armyInBegin; }
	public function getArmyAtEnd() 				{ return $this->armyAtEnd; }
	public function getEarnedExperience()		{ return $this->earnedExperience; }
		
	public function getSquadron($i) 			{ return $this->army[$i]; }
 

	//-----------------SETTER---------------
	public function setId($id) 										{ $this->id = $id; } 					
	public function setName($name) 									{ $this->name = $name; } 				
	public function setAvatar($avatar) 								{ $this->avatar = $avatar; } 			          
	public function setRPlayer($rPlayer) 							{ $this->rPlayer = $rPlayer; } 
	public function setRBase($rBase) 								{ $this->rBase = $rBase; }
	public function setPlayerName($playerName) 						{ $this->playerName = $playerName; } 				      	            
	public function setComment($comment) 							{ $this->comment = $comment; }
	public function setIsAttacker($isAttacker)						{ $this->isAttacker = $isAttacker; } 			  
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
	public function setOBName($oBName)								{ $this->oBName = $oBName; }
	public function setSquadronsIds($squadronsIds)					{ $this->squadronsIds = $squadronsIds; }
	public function setArmyInBegin($array)							{ $this->armyInBegin = $array; }

	public function __construct($c) {

		$this->setId($c->getId()); 	
		$this->setRPlayer($c->getRPlayer());
		$this->setPlayerName($c->getPlayerName());								
		$this->setName($c->getName()); 								
		$this->setAvatar($c->getAvatar()); 							
		$this->setRBase($c->getRBase());					
		$this->setComment($c->getComment()); 						
		$this->setSexe($c->getSexe()); 								
		$this->setAge($c->getAge()); 								
		$this->setLevel($c->getLevel()); 							
		$this->setExperience($c->getExperience()); 					
		$this->setUExperience($c->getUExperience()); 					
		$this->setPalmares($c->getPalmares()); 						
		$this->setTypeOfMove($c->getTypeOfMove()); 					
		$this->setrPlaceDestination($c->getRPlaceDestination()); 	
		$this->setArrivalDate($c->getArrivalDate()); 					
		$this->setResourcesTransported($c->getResourcesTransported());
		$this->setStatement($c->getStatement()); 					
		$this->setDCreation($c->getDCreation()); 					
		$this->setDAffectation($c->getDAffectation()); 				
		$this->setDDeath($c->getDDeath());
		$this->setOBName($c->getOBName());

		$this->armyInBegin = $c->getArmyInBegin();
		$this->squadronsIds = $c->getSquadronsIds();						
		$this->setArmy();
	}

	public function setArmy() {
		for($i = 0; $i < count($this->squadronsIds); $i++) {
			$this->army[$i] = new SquadronInFight($this->armyInBegin[$i], 
				$this->squadronsIds[$i], 
				$this->CONSTLineCoord[$i], $i, $this->id, $this->isAttacker);
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
	
	// ENGAGE UN COMBAT ENTRE CHAQUE SQUADRON CONTRE UN COMMANDANT
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
	
	public function upExperience($earnedExperience) {
		$this->experience += $earnedExperience;
		while(1) {
			if ($this->experience >= $this->experienceToLevelUp()) {
				$this->level++;
			} else { break; }
		}
	}

	public function experienceToLevelUp() {
		return pow(2, $this->level) * COM_CMDBASELVL;
	}
}