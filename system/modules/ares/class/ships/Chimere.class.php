<?php
class Chimere extends Fighter {
	protected $id = 0;
	protected $name = 'chasseur multi-tourelle';
	protected $codeName = 'Chimère';
	protected $nbrName = 3;
	protected $life = 26;
	protected $speed = 195;
	protected $attack = array(4, 4);
	protected $defense = 3;
	protected $nbrAttack = 0;
	protected $pev = 3;


	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>