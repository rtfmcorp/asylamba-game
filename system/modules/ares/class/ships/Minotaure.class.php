<?php
class Minotaure extends Destroyer {
	protected $id = 0;
	protected $name = 'destroyer';
	protected $codeName = 'Minotaure';
	protected $nbrName = 9;
	protected $life = 1000;
	protected $speed = 88;
	protected $attack = array(50, 50, 50, 30);
	protected $defense = 100;
	protected $nbrAttack = 0;
	protected $pev = 90;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>