<?php

namespace App\Modules\Demeter\Message\Law;

class SectorNameResultMessage
{
	public function __construct(protected int $lawId)
	{

	}

	public function getLawId(): int
	{
		return $this->lawId;
	}
}
