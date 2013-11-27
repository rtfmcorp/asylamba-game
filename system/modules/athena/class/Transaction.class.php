<?php

/**
 * Transaction
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 19.11.13
 */

class Transaction {
	// statement
	const ST_PROPOSED = 0;		// transaction proposée
	const ST_COMPLETED = 1;		// transaction terminée
	// type
	const TYP_RESOURCE = 0;
	const TYP_SHIP = 1;
	const TYP_COMMANDER = 2;

	// attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rPlace = 0;
	public $type;		// 0 = resource, 1 = ship, 2 = commander
	public $quantity;
	public $shipId;		// if $type == TYP_COMMANDER only
	public $price = 0;
	public $commercialShipQuantity = 0;	// ship needed for the transport
	public $statement = 0;
	public $dPublication = '';

	public function getId() { return $this->id; }
}