<?php
abstract class Fighter extends Ship {
	public function __construct($isAttacker) {
		$this->isAttacker = $isAttacker;
		if ($isAttacker == TRUE) {
			$this->life = $this->life * (FightController::getAFighterBonus(0));
			$this->speed = ($this->speed) * (FightController::getAFighterBonus(1));
			for ($i = 0; $i < $this->nbrAttack; $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getAFighterBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getAFighterBonus(3));
		} else {
			$this->life = ($this->life) * (FightController::getDFighterBonus(0));
			$this->speed = ($this->speed) * (FightController::getDFighterBonus(1));
			for($i = 0; $i < count($this->attack); $i++) {
				$this->attack[$i] = ($this->attack[$i]) * (FightController::getDFighterBonus(2));
			}
			$this->defense = ($this->defense) * (FightController::getDFighterBonus(3));
		}
		if ($this->defense < 1) {$this->defense = 1;}
		if ($this->speed < 1) {$this->speed = 1;}
	}
}
?>