<?php

/**
 * CommercialTax
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 05.03.14
 */

class CommercialTax {

	public $id = 0;
	public $faction = 0;
	public $relatedFaction = 0;
	public $exportTax;
	public $importTax;

	public function getId() { return $this->id; }
}