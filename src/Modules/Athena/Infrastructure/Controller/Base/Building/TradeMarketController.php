<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\TransactionManager;
use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Model\Transaction;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TradeMarketController extends AbstractController
{
	public function __invoke(
		CommanderManager $commanderManager,
		OrbitalBase $currentBase,
		OrbitalBaseHelper $orbitalBaseHelper,
		TransactionManager $transactionManager,
		string $mode,
	): Response {
		$usedShips = 0;

		return $this->render('pages/athena/trade_market.html.twig', [
			'mode' => $mode,
			'used_ships' => $usedShips,
			'max_ships' => $orbitalBaseHelper->getInfo(
				OrbitalBaseResource::COMMERCIAL_PLATEFORME,
				'level',
				$currentBase->getLevelCommercialPlateforme(),
				'nbCommercialShip',
			),
			'resources_current_rate' => $transactionManager->getLastCompletedTransaction(Transaction::TYP_RESOURCE)->currentRate,
			'resource_transactions' => $transactionManager->getProposedTransactions(Transaction::TYP_RESOURCE),
			'commander_current_rate' => $transactionManager->getLastCompletedTransaction(Transaction::TYP_COMMANDER)->currentRate,
			'commander_transactions' => $transactionManager->getProposedTransactions(Transaction::TYP_COMMANDER),
			'ship_current_rate' => $transactionManager->getLastCompletedTransaction(Transaction::TYP_SHIP)->currentRate,
			'ship_transactions' => $transactionManager->getProposedTransactions(Transaction::TYP_SHIP),
			'commercial_shippings' => $this->getCommercialShippingsData($currentBase, $usedShips),
			'base_commanders' => $commanderManager->getBaseCommanders(
				$currentBase->getId(),
				[Commander::INSCHOOL, Commander::RESERVE],
				['c.experience' => 'DESC'],
			),
		]);
	}

	protected function getCommercialShippingsData(OrbitalBase $currentBase, int &$usedShips): array
	{
		$commercialShippings = [
			'incoming' => [
				CommercialShipping::ST_WAITING => [],
				CommercialShipping::ST_GOING => [],
				CommercialShipping::ST_MOVING_BACK => [],
			],
			'outgoing' => [
				CommercialShipping::ST_WAITING => [],
				CommercialShipping::ST_GOING => [],
				CommercialShipping::ST_MOVING_BACK => [],
			]
		];
		/** @var CommercialShipping $commercialShipping */
		foreach ($currentBase->commercialShippings as $commercialShipping) {
			if ($commercialShipping->rBase === $currentBase->getId()) {
				$usedShips += $commercialShipping->shipQuantity;
				$commercialShippings['outgoing'][$commercialShipping->statement] = $commercialShipping;
			}
			if ($commercialShipping->rBaseDestination === $currentBase->getId()) {
				$commercialShippings['incoming'][$commercialShipping->statement] = $commercialShipping;
			}
		}
		return $commercialShippings;
	}
}
