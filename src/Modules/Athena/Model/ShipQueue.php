<?php

/**
 * ShipQueue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/
namespace App\Modules\Athena\Model;

use App\Shared\Domain\Model\QueueableInterface;

class ShipQueue implements QueueableInterface
{
	// ATTRIBUTES
	public $id;
	public $rOrbitalBase;
	public $dockType = 0;
	public $shipNumber	= 0;
	public $quantity = 1;
	public $dStart;
	public $dEnd;

	public function getId() { 
		return $this->id; 
	}

	public function getEndDate(): string
	{
		return $this->dEnd;
	}

	public function getResourceIdentifier(): int
	{
		return $this->shipNumber;
	}
}
