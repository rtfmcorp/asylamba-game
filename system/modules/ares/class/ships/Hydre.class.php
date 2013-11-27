<?php
class Hydre extends Destroyer {
	protected $id = 0;
	protected $name = 'destroyer missiles';
	protected $codeName = 'Hydre';
	protected $nbrName = 10;
	protected $life = 1200;
	protected $speed = 80;
	protected $attack = array(25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25);
	protected $defense = 100;
	protected $nbrAttack = 0;
	protected $pev = 92;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>