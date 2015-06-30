<?php

/**
 * Fight Controller
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 14.02.14
*/

class FightController {
	private $isAFight = FALSE;

	# ATTRIBUT STATIC DE LIGNE COURANTE
	
	private static $currentLine = 0;
	
	private static $AFighterBonus = array(1, 1, 1, 1);
	private static $ACorvetteBonus = array(1, 1, 1, 1);
	private static $AFrigateBonus = array(1, 1, 1, 1);
	private static $ADestroyerBonus = array(1, 1, 1, 1);
	
	private static $DFighterBonus = array(1, 1, 1, 1);
	private static $DCorvetteBonus = array(1, 1, 1, 1);
	private static $DFrigateBonus = array(1, 1, 1, 1);
	private static $DDestroyerBonus = array(1, 1, 1, 1);
	
	# REPLACAGE DE LA LIGNE A 3 A CHAQUE INSTANCE DE FIGHTMANAGER
	
	public function __construct($bool = FALSE) {
		if ($bool == NULL){}
		else {$this->isAFight = $bool;}
		self::$currentLine = 3;
	}
	
	# GETTER
	
	public static function getCurrentLine() {
		return self::$currentLine;
	}
	
	public static function getAFighterBonus($i) {
		return self::$AFighterBonus[$i];
	}
	public static function getACorvetteBonus($i) {
		return self::$ACorvetteBonus[$i];
	}
	public static function getAFrigateBonus($i) {
		return self::$AFrigateBonus[$i];
	}
	public static function getADestroyerBonus($i) {
		return self::$ADestroyerBonus[$i];
	}
	
	public static function getDFighterBonus($i) {
		return self::$DFighterBonus[$i];
	}
	public static function getDCorvetteBonus($i) {
		return self::$DCorvetteBonus[$i];
	}
	public static function getDFrigateBonus($i) {
		return self::$DFrigateBonus[$i];
	}
	public static function getDDestroyerBonus($i) {
		return self::$DDestroyerBonus[$i];
	}
	
	
	
	// SETTER
	
