<?php
/**
*Spy, mai 2013
* @author Noé Zufferey
* copyright Expansion
* @package artemis
*/

class Spy {
	public $id 						= 0;
	public $name 					= '';
	public $avatar 					= '';
	public $rPlayer 				= 0;
	public $rSystem					= 0;
	public $playerName				='';
	public $playerColor				='';
	public $comment 				= '';
	public $sexe 					= 0;
	public $age 					= 0;
	public $levelSkillBase			= 0;
	public $levelSkillArmy			= 0;
	public $levelSkillResources		= 0;
	public $level 					= 0;
	public $experience 				= 0;
	public $uExperience 			= 0;
	public $rSystemDestination 		= 0;
	public $arrivalDate 			= '';
	public $uTravel 				= '';
	public $uDeath					= '';
	public $statement 				= 0;
	public $dCreation 				= '';
	public $dDeath 					= '';

	public function getId() { return $this->id; }

	public function increaseSkillBase() {
		$this->levelSkillBase++;
	}
	public function increaseSkillArmy() {
		$this->levelSkillArmy++;
	}
	public function increaseSkillResources() {
		$this->levelSkillResources++;
	}

	public function executeUMethode() {
		$this->uTravel();
		$this->uExperience();
		$this->uDeath();
	}

	public function uTravel() {
		if ($this->statement == SPY_INMOVE) {
			if (Utils::now() >= $this->arrivalDate) {
				$this->statement = SPY_ACTIVE;
				$this->rSystem = $this->rSystemDestination;
				$this->uExperience = Utils::now();

				include_once HERMES;
				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Déplacement d\'espion');
				$notif->addBeg();
				$notif->addTxt('Votre espion ');
				$notif->addStg($this->name); 
				$notif->addTxt(' est arrivé a ');
				$notif->addLnk('', 'destination.'); //lien 
				$notif->addEnd();
				ASM::$ntm->add($notif);
			}
		}
	}
	
	public function experienceToLevelUp() {
		return pow(2, $this->level) * SPY_BASELVL;
	}

	public function upExperience($earnedExperience) {
		$this->experience += $earnedExperience;

		while($this->experience >= $this->experienceToLevelUp()) {
			$this->level++;
			CTR::$alert->add('Votre espion ' . $this->name . 'gagne un niveau.', ALERT_STD_SUCCESS);
		}
	}

	public function uExperience() {
		if ($this->statement == SPY_ACTIVE) {
			$oldDate = ($this->uExperience == NULL) ? Utils::now() : $this->uExperience;
			$newDate = Utils::now();
			$interval = Utils::interval($oldDate, $newDate);
			if ($interval >= 1) {
				$this->uExperience = $newDate;

				for ($i = 0; $i < $interval; $i++) {
					$earnedExperience = rand(1, 5);
					$this->upExperience($earnedExperience);
				}
			}
		}
	}

	public function uDeath() {
		if ($this->statement == SPY_ACTIVE) {
			$oldDate = ($this->uDeath == NULL) ? Utils::now() : $this->uDeath;
			$newDate = Utils::now();
			$interval = Utils::interval($oldDate, $newDate);
			$age = Utils::interval($this->dCreation, Utils::now()) + $this->age * 42;
			if($interval - $this->age * 42 >= 48) {
				for ($i = 0; $i < $interval; $i += 24) {
					$probabilityToDie = ceil(pow(2, $age));
					$probabilityToDie = ($probabilityToDie > 800) ? 800 : $probabilityToDie;
					$throw = rand(1, SPY_LIFEEXPENCTANCY);
					if ($throw > SPY_LIFEEXPENCTANCY - $probabilityToDie) {
						$this->statement = SPY_DEAD;

						include_once HERMES;
						$notif = new Notification();
						$notif->setRPlayer($this->rPlayer());
						$notif->setTitle('Perte d\'espion');
						$notif->addBeg();
						$notif->addTxt('Votre espion ');
						$notif->addStg($this->name()); 
						$notif->addTxt(' a été découvert et éliminé par l\'ennemi.'); // à faire un roulement aléatoire de trucs qui lui arrivent
						$notif->addEnd();
						ASM::$ntm->add($notif);
					}
				}
			}
		}
	}

	public function move($destination, $duration) {
		if ($this->statement == SPY_ACTIVE) {
			$this->rSystemDestination = $destination;
			$this->statement = SPY_INMOVE;
			$date = new DateTime(Utils::now());
			$date->modify('+' . $duration . 'second');
			$this->arrivalDate = $date->format('Y-m-d H:i:s');
			return TRUE;
		} else {
			CTR::$alert->add('Cet espion ne peut pas se déplacer.', ALERT_STD_ERROR);
			return FALSE;
		}
	}
}
?>