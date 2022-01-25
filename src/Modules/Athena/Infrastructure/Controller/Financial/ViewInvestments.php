<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Classes\Library\Game;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\TransactionManager;
use App\Modules\Athena\Model\CommercialRoute;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Model\Transaction;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Model\PlayerBonus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewInvestments extends AbstractController
{
	public function __invoke(
		Player $currentPlayer,
		Request $request,
		CommanderManager $commanderManager,
		CommercialRouteManager $commercialRouteManager,
		PlayerManager $playerManager,
		OrbitalBaseManager $orbitalBaseManager,
		TransactionManager $transactionManager,
	): Response {
		$session = $request->getSession();
		$taxCoeff = $this->getParameter('zeus.player.tax_coeff');

		$playerBases = $orbitalBaseManager->getPlayerBases($currentPlayer->getId());

		$commanders = $commanderManager->getPlayerCommanders($currentPlayer->getId(), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'ASC']);

		$transactions = $transactionManager->getPlayerPropositions($currentPlayer->getId(), Transaction::TYP_SHIP);

		# global variable
		$taxBonus = $session->get('playerBonus')->get(PlayerBonus::POPULATION_TAX);
		$tradeRoutesIncomeBonus = $session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME);

		$basesData = $this->getBasesData($commercialRouteManager, $playerBases, $taxCoeff);

		return $this->render('pages/athena/financial/investments.html.twig', [
			'commanders' => $commanders,
			'commanders_by_base' => array_reduce($commanders, function ($carry, Commander $commander) {
				if (!isset($carry[$commander->getRBase()])) {
					$carry[$commander->getRBase()] = [];
				}
				$carry[$commander->getRBase()][] = $commander;

				return $carry;
			}, []),
			'player_bases' => $playerBases,
			'tax_coeff' => $taxCoeff,
			'tax_bonus' => $taxBonus,
			'transactions' => $transactions,
			'trade_routes_income_bonus' => $tradeRoutesIncomeBonus,
			'bases_data' => $basesData,
			'investments_data' => $this->getInvestmentsData(
				$currentPlayer,
				$playerBases,
				$commanders,
				$transactions,
				$basesData,
				$taxBonus,
				$taxCoeff,
				$tradeRoutesIncomeBonus,
			),
		]);
	}

	private function getBasesData(CommercialRouteManager $commercialRouteManager, array $bases, int $taxCoeff): array
	{
		return array_reduce($bases, function (array $carry, OrbitalBase $base) use ($commercialRouteManager, $taxCoeff) {
			$carry[$base->getId()] = [
				'tax_income' => Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff),
				'routes' => array_merge(
					$commercialRouteManager->getByBase($base->getId()),
					$commercialRouteManager->getByDistantBase($base->getId())
				),
				'routes_count' => $commercialRouteManager->countBaseActiveRoutes($base->getId()),
				'routes_income' => $commercialRouteManager->getBaseIncome($base)
			];
			return $carry;
		}, []);
	}
	
	private function getInvestmentsData(
		Player $player,
		array $playerBases,
		array $commanders,
		array $transactions,
		array $basesData,
		int $taxBonus,
		int $taxCoeff,
		int $tradeRoutesIncomeBonus
	): array {
		$data = [
		    'totalTaxIn' => 0,
		    'totalTaxInBonus' => 0,
		    'totalRouteIncome' => 0,
		    'totalRouteIncomeBonus' => 0,
		    'totalInvest' => 0,
		    'totalInvestUni' => 0,
		    'totalFleetFees' => 0,
		    'totalShipsFees' => 0,
		    'totalTaxOut' => 0,
		    'totalMSFees' => 0,
		];

		foreach ($playerBases as $base) {
			$taxIn = Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff);
			$taxInBonus = $taxIn * $taxBonus / 100;
			$data['totalTaxIn'] += $taxIn;
			$data['totalTaxInBonus'] += $taxInBonus;
			$data['totalTaxOut'] += ($taxIn + $taxInBonus) * $base->getTax() / 100;
			$data['totalInvestUni'] += $player->iUniversity;
			$data['totalInvest'] += $base->getISchool() + $base->getIAntiSpy();
			$data['totalShipsFees'] += Game::getFleetCost($base->shipStorage, FALSE);

			// @TODO cout des trucs en vente

			foreach ($basesData[$base->getId()]['routes'] as $route) {
				if ($route->getStatement() == CommercialRoute::ACTIVE) {
					$data['totalRouteIncome'] += $route->getIncome();
					$data['totalRouteIncomeBonus'] += $route->getIncome() * $tradeRoutesIncomeBonus / 100;
				}
			}
		}

		foreach ($commanders as $commander) {
			$data['totalFleetFees'] += $commander->getLevel() * Commander::LVLINCOMECOMMANDER;
			$data['totalShipsFees'] += Game::getFleetCost($commander->getNbrShipByType());
		}

		foreach ($transactions as $transaction) {
			$data['totalShipsFees'] += ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity;
		}

		$data['total_income'] = $data['totalTaxIn'] + $data['totalTaxInBonus'] + $data['totalRouteIncome'] + $data['totalRouteIncomeBonus'];
		$data['total_expenses'] = $data['totalInvest'] + $data['totalInvestUni'] + $data['totalTaxOut'] + $data['totalMSFees'] + $data['totalFleetFees'] + $data['totalShipsFees'];

		$data['gains'] = $data['total_income'] - $data['total_expenses'];
		$data['remains']  = round($player->getCredit()) + round($data['gains']);

		return $data;
	}
}
