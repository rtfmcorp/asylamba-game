<?php

namespace Asylamba\Modules\Atlas\Routine;

use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Classes\Library\Game;

use Asylamba\Modules\Athena\Resource\ShipResource;

use Asylamba\Classes\Library\DataAnalysis;

use Asylamba\Modules\Atlas\Manager\PlayerRankingManager;
use Asylamba\Modules\Atlas\Repository\PlayerRankingRepository;

use Asylamba\Modules\Atlas\Model\Ranking;
use Asylamba\Modules\Atlas\Model\PlayerRanking;

class PlayerRoutine extends AbstractRoutine
{
	/** @var array **/
	protected $results;
	
	const COEF_RESOURCE = 0.001;
	
	public function execute(
		$players,
		\PDOStatement $resourcesStatement,
		\PDOStatement $resourcesDataStatement,
		\PDOStatement $generalStatement,
		\PDOStatement $armiesStatement,
		\PDOStatement $planetStatement,
		\PDOStatement $tradeRoutesStatement,
		\PDOStatement $linkedTradeRoutesStatement,
		\PDOStatement $attackersStatement,
		\PDOStatement $defendersStatement,
		OrbitalBaseHelper $orbitalBaseHelper
	)
	{
		$this->results = [];
		# create an array with all the players
		foreach ($players as $player) {
			$this->results[$player->id] = [
				'general' => 0, 
				'resources' => 0,
				'experience' => 0, 
				'victory' => 0,
				'defeat' => 0,
				'fight' => 0,
				'armies' => 0,
				'butcher' => 0,
				'butcherDestroyedPEV' => 0,
				'butcherLostPEV' => 0,
				'trader' => 0,

				'DA_Resources' => 0,
				'DA_PlanetNumber' => 0
			];
		}
		$this->calculateResources($resourcesStatement, $orbitalBaseHelper);
		$this->calculateDataResources($resourcesDataStatement);
		$this->calculatePlanetRanking($planetStatement);
		$this->calculateGeneralRanking($generalStatement);
		$this->calculateArmiesRanking($armiesStatement);
		$this->calculateTradeRanking($tradeRoutesStatement, $linkedTradeRoutesStatement);
		$this->calculateButcherRanking($attackersStatement, $defendersStatement);
	}
	
