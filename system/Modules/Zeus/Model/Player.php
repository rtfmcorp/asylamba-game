<?php

/**
 * Player
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @update 20.05.13
 */

namespace Asylamba\Modules\Zeus\Model;

class Player {
	public $id = 0; 
	public $bind = 0;
	public $rColor = 0;
	public $rGodfather = NULL;
	public $name = '';
	public $sex = 0;
	public $description = '';
	public $avatar = '';
	public $status = 1;
	public $credit = 0;
	public $uPlayer = '';
	public $experience = 0;
	public $factionPoint = 0;
	public $level = 0;
	public $victory = 0;
	public $defeat = 0;
	public $stepTutorial = 1;
	public $stepDone = FALSE;
	public $iUniversity = 5000;
	public $partNaturalSciences = 25;
	public $partLifeSciences = 25;
	public $partSocialPoliticalSciences = 25;
	public $partInformaticEngineering = 25;
	public $dInscription = '';
	public $dLastConnection = '';
	public $dLastActivity = '';
	public $premium = 0; 	# 0 = publicité, 1 = pas de publicité
	public $statement = 0;

	public $synchronized = FALSE;
	
	const ACTIVE = 1;
    const INACTIVE =  2;
    const HOLIDAY = 3;
    const BANNED = 4;
    const DELETED = 5;
    const DEAD = 6;
	
	const STANDARD = 1;
    const PARLIAMENT = 2;
    const TREASURER = 3;
    const WARLORD = 4;
    const MINISTER = 5;
    const CHIEF = 6;

	public function getId()					{ return $this->id; }
	public function getBind()				{ return $this->bind; }
	public function getRColor()				{ return $this->rColor; }
	public function getName()				{ return $this->name; }
	public function getAvatar()				{ return $this->avatar; }
	public function getStatus()				{ return $this->status; }
	public function getCredit()				{ return $this->credit; }
	public function getExperience()			{ return $this->experience; }
	public function getLevel()				{ return $this->level; }
	public function getVictory()			{ return $this->victory; }
	public function getDefeat()				{ return $this->defeat; }
	public function getStepTutorial()		{ return $this->stepTutorial; }
	public function getDInscription()		{ return $this->dInscription; }
	public function getDLastConnection()	{ return $this->dLastConnection; }
	public function getDLastActivity()		{ return $this->dLastActivity; }
	public function getPremium()			{ return $this->premium; }
	public function getStatement()			{ return $this->statement; }
	
	/**
	 * @return boolean
	 */
	public function isSynchronized()
	{
		return $this->synchronized;
	}

	public function setId($v) { 
		$this->id = $v;
		
		return $this;
	}
	public function setBind($v) {
		$this->bind = $v;
		
		return $this;
	}
	public function setRColor($v) { 
		$this->rColor = $v; 
		
		return $this;
	}
	public function setName($v) {
		$this->name = $v;
		
		return $this;
	}
	public function setAvatar($v) { 
		$this->avatar = $v;
		
		return $this;
	}
	public function setStatus($v) 
	{
		$this->status = $v;
		
		return $this;
	}
	
	public function setCredit($v) { 
		$this->credit = $v;
		
		return $this;
	}
	public function setExperience($v) { 
		$this->experience = $v;
		
		return $this;
	}
	public function setLevel($v) { 
		$this->level = $v;
		
		return $this;
	}
	public function setVictory($v) 			{ $this->victory = $v;
		
		return $this;}
	public function setDefeat($v) 			{ $this->defeat = $v;
		
		return $this;}
	public function setStepTutorial($v) 	{ $this->stepTutorial = $v;
		
		return $this;}
	public function setDInscription($v) 	{ $this->dInscription = $v;
		
		return $this;}
	public function setDLastConnection($v) 	{ $this->dLastConnection = $v;
		
		return $this;}
	public function setDLastActivity($v) 	{ $this->dLastActivity = $v;
		
		return $this;}
	public function setPremium($v) 			{ $this->premium = $v;
		
		return $this;}
	public function setStatement($v) 		{ $this->statement = $v;
		
		return $this;}

	public function increaseVictory($i) 	{ $this->victory += $i;
		
		return $this;}
	public function increaseDefeat($i) 		{ $this->defeat += $i;
		
		return $this;}

}