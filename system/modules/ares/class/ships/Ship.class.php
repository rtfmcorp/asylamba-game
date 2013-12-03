<?php
abstract class Ship {
	protected $id = 0;
	protected $name = '';
	protected $codeName = '';
	protected $nbrName = 0;
	protected $life = 0;
	protected $speed = 0;
	protected $attack = array();
	protected $defense = 0;
	protected $nbrAttack = 0;
	protected $pev = 0;
	protected $isAttacker;
	
	public function engage ($enemySquadron) {
		for ($i = 0; $i < $this->nbrAttack; $i++) {
			if ($enemySquadron->getNbrOfShips() == 0) {
				break;
			}
			$keyOfEnemyShip = $this->chooseEnemy($enemySquadron);
			if ($this->avoidance($enemySquadron->getShip($keyOfEnemyShip)) == 1) {
				$this->attack($keyOfEnemyShip, $i, $enemySquadron);
			}
		}
		return $enemySquadron;
	}
	
	protected function chooseEnemy($enemySquadron) {
		$aleaNbr = rand(0, $enemySquadron->getNbrOfShips() - 1);
		return $aleaNbr;
	}
		
	protected function attack($key, $i, $enemySquadron) {
		$damages = ceil(log(($this->attack[$i] / $enemySquadron->getShip($key)->getDefense()) + 1) * 4 * $this->attack[$i]);
		$enemySquadron->getShip($key)->receiveDamages($damages, $enemySquadron, $key);
	}
	
	protected function avoidance($enemyShip) {
		$avoidance = rand(0, $enemyShip->getSpeed());
		if ($avoidance > 80) {
			return 0;	
		} else {
			return 1;
		}
	}
	
	public function receiveDamages ($damages, $squadron, $key) {
		$this->life -= $damages;
		if($this->life <= 0) {
			$this->life = 0;
			$squadron->destructShip($key);
		}
	}
	
	public function affectId ($id) {
		$this->id = $id + 1;
	}
	
	//-----------------Getters------------------------
	
	public function getId()
	{ return $this->id;}
	public function getName()
	{ return $this->name;}
	public function getCodeName()
	{ return $this->codeName;}
	public function getNbrName()
	{ return $this->nbrName;}
	public function getLife()
	{ return $this->life;}
	public function getSpeed()
	{ return $this->speed;}
	public function getAttack()
	{ return $this->attack;}
	public function getDefense()
	{ return $this->defense;}
	public function getPev()
	{ return $this->pev;}
}
?>