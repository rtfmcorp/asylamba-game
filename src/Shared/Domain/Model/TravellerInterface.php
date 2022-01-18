<?php

namespace App\Shared\Domain\Model;

interface TravellerInterface
{
	public function getDepartureDate(): string;

	public function getArrivalDate(): string;
}
