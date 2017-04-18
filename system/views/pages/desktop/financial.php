<?php

use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$taxCoeff = $this->getContainer()->getParameter('zeus.player.tax_coeff');
$entityManager = $this->getContainer()->get('entity_manager');

# background paralax
echo '<div id="background-paralax" class="financial"></div>';

# inclusion des elements
include 'financialElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if (!$request->query->has('view') OR $request->query->get('view') == 'invest') {
		$player = $playerManager->get($session->get('playerId'));
		
		$playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));

		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'ASC']);

		$transactions = $transactionManager->getPlayerPropositions($session->get('playerId'), Transaction::TYP_SHIP);

		# global variable
		$taxBonus = $session->get('playerBonus')->get(PlayerBonus::POPULATION_TAX);
		$rcBonus = $session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME);
		
		$financial_credit = $session->get('playerInfo')->get('credit');

		$financial_totalTaxIn = 0;
		$financial_totalTaxInBonus = 0;
		$financial_totalRouteIncome = 0;
		$financial_totalRouteIncomeBonus = 0;

		$financial_totalInvest = 0;
		$financial_totalInvestUni = 0;
		$financial_totalFleetFees = 0;
		$financial_totalShipsFees = 0;
		$financial_totalTaxOut = 0;
		$financial_totalMSFees = 0;

		# bonus
		$financial_totalInvestUni += $player->iUniversity;

		# general array
		$ob_generalFinancial = array();
		$commander_generalFinancial = array();

		foreach ($playerBases as $orbitalBase) {
			$ob_generalFinancial[] = $orbitalBase;
			
			$thisTaxIn = Game::getTaxFromPopulation($orbitalBase->getPlanetPopulation(), $orbitalBase->typeOfBase, $taxCoeff);
			$thisTaxInBonus = $thisTaxIn * $taxBonus / 100;
			$financial_totalTaxIn += $thisTaxIn;
			$financial_totalTaxInBonus += $thisTaxInBonus;

			$financial_totalTaxOut += ($thisTaxIn + $thisTaxInBonus) * $orbitalBase->getTax() / 100;

			$financial_totalInvest += $orbitalBase->getISchool();
			$financial_totalInvest += $orbitalBase->getIAntiSpy();

			$financial_totalShipsFees += Game::getFleetCost($orbitalBase->shipStorage, FALSE);

			# TODO cout des trucs en vente

			$routes = array_merge(
				$commercialRouteManager->getByBase($orbitalBase->getId()),
				$commercialRouteManager->getByDistantBase($orbitalBase->getId())
			);
			foreach ($routes as $route) { 
				if ($route->getStatement() == CommercialRoute::ACTIVE) {
					$financial_totalRouteIncome += $route->getIncome();
					$financial_totalRouteIncomeBonus += $route->getIncome() * $rcBonus / 100;
				}
			}
			$entityManager->clear(CommercialRoute::class);
		}

		$commander_generalFinancial = [];
		foreach ($commanders as $commander) {
			$commander_generalFinancial[] = $commander;
			$financial_totalFleetFees += $commander->getLevel() * Commander::LVLINCOMECOMMANDER;
			$financial_totalShipsFees += Game::getFleetCost($commander->getNbrShipByType());
		}

        $transaction_generalFinancial = [];
		foreach ($transactions as $transaction) {
			$transaction_generalFinancial[] = $transaction;
			$financial_totalShipsFees += ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity;
		}

		$financial_totalIncome = $financial_totalTaxIn + $financial_totalTaxInBonus + $financial_totalRouteIncome + $financial_totalRouteIncomeBonus;
		$financial_totalFess = $financial_totalInvest + $financial_totalInvestUni + $financial_totalTaxOut + $financial_totalMSFees + $financial_totalFleetFees + $financial_totalShipsFees;

		$financial_benefice = $financial_totalIncome - $financial_totalFess;
		$financial_remains  = round($financial_credit) + round($financial_benefice);

		# generalFinancial component
		include COMPONENT . 'financial/generalFinancial.php';

		# impositionFinancial component
		$ob_impositionFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/impositionFinancial.php';

		# routeFinancial component
		$ob_routeFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/routeFinancial.php';

		# investFinancial component
		$ob_investFinancial = $ob_generalFinancial;
		$player_investFinancial = $player;
		include COMPONENT . 'financial/investFinancial.php';

		# taxOutFinancial component
		$ob_taxOutFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/taxOutFinancial.php';

		# shipsFeesFinancial component
		$commander_shipsFeesFinancial = $commander_generalFinancial;
		$transaction_shipsFeesFinancial = $transaction_generalFinancial;
		$ob_shipsFeesFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/shipFeesFinancial.php';

		# fleetFeesFinancial component
		$commander_fleetFeesFinancial = $commander_generalFinancial;
		$ob_fleetFeesFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/fleetFeesFinancial.php';
	} elseif ($request->query->get('view') == 'send') {
		include COMPONENT . 'financial/send-credit-player.php';
		include COMPONENT . 'financial/send-credit-faction.php';
		include COMPONENT . 'financial/last-send-credit.php';
		include COMPONENT . 'financial/last-receive-credit.php';
	}
echo '</div>';
