<?php

namespace App\Modules\Athena\Infrastructure\Twig;

use App\Classes\Library\Format;
use App\Classes\Library\Game;
use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Model\Transaction;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommercialShippingExtension extends AbstractExtension
{
	public function getFilters(): array
	{
		return [
			new TwigFilter('transaction_picto', fn (CommercialShipping $commercialShipping) => Transaction::getResourcesIcon($commercialShipping->quantity)),
		];
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_min_price', fn (string $transactionType, int $quantity, int $identifier = null) =>  Game::getMinPriceRelativeToRate($transactionType, 1, $identifier)),
			new TwigFunction('get_transaction_class', fn (CommercialShipping $commercialShipping) => match ($commercialShipping->typeOfTransaction) {
				Transaction::TYP_RESOURCE => 'resources',
				Transaction::TYP_COMMANDER => 'commander',
				Transaction::TYP_SHIP => 'ship',
			}),
			new TwigFunction('get_cancellation_price', fn (CommercialShipping $commercialShipping) => Format::number(floor($commercialShipping->price * Transaction::PERCENTAGE_TO_CANCEL / 100))),
		];
	}
}
