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

use Asylamba\Classes\Worker\CTR;

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

	protected $synchronized = FALSE;
	
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

	public function setId($v, $playerId) { 
		$this->id = $v; 
		if ($v == $playerId) {
			$this->synchronized = TRUE;
		}
	}
	public function setBind($v) {
		$this->bind = $v;
	}
	public function setRColor($v) { 
		$this->rColor = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('color', $v);
		}
	}
	public function setName($v) {
		$this->name = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('name', $v);
		}
	}
	public function setAvatar($v) { 
		$this->avatar = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('avatar', $v);
		}
	}
	public function setStatus($v) 			{ $this->status = $v; }
	public function setCredit($v) { 
		$this->credit = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('credit', $v);
		}
	}
	public function setExperience($v) { 
		$this->experience = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('experience', $v);
		}
	}
	public function setLevel($v) { 
		$this->level = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('level', $v);
		}
	}
	public function setVictory($v) 			{ $this->victory = $v; }
	public function setDefeat($v) 			{ $this->defeat = $v; }
	public function setStepTutorial($v) 	{ $this->stepTutorial = $v; }
	public function setDInscription($v) 	{ $this->dInscription = $v; }
	public function setDLastConnection($v) 	{ $this->dLastConnection = $v; }
	public function setDLastActivity($v) 	{ $this->dLastActivity = $v; }
	public function setPremium($v) 			{ $this->premium = $v; }
	public function setStatement($v) 		{ $this->statement = $v; }

	public function increaseVictory($i) 	{ $this->victory += $i; }
	public function increaseDefeat($i) 		{ $this->defeat += $i; }

}