	public function processResults(Ranking $ranking, $players, PlayerRankingManager $playerRankingManager, PlayerRankingRepository $playerRankingRepository)
	{
		foreach ($players as $playerId) {
			if (isset($this->results[$playerId->id])) {
				# add the points to the list
				$this->results[$playerId->id]['experience'] += $playerId->experience;
				$this->results[$playerId->id]['victory'] += $playerId->victory;
				$this->results[$playerId->id]['defeat'] += $playerId->defeat;
				$this->results[$playerId->id]['fight'] += $playerId->victory - $playerId->defeat;
			}
		}

		# copy the arrays
		$listG = $this->results;
		$listR = $this->results;
		$listE = $this->results;
		$listF = $this->results;
		$listA = $this->results;
		$listB = $this->results;
		$listT = $this->results;

		# sort all the copies
		uasort($listG, [$this, 'cmpGeneral']);
		uasort($listR, [$this, 'cmpResources']);
		uasort($listE, [$this, 'cmpExperience']);
		uasort($listF, [$this, 'cmpFight']);
		uasort($listA, [$this, 'cmpArmies']);
		uasort($listB, [$this, 'cmpButcher']);
		uasort($listT, [$this, 'cmpTrader']);

		/*foreach ($list as $key => $value) {
			echo $key . ' => ' . $value['general'] . '<br/>';
		}*/

		# put the position in each array
		$position = 1;
		foreach ($listG as $key => $value) { $listG[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listR as $key => $value) { $listR[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listE as $key => $value) { $listE[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listF as $key => $value) { $listF[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listA as $key => $value) { $listA[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listB as $key => $value) { $listB[$key]['position'] = $position++;}
		$position = 1;
		foreach ($listT as $key => $value) { $listT[$key]['position'] = $position++;}

		foreach ($players as $player) {
			$playerId = $player->getId();
			
			$pr = new PlayerRanking();
			$pr->rRanking = $ranking->getId();
			$pr->rPlayer = $playerId; 

			# voir s'il faut améliorer (p.ex. : stocker le tableau des objets et supprimer chaque objet utilisé pour que la liste se rapetisse)
			$firstRanking = true;
			for ($i = 0; $i < $playerRankingManager->size(); $i++) {
				if ($playerRankingManager->get($i)->rPlayer == $playerId) {
					$firstRanking = false;
					$oldRanking = $playerRankingManager->get($i);
					break;
				}
			}

			$pr->general = $listG[$playerId]['general'];
			$pr->generalPosition = $listG[$playerId]['position'];
			$pr->generalVariation = $firstRanking ? 0 : $oldRanking->generalPosition - $pr->generalPosition;
			$player->factionPoint = $pr->general;

			$pr->resources = $listR[$playerId]['resources'];
			$pr->resourcesPosition = $listR[$playerId]['position'];
			$pr->resourcesVariation = $firstRanking ? 0 : $oldRanking->resourcesPosition - $pr->resourcesPosition;

			$pr->experience = $listE[$playerId]['experience'];
			$pr->experiencePosition = $listE[$playerId]['position'];
			$pr->experienceVariation = $firstRanking ? 0 : $oldRanking->experiencePosition - $pr->experiencePosition;

			$pr->fight = ($listF[$playerId]['fight'] >= 0) ? $listF[$playerId]['fight'] : 0;
			$pr->victories = $listF[$playerId]['victory'];
			$pr->defeat = $listF[$playerId]['defeat'];
			$pr->fightPosition = $listF[$playerId]['position'];
			$pr->fightVariation = $firstRanking ? 0 : $oldRanking->fightPosition - $pr->fightPosition;

			$pr->armies = $listA[$playerId]['armies'];
			$pr->armiesPosition = $listA[$playerId]['position'];
			$pr->armiesVariation = $firstRanking ? 0 : $oldRanking->armiesPosition - $pr->armiesPosition;

			$pr->butcher = ($listB[$playerId]['butcher'] >= 0) ? $listB[$playerId]['butcher'] : 0;
			$pr->butcherDestroyedPEV = $listB[$playerId]['butcherDestroyedPEV'];
			$pr->butcherLostPEV = $listB[$playerId]['butcherLostPEV'];
			$pr->butcherPosition = $listB[$playerId]['position'];
			$pr->butcherVariation = $firstRanking ? 0 : $oldRanking->butcherPosition - $pr->butcherPosition;

			$pr->trader = $listT[$playerId]['trader'];
			$pr->traderPosition = $listT[$playerId]['position'];
			$pr->traderVariation = $firstRanking ? 0 : $oldRanking->traderPosition - $pr->traderPosition;

			$playerRankingManager->add($pr);

			if (DATA_ANALYSIS) {
				$playerRankingRepository->insertDataAnalysis(
					$player,
					$pr,
					$this->results[$player]['DA_Resources'],
					$this->results[$player]['DA_PlanetNumber']
				);
			}
		}
	}
	
	protected function calculateResources(\PDOStatement $statement, OrbitalBaseHelper $orbitalBaseHelper)
	{
		while ($row = $statement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$resourcesProd = Game::resourceProduction($orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $row['levelRefinery'], 'refiningCoefficient'), $row['coefResources']);
				$this->results[$row['player']]['resources'] += $resourcesProd;
			}
		}
		$statement->closeCursor();
	}
	
	protected function calculateDataResources(\PDOStatement $statement)
	{
		while ($row = $statement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['DA_Resources'] += DataAnalysis::resourceToStdUnit($row['sumResources']);
			}
		}
		$statement->closeCursor();
	}
	
	protected function calculatePlanetRanking(\PDOStatement $statement)
	{
		while ($row = $statement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['DA_PlanetNumber'] += $row['sumPlanets'];
			}
		}
		$statement->closeCursor();
	}
	
	protected function calculateGeneralRanking(\PDOStatement $statement)
	{
		while ($row = $statement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$shipPrice = 0;
				$shipPrice += ShipResource::getInfo(0, 'resourcePrice') * $row['s0'];
				$shipPrice += ShipResource::getInfo(1, 'resourcePrice') * $row['s1'];
				$shipPrice += ShipResource::getInfo(2, 'resourcePrice') * $row['s2'];
				$shipPrice += ShipResource::getInfo(3, 'resourcePrice') * $row['s3'];
				$shipPrice += ShipResource::getInfo(4, 'resourcePrice') * $row['s4'];
				$shipPrice += ShipResource::getInfo(5, 'resourcePrice') * $row['s5'];
				$shipPrice += ShipResource::getInfo(6, 'resourcePrice') * $row['s6'];
				$shipPrice += ShipResource::getInfo(7, 'resourcePrice') * $row['s7'];
				$shipPrice += ShipResource::getInfo(8, 'resourcePrice') * $row['s8'];
				$shipPrice += ShipResource::getInfo(9, 'resourcePrice') * $row['s9'];
				$shipPrice += ShipResource::getInfo(10, 'resourcePrice') * $row['s10'];
				$shipPrice += ShipResource::getInfo(11, 'resourcePrice') * $row['s11'];
				$points = round($shipPrice * self::COEF_RESOURCE);
				$points += $row['points'];
				$points += round($row['resources'] * self::COEF_RESOURCE);
				$this->results[$row['player']]['general'] += $points;

				$pevQuantity = 0;
				$pevQuantity += ShipResource::getInfo(0, 'pev') * $row['s0'];
				$pevQuantity += ShipResource::getInfo(1, 'pev') * $row['s1'];
				$pevQuantity += ShipResource::getInfo(2, 'pev') * $row['s2'];
				$pevQuantity += ShipResource::getInfo(3, 'pev') * $row['s3'];
				$pevQuantity += ShipResource::getInfo(4, 'pev') * $row['s4'];
				$pevQuantity += ShipResource::getInfo(5, 'pev') * $row['s5'];
				$pevQuantity += ShipResource::getInfo(6, 'pev') * $row['s6'];
				$pevQuantity += ShipResource::getInfo(7, 'pev') * $row['s7'];
				$pevQuantity += ShipResource::getInfo(8, 'pev') * $row['s8'];
				$pevQuantity += ShipResource::getInfo(9, 'pev') * $row['s9'];
				$pevQuantity += ShipResource::getInfo(10, 'pev') * $row['s10'];
				$pevQuantity += ShipResource::getInfo(11, 'pev') * $row['s11'];
				$this->results[$row['player']]['armies'] += $pevQuantity;
			}
		}
		$statement->closeCursor();
	}
	
