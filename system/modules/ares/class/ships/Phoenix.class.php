<?php
class Phoenix extends Destroyer {
	protected $id = 0;
	protected $name = 'croiseur lourd';
	protected $codeName = 'Phoenix';
	protected $nbrName = 12;
	protected $life = 1300;
	protected $speed = 50;
	protected $attack = array(20, 20, 20, 20, 50, 50, 100, 100, 300);
	protected $defense = 150;
	protected $nbrAttack = 0;
	protected $pev = 100;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>