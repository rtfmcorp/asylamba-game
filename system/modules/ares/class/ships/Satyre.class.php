<?php
class Satyre extends Fighter {
	protected $id = 0;
	protected $name = 'chasseur lourd';
	protected $codeName = 'Satyre';
	protected $nbrName = 2;
	protected $life = 32;
	protected $speed = 195;
	protected $attack = array(6);
	protected $defense = 5;
	protected $nbrAttack = 0;
	protected $pev = 3;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>