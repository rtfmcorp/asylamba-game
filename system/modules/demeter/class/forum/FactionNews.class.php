<?php

/**
 * news de faction
 *
 * @author Noé Zufferey
 * @copyright Asylamba
 *
 * @package Demeter
 * @update 09.01.15
*/

class FactionNews {
	const STANDARD 		= 0;
	const PINNED 		= 1;

	public $id 				= 0;
	public $rFaction		= 0;
	public $title 			= 0;
	public $oContent 		= 0;
	public $pContent 		= 0;
	public $pinned 			= 0;
	public $statement 		= 0;
	public $dCreation		= '';

	public function getId() { return $this->id; }

	public function edit($content) {
		$this->oContent = $content;

		$p = new Parser();
		$p->parseBigTag = TRUE;
		$content = $p->parse($content);

		$this->pContent = $content;
	}
}