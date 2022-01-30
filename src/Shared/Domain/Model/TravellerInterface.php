<?php

namespace App\Shared\Domain\Model;

interface TravellerInterface
{
	public function isMoving(): bool;

	public function getDepartureDate(): string;

	public function getArrivalDate(): string;
}
