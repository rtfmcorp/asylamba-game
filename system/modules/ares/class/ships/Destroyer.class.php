<?php
abstract class Destroyer extends Ship {
	public function __construct($isAttacker) {
		$this->isAttacker = $isAttacker;
		if ($isAttacker == TRUE) {
			$this->life = $this->life * (FightController::getADestroyerBonus(0));
			$this->speed = ($this->speed) * (FightController::getADestroyerBonus(1));
			for ($i = 0; $i < $this->nbrAttack; $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getADestroyerBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getADestroyerBonus(3));
		} else {
			$this->life = ($this->life) * (FightController::getDDestroyerBonus(0));
			$this->speed = ($this->speed) * (FightController::getDDestroyerBonus(1));
			for($i = 0; $i < count($this->attack); $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getDDestroyerBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getDDestroyerBonus(3));
		}
		if ($this->defense < 1) {$this->defense = 1;}
		if ($this->speed < 1) {$this->speed = 1;}
	}
}
?>