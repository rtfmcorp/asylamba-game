<?php

use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');

# background paralax
echo '<div id="background-paralax" class="financial"></div>';

# inclusion des elements
include 'financialElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if (!$request->query->has('view') OR $request->query->get('view') == 'invest') {
		# loading des objets
		$S_PAM_FIN = $playerManager->getCurrentSession();
		$playerManager->newSession();
		$playerManager->load(array('id' => $session->get('playerId')));

		$S_OBM_FIN = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession();
		$orbitalBaseManager->load(
			array('rPlayer' => $session->get('playerId')),
			array('rPlace', 'ASC')
		);

		$S_COM_FIN = $commanderManager->getCurrentSession();
		$commanderManager->newSession();
		$commanderManager->load(
			array(
				'c.rPlayer' => $session->get('playerId'),
				'c.statement' => array(Commander::AFFECTED, Commander::MOVING)
			), 
			array('c.rBase', 'ASC')
		);

		$S_TRM1 = $transactionManager->getCurrentSession();
		$transactionManager->newSession();
		$transactionManager->load(array('rPlayer' => $session->get('playerId'), 'type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED));

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
		$financial_totalInvestUni += $playerManager->get(0)->iUniversity;

		# general array
		$ob_generalFinancial = array();
		$commander_generalFinancial = array();

		for ($i = 0; $i < $orbitalBaseManager->size(); $i++) {
			$ob_generalFinancial[] = $orbitalBaseManager->get($i);
			
			$thisTaxIn = Game::getTaxFromPopulation($orbitalBaseManager->get($i)->getPlanetPopulation(), $orbitalBaseManager->get($i)->typeOfBase);
			$thisTaxInBonus = $thisTaxIn * $taxBonus / 100;
			$financial_totalTaxIn += $thisTaxIn;
			$financial_totalTaxInBonus += $thisTaxInBonus;

			$financial_totalTaxOut += ($thisTaxIn + $thisTaxInBonus) * $orbitalBaseManager->get($i)->getTax() / 100;

			$financial_totalInvest += $orbitalBaseManager->get($i)->getISchool();
			$financial_totalInvest += $orbitalBaseManager->get($i)->getIAntiSpy();

			$financial_totalShipsFees += Game::getFleetCost($orbitalBaseManager->get($i)->shipStorage, FALSE);

			# TODO cout des trucs en vente

			$S_CRM1 = $commercialRouteManager->getCurrentSession();
			$commercialRouteManager->changeSession($orbitalBaseManager->get($i)->routeManager);
			for ($k = 0; $k < $commercialRouteManager->size(); $k++) { 
				if ($commercialRouteManager->get($k)->getStatement() == CommercialRoute::ACTIVE) {
					$financial_totalRouteIncome += $commercialRouteManager->get($k)->getIncome();
					$financial_totalRouteIncomeBonus += $commercialRouteManager->get($k)->getIncome() * $rcBonus / 100;
				}
			}
			$commercialRouteManager->changeSession($S_CRM1);
		}

		$commander_generalFinancial = [];
		for ($i = 0; $i < $commanderManager->size(); $i++) {
			$commander_generalFinancial[] = $commanderManager->get($i);
			$financial_totalFleetFees += $commanderManager->get($i)->getLevel() * Commander::LVLINCOMECOMMANDER;
			$financial_totalShipsFees += Game::getFleetCost($commanderManager->get($i)->getNbrShipByType());
		}

		$transaction_generalFinancial = [];
		for ($i = 0; $i < $transactionManager->size(); $i++) {
			$transaction = $transactionManager->get($i);
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
		$player_investFinancial = $playerManager->get(0);
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

		# close
		$transactionManager->changeSession($S_TRM1);
		$playerManager->changeSession($S_PAM_FIN);
		$orbitalBaseManager->changeSession($S_OBM_FIN);
		$commanderManager->changeSession($S_COM_FIN);
	} elseif ($request->query->get('view') == 'send') {
		include COMPONENT . 'financial/send-credit-player.php';
		include COMPONENT . 'financial/send-credit-faction.php';
		include COMPONENT . 'financial/last-send-credit.php';
		include COMPONENT . 'financial/last-receive-credit.php';
	}
echo '</div>';
