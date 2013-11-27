<?php

/**
 * MessageRadio
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 07.02.13
*/

class MessageRadio {
	// ATTRIBUTES
	public $id 			 = 0;
	public $rPlayer 	 = 0;
	public $rSystem 	 = 0;
	public $oContent 	 = '';
	public $pContent 	 = '';
	public $dCreation	 = '';
	public $statement	 = 1;

	public $playerName	 = '';
	public $playerColor	 = 0;
	public $playerAvatar = '';

	public function getId() { return $this->id; }

	// CONSTRUCTOR
	public function __construct() {
		$this->dCreation = Lib::now();
	}

	public function edit($content) {
		$this->oContent = $content;

		$parser = new Parser();
		$content = $parser->parse($content);
		
		$this->pContent = $content;
	}

	public function valid() { $this->statement = 1; }
	public function unValid() { $this->statement = 0; }

	public function hide() { $this->statement = 2; }
	public function show() { $this->statement = 1; }
}
?>