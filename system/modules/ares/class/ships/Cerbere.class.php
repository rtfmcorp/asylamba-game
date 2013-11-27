<?php
class Cerbere extends Destroyer {
	protected $id = 0;
	protected $name = 'croiseur';
	protected $codeName = 'Cerbère';
	protected $nbrName = 11;
	protected $life = 1220;
	protected $speed = 70;
	protected $attack = array(30, 30, 30, 30, 100, 250);
	protected $defense = 120;
	protected $nbrAttack = 0;
	protected $pev = 100;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>