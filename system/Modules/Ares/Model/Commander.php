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

namespace Asylamba\Modules\Ares\Model;

use Asylamba\Modules\Athena\Resource\ShipResource;

class Commander
{
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
	public $statement 				= Commander::INSCHOOL;
	public $line 					= 1;
	public $dCreation 				= '';
	public $dAffectation 			= '';
	public $dDeath 					= '';

	# variables de jointure quelconque
	public $oBName					= '';
	public $playerName				= '';
	public $playerColor				= 0;

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
	public $startPlacePop			= 0;
	public $destinationPlaceName	= '';
	public $destinationPlacePop		= 0;
	# Tableau d'objets squadron       
	public $army = array();

	public $uCommander				= '';
	public $hasToU					= TRUE;
	public $hasArmySetted			= FALSE;
	public $uMethodCtced			= FALSE;
	public $lastUMethod				= NULL;
    
	const COEFFSCHOOL 				= 100;
	const COEFFEARNEDEXP 			= 50;
	const COEFFEXPPLAYER			= 100;
	const CMDBASELVL 				= 100;
	
	const FLEETSPEED 				= 35;
    
	const COEFFMOVEINSYSTEM 		= 584;
	const COEFFMOVEOUTOFSYSTEM 		= 600;
	const COEFFMOVEINTERSYSTEM 		= 50000;

	const LVLINCOMECOMMANDER 		= 200;

	const CREDITCOEFFTOCOLONIZE		= 80000;
	const CREDITCOEFFTOCONQUER		= 150000;

	# loot const
	const LIMITTOLOOT 				= 5000;
	const COEFFLOOT 				= 275;

	# Commander statements
	const INSCHOOL 					= 0; # dans l'école
	const AFFECTED 					= 1; # autour de la base
	const MOVING 					= 2; # en déplacement
	const DEAD 						= 3; # mort
	const DESERT 					= 4; # déserté
	const RETIRED 					= 5; # à la retraite
	const ONSALE 					= 6; # dans le marché
	const RESERVE 					= 7; # dans la réserve (comme à l'école mais n'apprend pas)

	# types of travel
	const MOVE						= 0; # déplacement
	const LOOT						= 1; # pillage
	const COLO						= 2; # colo ou conquete
	const BACK						= 3; # retour après une action

	const MAXTRAVELTIME				= 57600;
	const DISTANCEMAX				= 30;

	# Const de lineCoord
	public static $LINECOORD = array(1, 1, 1, 2, 2, 1, 2, 3, 3, 1, 2, 3, 4, 4, 2, 3, 4, 5, 5, 3, 4, 5, 6, 6, 4, 5, 6, 7, 7, 5, 6, 7);

	/**
     * @param int $id
     * @return $this
     */
	public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param string $name
     * @return $this
     */
	public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $avatar
     * @return $this
     */
	public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getAvatar()
    {
        return $this->avatar;
    }
    
