<?php
class Dryade extends Corvette {
	protected $id = 0;
	protected $name = 'corvette lourde';
	protected $codeName = 'Dryade';
	protected $nbrName = 5;
	protected $life = 60;
	protected $speed = 160;
	protected $attack = array(30);
	protected $defense = 10;
	protected $nbrAttack = 0;
	protected $pev = 7;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>