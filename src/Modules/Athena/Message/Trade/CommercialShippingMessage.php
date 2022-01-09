<?php

namespace App\Modules\Athena\Message\Trade;

class CommercialShippingMessage
{
	public function __construct(protected int $commercialShippingId)
	{

	}

	public function getCommercialShippingId(): int
	{
		return $this->commercialShippingId;
	}
}
