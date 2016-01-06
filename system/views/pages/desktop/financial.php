<?php
# background paralax
echo '<div id="background-paralax" class="financial"></div>';

# inclusion des elements
include 'financialElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'invest') {
		# inclusion des modules
		include_once ATHENA;
		include_once ZEUS;
		include_once ARES;

		# loading des objets
		$S_PAM_FIN = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

		$S_OBM_FIN = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(
			array('rPlayer' => CTR::$data->get('playerId')),
			array('rPlace', 'ASC')
		);

		$S_COM_FIN = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(
			array(
				'c.rPlayer' => CTR::$data->get('playerId'),
				'c.statement' => array(COM_AFFECTED, COM_MOVING)
			), 
			array('c.rBase', 'ASC')
		);

		# global variable
		$taxBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::POPULATION_TAX);
		$rcBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME);
		
		$financial_credit = CTR::$data->get('playerInfo')->get('credit');

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
		$financial_totalInvestUni += ASM::$pam->get(0)->iUniversity;

		# general array
		$ob_generalFinancial = array();
		$commander_generalFinancial = array();

		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$ob_generalFinancial[] = ASM::$obm->get($i);
			
			$thisTaxIn = Game::getTaxFromPopulation(ASM::$obm->get($i)->getPlanetPopulation(), ASM::$obm->get($i)->typeOfBase);
			$thisTaxInBonus = $thisTaxIn * $taxBonus / 100;
			$financial_totalTaxIn += $thisTaxIn;
			$financial_totalTaxInBonus += $thisTaxInBonus;

			$financial_totalTaxOut += ($thisTaxIn + $thisTaxInBonus) * ASM::$obm->get($i)->getTax() / 100;

			$financial_totalInvest += ASM::$obm->get($i)->getISchool();
			$financial_totalInvest += ASM::$obm->get($i)->getIAntiSpy();

			$financial_totalShipsFees += Game::getFleetCost(ASM::$obm->get($i)->shipStorage, FALSE);

			# TODO cout des trucs en vente

			$S_CRM1 = ASM::$crm->getCurrentSession();
			ASM::$crm->changeSession(ASM::$obm->get($i)->routeManager);
			for ($k = 0; $k < ASM::$crm->size(); $k++) { 
				if (ASM::$crm->get($k)->getStatement() == CRM_ACTIVE) {
					$financial_totalRouteIncome += ASM::$crm->get($k)->getIncome();
					$financial_totalRouteIncomeBonus += ASM::$crm->get($k)->getIncome() * $rcBonus / 100;
				}
			}
			ASM::$crm->changeSession($S_CRM1);
		}

		for ($i = 0; $i < ASM::$com->size(); $i++) {
			$commander_generalFinancial[] = ASM::$com->get($i);
			$financial_totalFleetFees += ASM::$com->get($i)->getLevel() * COM_LVLINCOMECOMMANDER;
			$financial_totalShipsFees += Game::getFleetCost(ASM::$com->get($i)->getNbrShipByType());
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
		$player_investFinancial = ASM::$pam->get(0);
		include COMPONENT . 'financial/investFinancial.php';

		# taxOutFinancial component
		$ob_taxOutFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/taxOutFinancial.php';

		$commander_shipsFeesFinancial = $commander_generalFinancial;
		$ob_shipsFeesFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/shipFeesFinancial.php';

		# fleetFeesFinancial component
		$commander_fleetFeesFinancial = $commander_generalFinancial;
		$ob_fleetFeesFinancial = $ob_generalFinancial;
		include COMPONENT . 'financial/fleetFeesFinancial.php';

		# close
		ASM::$pam->changeSession($S_PAM_FIN);
		ASM::$obm->changeSession($S_OBM_FIN);
		ASM::$com->changeSession($S_COM_FIN);
	} elseif (CTR::$get->get('view') == 'send') {
		include COMPONENT . 'financial/send-credit-player.php';
		include COMPONENT . 'financial/send-credit-faction.php';
		include COMPONENT . 'financial/last-send-credit.php';
		include COMPONENT . 'financial/last-receive-credit.php';
	}
echo '</div>';
?>