    /**
     * @param int $rPlayer
     * @return $this
     */
	public function setRPlayer($rPlayer)
    {
        $this->rPlayer = $rPlayer;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getRPlayer()
    {
        return $this->rPlayer;
    }
    
    /**
     * @param string $playerName
     * @return $this
     */
	public function setPlayerName($playerName)
    {
        $this->playerName = $playerName;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getPlayerName()
    {
        return $this->playerName;
    }
    
    /**
     * @param int $playerColor
     * @return $this
     */
	public function setPlayerColor($playerColor)
    {
        $this->playerColor = $playerColor;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getPlayerColor()
    {
        return $this->playerColor;
    }
    
    /**
     * @param int $rBase
     * @return $this
     */
	public function setRBase($rBase)
    {
        $this->rBase = $rBase;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getRBase()
    {
        return $this->rBase;
    }
    
    /**
     * @param string $comment
     * @return $this
     */
	public function setComment($comment)
    {
        $this->comment = $comment;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getComment()
    {
        return $this->comment;
    }
    
    /**
     * @param int $sexe
     * @return $this
     */
	public function setSexe($sexe)
    {
        $this->sexe = $sexe;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getSexe()
    {
        return $this->sexe;
    }
    
    /**
     * @param int $age
     * @return $this
     */
	public function setAge($age)
    {
        $this->age = $age;
		
		return $this;
    }
    
    /**
     * @return int
     */
	public function getAge()
    {
        return $this->age;
    }
    
    /**
     * @param int $level
     * @return $this
     */
	public function setLevel($level)
    {
        $this->level = $level;
        
        return $this;
    } 	
    
    /**
     * @return int
     */
	public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * @param int $experience
     * @return $this
     */
	public function setExperience($experience)
    {
        $this->experience = $experience;
        
        return $this;
    }

    /**
     * @return int
     */
	public function getExperience()
    {
        return $this->experience;
    }
    
    /**
     * @param int $earnedExperience
     * @return $this
     */
    public function setEarnedExperience($earnedExperience)
    {
        $this->earnedExperience = $earnedExperience;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getEarnedExperience()
    {
        return $this->earnedExperience;
    }
    
    /**
     * @param string $updatedAt
     * @return $this
     */
	public function setUpdatedAt($updatedAt)
    {
        $this->uCommander = $updatedAt;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getUpdatedAt()
    {
        return $this->uCommander;
    }
    
    /**
     * @param int $palmares
     * @return $this
     */
	public function setPalmares($palmares)
    {
        $this->palmares = $palmares;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getPalmares()
    {
        return $this->palmares;
    }
    
    /**
     * @param int $travelType
     */
	public function setTravelType($travelType)
    {
        $this->travelType = $travelType;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getTravelType()
    {
        return $this->travelType;
    }
	
	/**
	 * @param int $travelLength
	 * @return Commander
	 */
	public function setTravelLength($travelLength)
	{
		$this->travelLength = $travelLength;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTravelLength()
	{
		return $this->travelLength;
	}
	
	/**
	 * @param int $startPlaceId
	 * @return Commander
	 */
	public function setStartPlaceId($startPlaceId)
	{
		$this->rStartPlace = $startPlaceId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getStartPlaceId()
	{
		return $this->rStartPlace;
	}
    
    /**
     * @param int $rDestinationPlace
     * @return $this
     */
	public function setRPlaceDestination($rDestinationPlace)
    {
        $this->rDestinationPlace = $rDestinationPlace;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getRPlaceDestination()
    {
        return $this->rDestinationPlace;
    }
    
    /**
     * @param int $resources
     * @return $this
     */
	public function setResources($resources)
    {
        $this->resources = $resources;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getResources()
    {
        return $this->resources;
    }
    
    /**
     * @param int $statement
     * @return $this
     */
	public function setStatement($statement)
    {
        $this->statement = $statement;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getStatement()
    {
        return $this->statement;
    }
    
    /**
     * @param string $dCreation
     * @return $this
     */
	public function setDCreation($dCreation)
    {
        $this->dCreation = $dCreation;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getDCreation()
    {
        return $this->dCreation;
    }
    
    /**
     * @param string $dAffectation
     * @return $this
     */
	public function setDAffectation($dAffectation)
    {
        $this->dAffectation = $dAffectation;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getDAffectation()
    {
        return $this->dAffectation;
    }
	
	/**
	 * @param string $startedAt
	 * @return \Asylamba\Modules\Ares\Model\Commander
	 */
	public function setStartedAt($startedAt)
	{
		$this->dStart = $startedAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getStartedAt()
	{
		return $this->dStart;
	}
    
    /**
     * @param string $arrivalDate
     * @return $this
     */
    public function setArrivalDate($arrivalDate)
    {
        $this->dArrival = $arrivalDate;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getArrivalDate()
    {
        return $this->dArrival;
    }
    
    /**
     * @param string $dDeath
     * @return $this
     */
	public function setDDeath($dDeath)
    {
        $this->dDeath = $dDeath;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getDDeath()
    {
        return $this->dDeath;
    }
    
    /**
     * @param int $lengthTravel
     * @return $this
     */
	public function setLengthTravel($lengthTravel)
    {
        $this->lengthTravel = $lengthTravel;
        
        return $this;
    }
    
    /**
     * @return int
     */
	public function getLengthTravel()
    {
        return $this->lengthTravel;
    }
    
    /**
     * @param string $oBName
     * @return $this
     */
	public function setBaseName($oBName)
    {
        $this->oBName = $oBName;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getBaseName()
    {
        return $this->oBName;
    }
    
    /**
     * @param string $doName
     * @return $this
     */
	public function setDestinationPlaceName($doName)
    {
        $this->destinationPlaceName = $doName;
        
        return $this;
    }
    
    /**
     * @return string
     */
	public function getDestinationPlaceName()
    {
        return $this->destinationPlaceName;
    }
    
    /**
     * @param array $squadronsIds
     * @return $this
     */
	public function setSquadronsIds($squadronsIds)
    {
        $this->squadronsIds = $squadronsIds;
        
        return $this;
    }
    
    /**
     * @param int $squadronId
     * @return $this
     */
    public function addSquadronId($squadronId)
    {
        $this->squadronsIds[] = $squadronId;
        
        return $this;
    }
    
    /**
     * @return array
     */
	public function getSquadronsIds()
    {
        return $this->squadronsIds;
    }
    
    /**
     * @param array $armyInBegin
     * @return $this
     */
	public function setArmyInBegin($armyInBegin)
    {
        $this->armyInBegin = $armyInBegin;
        
        return $this;
    }
    
    /**
     * @param array $army
     * @return $this
     */
    public function addArmyInBegin($army)
    {
        $this->armyInBegin[] = $army;
        
        return $this;
    }
    
    /**
     * @return array
     */
	public function getArmyInBegin()
    {
        return $this->armyInBegin;
    }
    
    /**
     * @param bool $isAttacker
     * @return $this
     */
	public function setIsAttacker($isAttacker)
    {
        $this->isAttacker = $isAttacker;
        
        return $this;
    }
    
    /**
     * @return bool
     */
	public function getIsAttacker()
    {
        return $this->isAttacker;
    }

	public function setArmy()
    {
		if (!$this->hasArmySetted) {
			for( $i = 0; $i < count($this->squadronsIds) AND $i < 25; $i++) {
				$this->army[$i] = new Squadron(
					$this->armyInBegin[$i], 
					$this->squadronsIds[$i], 
					self::$LINECOORD[$i], 
					$i, 
					$this->id);
			}
			$this->setPevInBegin();
			$this->hasArmySetted = TRUE;
		}
	}
    
	public function getArmy()
    {
        $this->setArmy();
        return $this->army;
    }

	public function setPevInBegin() {
		$pev = 0;
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$pev += $squadron[$i] * ShipResource::getInfo($i, 'pev');
			}
		}
		$this->pevInBegin = $pev;
	}
    
	public function getPevInBegin()
    {
        return $this->pevInBegin;
    }
	
	public function getPev() {
		$pev = 0;
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$pev += $squadron[$i] * ShipResource::getInfo($i, 'pev');
			}
		}
		return $pev;
	}

	public function setArmyAtEnd()
    {
		$this->setArmy();
		$i = 0;
		foreach ($this->army AS $squadron) {
			$this->armyAtEnd[$i] = $squadron->getArrayOfShips();
			$i++;
		}
	}
    
	public function getArmyAtEnd()
    {
        return $this->armyAtEnd;
    }

	public function getFormatLineCoord()
    {
		$return = array();

		for ($i = 0; $i < ($this->level + 1); $i++) { 
			$return[] = self::$LINECOORD[$i];
		}
		return $return;
	}
    
	public function getSizeArmy()
    {
        return count($this->squadronsIds);
    }

	public function getPevToLoot()
    {
		$pev = 0;
		foreach ($this->armyAtEnd as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$pev += $squadron[$i] * ShipResource::getInfo($i, 'pev');
			}
		}

		if ($pev != 0) {
			return $pev;
		} else {
			return $this->getPev();
		}
	}
	
	public function getSquadron($i)	
    {
		$this->setArmy();
		if (!empty($this->army[$i])) {
			return $this->army[$i]; 
		} else {
			return FALSE;
		}
	}

	# renvoie un tableau de nombre de vaisseaux
	public function getNbrShipByType()
    {
		$array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		foreach ($this->armyInBegin as $squadron) {
			for ($i = 0; $i < 12; $i++) {
				$array[$i] += $squadron[$i];
			}
		}
		return $array;
	}
}