	protected function calculateArmiesRanking(\PDOStatement $statement)
	{
		while ($row = $statement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$shipPrice = 0;
				$shipPrice += ShipResource::getInfo(0, 'resourcePrice') * $row['s0'];
				$shipPrice += ShipResource::getInfo(1, 'resourcePrice') * $row['s1'];
				$shipPrice += ShipResource::getInfo(2, 'resourcePrice') * $row['s2'];
				$shipPrice += ShipResource::getInfo(3, 'resourcePrice') * $row['s3'];
				$shipPrice += ShipResource::getInfo(4, 'resourcePrice') * $row['s4'];
				$shipPrice += ShipResource::getInfo(5, 'resourcePrice') * $row['s5'];
				$shipPrice += ShipResource::getInfo(6, 'resourcePrice') * $row['s6'];
				$shipPrice += ShipResource::getInfo(7, 'resourcePrice') * $row['s7'];
				$shipPrice += ShipResource::getInfo(8, 'resourcePrice') * $row['s8'];
				$shipPrice += ShipResource::getInfo(9, 'resourcePrice') * $row['s9'];
				$shipPrice += ShipResource::getInfo(10, 'resourcePrice') * $row['s10'];
				$shipPrice += ShipResource::getInfo(11, 'resourcePrice') * $row['s11'];
				$points = round($shipPrice * self::COEF_RESOURCE);
				$this->results[$row['player']]['general'] += $points;

				$pevQuantity = 0;
				$pevQuantity += ShipResource::getInfo(0, 'pev') * $row['s0'];
				$pevQuantity += ShipResource::getInfo(1, 'pev') * $row['s1'];
				$pevQuantity += ShipResource::getInfo(2, 'pev') * $row['s2'];
				$pevQuantity += ShipResource::getInfo(3, 'pev') * $row['s3'];
				$pevQuantity += ShipResource::getInfo(4, 'pev') * $row['s4'];
				$pevQuantity += ShipResource::getInfo(5, 'pev') * $row['s5'];
				$pevQuantity += ShipResource::getInfo(6, 'pev') * $row['s6'];
				$pevQuantity += ShipResource::getInfo(7, 'pev') * $row['s7'];
				$pevQuantity += ShipResource::getInfo(8, 'pev') * $row['s8'];
				$pevQuantity += ShipResource::getInfo(9, 'pev') * $row['s9'];
				$pevQuantity += ShipResource::getInfo(10, 'pev') * $row['s10'];
				$pevQuantity += ShipResource::getInfo(11, 'pev') * $row['s11'];
				$this->results[$row['player']]['armies'] += $pevQuantity;
			}
		}
		$statement->closeCursor();
	}
	
	protected function calculateTradeRanking(\PDOStatement $routesStatement, \PDOStatement $linkedRoutesStatement)
	{
		while ($row = $routesStatement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['trader'] += $row['income'];
			}
		}
		$routesStatement->closeCursor();
		while ($row = $linkedRoutesStatement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['trader'] += $row['income'];
			}
		}
		$linkedRoutesStatement->closeCursor();
	}
	
	protected function calculateButcherRanking(\PDOStatement $attackerStatement, \PDOStatement $defenderStatement)
	{
		while ($row = $attackerStatement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['butcherDestroyedPEV'] += $row['destroyedPEV'];
				$this->results[$row['player']]['butcherLostPEV'] += $row['lostPEV'];
				$this->results[$row['player']]['butcher'] += $row['destroyedPEV'] - $row['lostPEV'];
			}
		}
		$attackerStatement->closeCursor();
		while ($row = $defenderStatement->fetch()) {
			if (isset($this->results[$row['player']])) {
				$this->results[$row['player']]['butcherDestroyedPEV'] += $row['destroyedPEV'];
				$this->results[$row['player']]['butcherLostPEV'] += $row['lostPEV'];
				$this->results[$row['player']]['butcher'] += $row['destroyedPEV'] - $row['lostPEV'];
			}
		}
		$defenderStatement->closeCursor();
	}

	protected function cmpGeneral($a, $b)
	{
		if($a['general'] == $b['general']) {
			return 0;
		}
		return ($a['general'] > $b['general']) ? -1 : 1;
	}

	protected function cmpResources($a, $b) {
		if($a['resources'] == $b['resources']) {
			return 0;
		}
		return ($a['resources'] > $b['resources']) ? -1 : 1;
	}

	protected function cmpExperience($a, $b) {
		if($a['experience'] == $b['experience']) {
			return 0;
		}
		return ($a['experience'] > $b['experience']) ? -1 : 1;
	}

	protected function cmpFight($a, $b) {
		if($a['fight'] == $b['fight']) {
			return 0;
		}
		return ($a['fight'] > $b['fight']) ? -1 : 1;
	}

	protected function cmpArmies($a, $b) {
		if($a['armies'] == $b['armies']) {
			return 0;
		}
		return ($a['armies'] > $b['armies']) ? -1 : 1;
	}

	protected function cmpButcher($a, $b) {
		if($a['butcher'] == $b['butcher']) {
			return 0;
		}
		return ($a['butcher'] > $b['butcher']) ? -1 : 1;
	}

	protected function cmpTrader($a, $b) {
		if($a['trader'] == $b['trader']) {
			return 0;
		}
		return ($a['trader'] > $b['trader']) ? -1 : 1;
	}
}