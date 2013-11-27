<?php
abstract class Corvette extends Ship {
	public function __construct($isAttacker) {
		$this->isAttacker = $isAttacker;
		if ($isAttacker == TRUE) {
			$this->life = $this->life * (FightController::getACorvetteBonus(0));
			$this->speed = ($this->speed) * (FightController::getACorvetteBonus(1));
			for($i = 0; $i < count($this->attack); $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getACorvetteBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getACorvetteBonus(3));
		} else {
			$this->life = ($this->life) * (FightController::getDCorvetteBonus(0));
			$this->speed = ($this->speed) * (FightController::getDCorvetteBonus(1));
			for($i = 0; $i < count($this->attack); $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getDCorvetteBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getDCorvetteBonus(3));
		}
		if ($this->defense < 1) {$this->defense = 1;}
		if ($this->speed < 1) {$this->speed = 1;}
	}
}
?>