<?php
class Cyclope extends Frigate {
	protected $id = 0;
	protected $name = 'frégate ionique';
	protected $codeName = 'Cyclope';
	protected $nbrName = 8;
	protected $life = 320;
	protected $speed = 90;
	protected $attack = array(600);
	protected $defense = 40;
	protected $nbrAttack = 0;
	protected $pev = 45;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>