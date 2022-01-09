<?php

namespace App\Modules\Demeter\Message\Law;

class AllianceDeclarationResultMessage
{
	public function __construct(protected int $lawId)
	{

	}

	public function getLawId(): int
	{
		return $this->lawId;
	}
}
