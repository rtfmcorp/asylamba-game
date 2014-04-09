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

	public function startFight($commanderA, $commanderD, $place) {
		$commanderA->setIsAttacker(TRUE);
		$commanderD->setIsAttacker(FALSE);
		
		if ($place->getTypeOfBase() == 4) {
			LiveReport::$placeName = $place->getBaseName();
		} else if ($place->getTypeOfBase() == 0 ) {
			LiveReport::$placeName = 'planète rebelle';
		} else {
			LiveReport::$placeName = 'vaisseau mère';
		}
		LiveReport::$type = $commanderA->getTypeOfMove();
		LiveReport::setCommanders(array($commanderA, $commanderD));
		LiveReport::setDefender($commanderD);
		LiveReport::setDFight($commanderA->getArrivalDate());
		LiveReport::setType($commanderA->getTypeOfMove());
		LiveReport::setRPlace($commanderA->getRPlaceDestination());
		$commanderA->setPevInBegin();
		$commanderD->setPevInBegin();
	
		while(1) {
			if (LiveReport::$round == 1000) {
				break;
			}
			$nbrShipsD = 0;
			$nbrShipsA = 0;
			
			foreach ($commanderD->getArmy() as $squadronD) {
				$nbrShipsD += $squadronD->getNbrShips();
			}
			if($nbrShipsD == 0) {
				$commanderA->resultOfFight(TRUE, $commanderD);
				$commanderD->resultOfFight(FALSE);
				$commanderD->setStatement(3);
				$commanderD->setDDeath(Utils::now());
				LiveReport::setWinner($commanderA->getRPlayer());

				if ($commanderD->getRPlayer() != 0) {
					include_once ZEUS;
					$oldPlayerSess = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession();
					ASM::$pam->load(array('id' => $commanderA->getRPlayer()));
					ASM::$pam->load(array('id' => $commanderD->getRPlayer()));
					ASM::$pam->get(0)->increaseVictory(1);
					ASM::$pam->get(1)->increaseDefeat(1);
					ASM::$pam->changeSession($oldPlayerSess);
				} else {
					include_once ZEUS;
					$oldPlayerSess = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession();
					ASM::$pam->load(array('id' => $commanderA->getRPlayer()));
					ASM::$pam->get()->increaseVictory(1);
				}

				break;
			} else {
				$commanderD = $commanderA->engage($commanderD, $commanderA);
				LiveReport::$halfround++;
			}
			
			foreach ($commanderA->getArmy() as $squadronA) {
				$nbrShipsA += $squadronA->getNbrShips();
			}
			if ($nbrShipsA == 0) {
				$commanderD->resultOfFight(TRUE, $commanderA);
				$commanderA->resultOfFight(FALSE);
				$commanderA->setStatement(3);
				$commanderD->setDDeath(Utils::now());
				LiveReport::setWinner($commanderD->getRPlayer());

				if ($commanderD->getRPlayer() != 0) {
					include_once ZEUS;
					$oldPlayerSess = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession();
					ASM::$pam->load(array('id' => $commanderA->getRPlayer()));
					ASM::$pam->load(array('id' => $commanderD->getRPlayer()));
					ASM::$pam->get(1)->increaseVictory(1);
					ASM::$pam->get(0)->increaseDefeat(1);
					ASM::$pam->changeSession($oldPlayerSess);
				} else{
					include_once ZEUS;
					$oldPlayerSess = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession();
					ASM::$pam->load(array('id' => $commanderA->getRPlayer()));
					ASM::$pam->get()->increaseDefeat(1);
				}

				break;
			} else {
				$commanderA = $commanderD->engage($commanderA, $commanderD);
				LiveReport::$halfround++;
			}
			LiveReport::$round++;
			self::$currentLine++;
		}
		LiveReport::setFinalCommanders(array($commanderA, $commanderD));
		LiveReport::setFinalArmies($commanderA->getArmyAtEnd(), $commanderD->getArmyAtEnd());

		return array($commanderA, $commanderD);
	}
}
?>