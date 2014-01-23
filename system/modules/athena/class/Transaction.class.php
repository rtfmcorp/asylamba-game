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
	# statement
	const ST_PROPOSED = 0;		# transaction proposée
	const ST_COMPLETED = 1;		# transaction terminée
	const ST_CANCELED = 2;		# transaction annulée
	# type
	const TYP_RESOURCE = 0;
	const TYP_SHIP = 1;
	const TYP_COMMANDER = 2;

	# percentage of variation in rate (+ or - 30%)
	const PERCENTAGE_VARIATION = 30;
	# percentage to cancel an offer
	const PERCENTAGE_TO_CANCEL = 5;
	# divide price by this constant to find the experience
	const EXPERIENCE_DIVIDER = 15000;

	# attributes
	# x system
	# y system
	public $id = 0; 
	public $rPlayer = 0;
	public $rPlace = 0;
	public $type;			# see const TYP_*
	public $quantity;		# if ($type == TYP_RESOURCE) --> resource
							# if ($type == TYP_SHIP) --> ship quantity
							# if ($type == TYP_COMMANDER) --> experience
	public $identifier;		# if ($type == TYP_RESOURCE) --> NULL
							# if ($type == TYP_SHIP) --> shipId
							# if ($type == TYP_COMMANDER) --> rCommander
	public $price = 0;
	public $commercialShipQuantity = 0;	# ship needed for the transport
	public $statement = 0;
	public $dPublication = '';
	public $dValidation = NULL; 	# date of acceptance or cancellation
	public $currentRate;	# 1 credit = 	x resources
							#				x pev
							#				x experience

	public function getId() { return $this->id; }

	public function getPriceToCancelOffer() {
		# 5% of the price
		return floor($this->price * self::PERCENTAGE_TO_CANCEL / 100);
	}

	public function getExperienceEarned() {
		return 1 + round($this->price / self::EXPERIENCE_DIVIDER);
	}
}