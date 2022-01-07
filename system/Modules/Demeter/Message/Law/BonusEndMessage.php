<?php

namespace Asylamba\Modules\Demeter\Message\Law;

class BonusEndMessage
{
	public function __construct(protected int $lawId)
	{

	}

	public function getLawId(): int
	{
		return $this->lawId;
	}
}
