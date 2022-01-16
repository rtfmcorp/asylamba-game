<?php

namespace App\Shared\Domain\Model;

interface QueueableInterface
{
	public function getEndDate(): string;

	public function getResourceIdentifier(): int;
}