	public function setPlayerBonus($ABonus, $DBonus) {
		if (isset($ABonus['global'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFighterBonus[$i] += $ABonus['global'][$i];
				self::$ACorvetteBonus[$i] += $ABonus['global'][$i];
				self::$AFrigateBonus[$i] += $ABonus['global'][$i];
				self::$ADestroyerBonus[$i] += $ABonus['global'][$i];
			}
		}
		
		if (isset($ABonus['fighter'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFighterBonus[$i] += $ABonus['fighter'][$i];
			}
		}
		
		if (isset($ABonus['corvette'])) {
			for($i = 0; $i < 4; $i++) {
				self::$ACorvetteBonus[$i] += $ABonus['corvette'][$i];
			}
		}
		
		if (isset($ABonus['frigate'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFrigateBonus[$i] += $ABonus['frigate'][$i];
			}
		}
		
		if (isset($ABonus['destroyer'])) {
			for($i = 0; $i < 4; $i++) {
				self::$ADestroyerBonus[$i] += $ABonus['destroyer'][$i];
			}
		}
		
		if (isset($DBonus['global'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFighterBonus[$i] += $DBonus['global'][$i];
				self::$DCorvetteBonus[$i] += $DBonus['global'][$i];
				self::$DFrigateBonus[$i] += $DBonus['global'][$i];
				self::$DDestroyerBonus[$i] += $DBonus['global'][$i];
			}
		}
		
		if (isset($DBonus['fighter'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFighterBonus[$i] += $DBonus['fighter'][$i];
			}
		}
		
		if (isset($DBonus['corvette'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DCorvetteBonus[$i] += $DBonus['corvette'][$i];
			}
		}
		
		if (isset($DBonus['frigate'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFrigateBonus[$i] += $DBonus['frigate'][$i];
			}
		}
		
		if (isset($DBonus['destroyer'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DDestroyerBonus[$i] += $DBonus['destroyer'][$i];
			}
		}
			
	}
	
	public function setEnvironmentBonus($ABonus, $DBonus) {
		if (isset($ABonus['global'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFighterBonus[$i] += $ABonus['global'][$i];
				self::$ACorvetteBonus[$i] += $ABonus['global'][$i];
				self::$AFrigateBonus[$i] += $ABonus['global'][$i];
				self::$ADestroyerBonus[$i] += $ABonus['global'][$i];
			}
		}
		
		if (isset($ABonus['fighter'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFighterBonus[$i] += $ABonus['fighter'][$i];
			}
		}
		
		if (isset($ABonus['corvette'])) {
			for($i = 0; $i < 4; $i++) {
				self::$ACorvetteBonus[$i] += $ABonus['corvette'][$i];
			}
		}
		
		if (isset($ABonus['frigate'])) {
			for($i = 0; $i < 4; $i++) {
				self::$AFrigateBonus[$i] += $ABonus['frigate'][$i];
			}
		}
		
		if (isset($ABonus['destroyer'])) {
			for($i = 0; $i < 4; $i++) {
				self::$ADestroyerBonus[$i] += $ABonus['destroyer'][$i];
			}
		}
		
		if (isset($DBonus['global'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFighterBonus[$i] += $DBonus['global'][$i];
				self::$DCorvetteBonus[$i] += $DBonus['global'][$i];
				self::$DFrigateBonus[$i] += $DBonus['global'][$i];
				self::$DDestroyerBonus[$i] += $DBonus['global'][$i];
			}
		}
		
		if (isset($DBonus['fighter'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFighterBonus[$i] += $DBonus['fighter'][$i];
			}
		}
		
		if (isset($DBonus['corvette'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DCorvetteBonus[$i] += $DBonus['corvette'][$i];
			}
		}
		
		if (isset($DBonus['frigate'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DFrigateBonus[$i] += $DBonus['frigate'][$i];
			}
		}
		
		if (isset($DBonus['destroyer'])) {
			for($i = 0; $i < 4; $i++) {
				self::$DDestroyerBonus[$i] += $DBonus['destroyer'][$i];
			}
		}
	}
	
	/**
	 * DEMARE LE COMBAT ENTRE DEUX COMMANDANT
	 *		COMPTE L'ARMEE D
	 *		si 0 vaisseaux
	 *			A gagne
	 *		SINON COMBAT
	 *		COMPTE L'ARMEE A
	 *		si 0 vaisseaux
	 *			D gagne
	 *		SINON COMBAT
	*/

	public function startFight($commanderA, $playerA, $commanderD, $playerD = NULL) {
		include_once DEMETER;

		$commanderA->setIsAttacker(TRUE);
		$commanderD->setIsAttacker(FALSE);
		
		$commanderA->setPevInBegin();
		$commanderD->setPevInBegin();

		LiveReport::$rPlayerAttacker = $commanderA->rPlayer;
		LiveReport::$rPlayerDefender = $commanderD->rPlayer;

		LiveReport::$avatarA = $commanderA->avatar;
		LiveReport::$avatarD = $commanderD->avatar;
		LiveReport::$nameA = $commanderA->name;
		LiveReport::$nameD = $commanderD->name;
		LiveReport::$levelA = $commanderA->level;
		LiveReport::$levelD = $commanderD->level;
		LiveReport::$experienceA = $commanderA->experience;
		LiveReport::$experienceD = $commanderD->experience;
		LiveReport::$palmaresA = $commanderA->palmares;
		LiveReport::$palmaresD = $commanderD->palmares;

		$i = 0;
		foreach ($commanderA->armyInBegin AS $s) {
			LiveReport::$squadrons[] = array(0, $i, 0, 0, $commanderA->id, $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11]);
			$i++;
		}
		$i = 0;
		foreach ($commanderD->armyInBegin AS $s) {
			LiveReport::$squadrons[] = array(0, $i, 0, 0, $commanderD->id, $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11]);
			$i++;
		}
		/* array (
		    'id' => 38,
		    'rCommander' => 31,
		    'ship0' => 0,
		    0 => 0,
		    'ship1' => 0,
		    1 => 0,
		    'ship2' => 0,
		    2 => 0,
		    'ship3' => 0,
		    3 => 0,
		    'ship4' => 0,
		    4 => 0,
		    'ship5' => 0,
		    5 => 0,
		    'ship6' => 0,
		    6 => 0,
		    'ship7' => 0,
		    7 => 0,
		    'ship8' => 0,
		    8 => 0,
		    'ship9' => 0,
		    9 => 0,
		    'ship10' => 0,
		    10 => 0,
		    'ship11' => 0,
		    11 => 0,
		    'dCreation' => '2014-05-10 10:29:35',
		    12 => '2014-05-10 10:29:35',
		    'dLastModification' => '2014-06-05 14:10:49',
		    13 => '2014-06-05 14:10:49'*/
	
		while(1) {
			if (LiveReport::$round == 1000) {
				break;
			}
			$nbrShipsD = 0;
			$nbrShipsA = 0;
			
			foreach ($commanderA->getArmy() as $squadronA) {
				$nbrShipsA += $squadronA->getNbrShips();
			}
			if ($nbrShipsA == 0) {
				$commanderD->resultOfFight(TRUE, $commanderA);
				$commanderA->resultOfFight(FALSE, $commanderD);
				$commanderA->setStatement(3);
				$commanderD->setDDeath(Utils::now());
				LiveReport::$rPlayerWinner = $commanderD->rPlayer;

				if ($commanderD->rPlayer != ID_GAIA) {
					$playerD->increaseVictory(1);
					$playerA->increaseDefeat(1);
				} else{
					$playerA->increaseDefeat(1);
				}

				break;
			} else {
				$commanderA = $commanderD->engage($commanderA, $commanderD);
				LiveReport::$halfround++;
			}
			
			foreach ($commanderD->getArmy() as $squadronD) {
				$nbrShipsD += $squadronD->getNbrShips();
			}
			if($nbrShipsD == 0) {
				$commanderA->resultOfFight(TRUE, $commanderD);
				$commanderD->resultOfFight(FALSE, $commanderA);
				$commanderD->setStatement(3);
				$commanderD->setDDeath(Utils::now());
				LiveReport::$rPlayerWinner = $commanderA->rPlayer;

				if ($commanderD->rPlayer != ID_GAIA) {
					$playerA->increaseVictory(1);
					$playerD->increaseDefeat(1);
				} else {
					$playerA->increaseVictory(1);
				}

				break;
			} else {
				$commanderD = $commanderA->engage($commanderD, $commanderA);
				LiveReport::$halfround++;
			}
			
			LiveReport::$round++;
			self::$currentLine++;
		}

		$i = 0;
		foreach ($commanderA->armyAtEnd AS $s) {
			LiveReport::$squadrons[] = array(0, $i, 0, -1, $commanderA->id, $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11]);
			$i++;
		}
		$i = 0;
		foreach ($commanderD->armyAtEnd AS $s) {
			LiveReport::$squadrons[] = array(0, $i, 0, -1, $commanderD->id, $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11]);
			$i++;
		}
		return array($commanderA, $commanderD);
	}
}
?>