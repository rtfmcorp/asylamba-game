<?php

/**
 * CommercialShipping
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 13.11.13
 */

class CommercialShipping {
	// statement
	const ST_WAITING = 0;		// pret au départ, statique
	const ST_GOING = 1;			// aller
	const ST_MOVING_BACK = 2;	// retour

	// attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rBase = 0;
	public $rBaseDestination = 0;
	public $rTransaction = NULL;			// soit l'un
	public $resourceTransported = NULL;		// soit l'autre
	public $shipQuantity = 0;
	public $dDeparture = '';
	public $dArrival = '';
	public $statement = 0;

	public function getId() { return $this->id; }
}