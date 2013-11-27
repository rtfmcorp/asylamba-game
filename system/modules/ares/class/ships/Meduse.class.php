<?php
class Meduse extends Corvette {
	protected $id = 0;
	protected $name = 'corvette multi-tourelle';
	protected $codeName = 'Méduse';
	protected $nbrName = 6;
	protected $life = 75;
	protected $speed = 170;
	protected $attack = array(15, 8, 8);
	protected $defense = 8;
	protected $nbrAttack = 0;
	protected $pev = 10;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>