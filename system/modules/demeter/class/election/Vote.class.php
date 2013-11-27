<?php

/**
 * Message Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

class Vote {
	const PUBLISHED 		= 1;
	const HIDDEN 			= 2;
	const FORBIDDEN_FLOOD 	= 3;
	const FORBIDDEN_INSULT 	= 4;
	const FORBIDDEN_PR0N 	= 5;
	const FORBIDDEN_RACISM 	= 6;	

	public $id 					= 0;
	public $rPlayer 			= 0;
	public $rTopic				= 0;
	public $oContent 			= '';
	public $pContent 			= '';
	public $statement			= 0;
	public $dCreation			= '';
	public $dLastModification	= '';

	public $playerName			= '';
	public $playerColor			= '';
	public $playerAvatar		= '';

	public function getId() { return $this->id; }

	public function edit($content) {
		$this->oContent = $content;

		$p = new Parser();
		$p->parseBigTag = TRUE;
		$content = $p->parse($content);

		$this->pContent = $content;
	}
}