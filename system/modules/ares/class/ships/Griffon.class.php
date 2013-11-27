<?php
class Griffon extends Frigate {
	protected $id = 0;
	protected $name = 'frégate d\'attaque';
	protected $codeName = 'Griffon';
	protected $nbrName = 7;
	protected $life = 300;
	protected $speed = 110;
	protected $attack = array(20, 20, 20, 20);
	protected $defense = 50;
	protected $nbrAttack = 0;
	protected $pev = 25;

	public function __construct() {
		$this->nbrAttack = count($this->attack);
	}
}	
?>