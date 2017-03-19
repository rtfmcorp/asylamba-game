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
namespace Asylamba\Modules\Athena\Model;

use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Utils;

class CommercialShipping {
	# statement
	const ST_WAITING = 0;		# pret au départ, statique
	const ST_GOING = 1;			# aller
	const ST_MOVING_BACK = 2;	# retour

	const WEDGE = 1000;	# soute

	# attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rBase = 0;
	public $rBaseDestination = 0;
	public $rTransaction = NULL;			# soit l'un
	public $resourceTransported = NULL;		# soit l'autre
	public $shipQuantity = 0;
	public $dDeparture = '';
	public $dArrival = '';
	public $statement = 0;

	public $baseRSystem;
	public $basePosition;
	public $baseXSystem;
	public $baseYSystem;

	public $destinationRSystem;
	public $destinationPosition;
	public $destinationXSystem;
	public $destinationYSystem;

	public $price;
	public $typeOfTransaction;
	public $quantity;
	public $identifier;
	public $commanderAvatar;
	public $commanderName;
	public $commanderLevel;
	public $commanderVictory;
	public $commanderExperience;

	public function getId() { return $this->id; }
}