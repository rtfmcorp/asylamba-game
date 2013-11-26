<?php
abstract class Frigate extends Ship {
	public function __construct($isAttacker) {
		$this->isAttacker = $isAttacker;
		if ($isAttacker == TRUE) {
			$this->life = $this->life * (FightController::getAFrigateBonus(0));
			$this->speed = ($this->speed) * (FightController::getAFrigateBonus(1));
			for ($i = 0; $i < $this->nbrAttack; $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getAFrigateBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getAFrigateBonus(3));
		} else {
			$this->life = ($this->life) * (FightController::getDFrigateBonus(0));
			$this->speed = ($this->speed) * (FightController::getDFrigateBonus(1));
			for($i = 0; $i < count($this->attack); $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getDFrigateBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getDFrigateBonus(3));
		}
		if ($this->defense < 1) {$this->defense = 1;}
		if ($this->speed < 1) {$this->speed = 1;}
	}
}
?>