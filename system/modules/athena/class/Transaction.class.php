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
	const ST_CANCELED = 2;		// transaction annulée
	// type
	const TYP_RESOURCE = 0;
	const TYP_SHIP = 1;
	const TYP_COMMANDER = 2;

	// percentage to cancel an offer
	const PERCENTAGE_TO_CANCEL = 5;

	// attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rPlace = 0;
	public $type;			// 0 = resource, 1 = ship, 2 = commander
	public $quantity;
	public $identifier;		// if ($type == TYP_COMMANDER) --> rCommander
							// if ($type == TYP_SHIP) --> shipId
	public $price = 0;
	public $commercialShipQuantity = 0;	// ship needed for the transport
	public $statement = 0;
	public $dPublication = '';
	public $dValidation = ''; 	// date of acceptance or cancellation
	public $currentRate;		// 1000 resources = x credits
								// OR 100 PEV = x credits
								// OR (for commanders it doesn't work with a rate !)

	public function getId() { return $this->id; }

	public function getPriceToCancelOffer() {
		// 5% of the price
		return floor($this->price * self::PERCENTAGE_TO_CANCEL / 100);
	}
}