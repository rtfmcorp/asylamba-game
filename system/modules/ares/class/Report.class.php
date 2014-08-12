<?php

/**
 * Report
 *
 * @author Noé Zufferey
 * @copyright Asylamba - le jeu
 *
 * @package Arès
 * @update 01.06.14
*/

#TODO 
/*
avatar
name
level
exp avant le combat
nbr victoires
*/

class Report {
	public $id					= 0;
	public $rPlayerAttacker		= 0;
	public $rPlayerDefender		= 0;
	public $rPlayerWinner		= 0;
	public $avatarA				= '';
	public $avatarD				= '';
	public $nameA				= '';
	public $nameD				= '';
	public $levelA				= 0;
	public $levelD				= 0;
	public $experienceA			= 0;
	public $experienceD			= 0;
	public $palmaresA			= 0;
	public $palmaresD			= 0;
	public $resources			= 0;
	public $expCom				= 0;
	public $expPlayerA			= 0;
	public $expPlayerD			= 0;
	public $rPlace				= 0;
	public $type				= 0;
	public $round				= 0;
	public $importance			= 0;
	public $statementAttacker	= 0;
	public $statementDefender	= 0;
	public $dFight				= '';
	public $placeName			= '';

	public $squadrons = array();

	public $armyInBeginA = array();
	public $armyInBeginD = array();
	public $armyAtEndA = array();
	public $armyAtEndD = array();

	public $fight = array();

	public $totalInBeginA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalInBeginD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalAtEndA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalAtEndD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $diferenceA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $diferenceD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

	public function getId() { return $this->id; }

	public function setArmies($squadrons) {
		$this->squadrons = $squadrons;
		
		# squadron(id, pos, rReport, round, rCommander, ship0, ..., ship11)
		$rCommanderA = $this->squadrons[0][4];

		foreach ($this->squadrons AS $sq) {
			if ($sq[3] == 0) {
				if ($sq[4] == $rCommanderA) {
					$this->armyInBeginA[] = $sq;
				} else {
					$this->armyInBeginD[] = $sq;
				}
			} elseif ($sq[3] > 0) {
				$this->fight[] = $sq;
			} else {
				if ($sq[4] == $rCommanderA) {
					$this->armyAtEndA[] = $sq;
				} else {
					$this->armyatEndD[] = $sq;
				}
			}
		}

		foreach ($this->armyInBeginA AS $sq) {
			for ($i = 5; $i < 16; $i++) {
				$this->totalInBeginA[$i - 5] += $sq[$i];
			}
		}
		foreach ($this->armyInBeginD AS $sq) {
			for ($i = 5; $i < 16; $i++) {
				$this->totalInBeginD[$i - 5] += $sq[$i];
			}
		}
		foreach ($this->armyAtEndA AS $sq) {
			for ($i = 5; $i < 16; $i++) {
				$this->totalAtEndA[$i - 5] += $sq[$i];
			}
		}
		foreach ($this->armyAtEndD AS $sq) {
			for ($i = 5; $i < 16; $i++) {
				$this->totalAtEndD[$i - 5] += $sq[$i];
			}
		}

		for ($i = 0; $i < 12; $i++) {
			$this->diferenceA[$i] = $this->totalInBeginA[$i] - $this->totalAtEndA[$i];
		}
		for ($i = 0; $i < 12; $i++) {
			$this->diferenceD[$i] = $this->totalInBeginD[$i] - $this->totalAtEndD[$i];
		}
	}
}