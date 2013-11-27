<?php
class Pegase extends Fighter {
	protected $id = 0;
	protected $name = 'chasseur léger';
	protected $codeName = 'Pégase';
	protected $nbrName = 1;
	protected $life = 26;
	protected $speed = 200;
	protected $attack = array(5);
	protected $defense = 2;
	protected $nbrAttack = 0;
	protected $pev = 2;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>