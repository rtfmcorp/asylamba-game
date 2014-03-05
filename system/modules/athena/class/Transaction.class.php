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

	# percentage to cancel an offer
	const PERCENTAGE_TO_CANCEL = 5;
	# divide price by this constant to find the experience
	const EXPERIENCE_DIVIDER = 15000;

	# minimum rates for each type
	const MIN_RATE_RESOURCE = 1;
	const MIN_RATE_SHIP = 1;
	const MIN_RATE_COMMANDER = 1;

	# attributes
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
	public $currentRate;	# 1 resource = x credits (for resources et ships)
							# 1 experience = x credits

	# additionnal attributes
	public $playerName;
	public $playerColor;
	public $placeName;
	public $sector;
	public $sectorColor;
	public $rSystem;
	public $positionInSystem;
	public $xSystem;
	public $ySystem;

	public function getId() { return $this->id; }

	public function getPriceToCancelOffer() {
		# 5% of the price
		return floor($this->price * self::PERCENTAGE_TO_CANCEL / 100);
	}

	public function getExperienceEarned() {
		return 1 + round($this->price / self::EXPERIENCE_DIVIDER);
	}

	public static function getResourcesIcon($quantity) {
		if (1000000 <= $quantity && $quantity < 5000000) {
			return 5;
		} elseif (500000 <= $quantity && $quantity < 1000000) {
			return 4;
		} elseif (100000 <= $quantity && $quantity < 500000) {
			return 3;
		} elseif (10000 <= $quantity && $quantity < 100000) {
			return 2;
		} else {
			return 1;
		}	
	}
}