<?php

namespace App\Modules\Atlas\Routine;

use App\Modules\Atlas\Model\Ranking;
use App\Modules\Atlas\Model\FactionRanking;
use App\Modules\Demeter\Model\Color;

use App\Classes\Library\Utils;

class FactionRoutine
{
	/**
	 * Contains results of all alive factions
	 *
	 * @var array
	 */
	protected $results = [];
	/** @var bool **/
	protected $isGameOver = false;
	
	/**
	 * @param Color $faction
	 * @param array $playerRankings
	 * @param array $routesIncome
	 * @param array $sectors
	 */
	public function execute(Color $faction, $playerRankings, $routesIncome, $sectors)
	{
		$this->results[$faction->getId()] = array(
			'general' => 0, 
			'wealth' => 0, 
			'territorial' => 0,
			'points' => $faction->rankingPoints);
		if ($faction->isWinner == 1) {
			$this->isGameOver = true;
		}
		$this->calculateGeneralRanking($playerRankings);
		$this->calculateWealthRanking($faction, $routesIncome);
		$this->calculateTerritorialRanking($faction, $sectors);
	}
	
	public function processResults(
		Ranking $ranking,
		$factions,
		$factionRankingManager,
		$serverStartTime,
		$hoursBeforeStartOfRanking,
		$pointsToWin,
	) {
		#---------------- COMPUTING -------------------#

		# copy the arrays
		$listG = $this->results;
		$listW = $this->results;
		$listT = $this->results;

		# sort all the copies
		uasort($listG, [$this, 'cmpFactionGeneral']);
		uasort($listW, [$this, 'cmpWealth']);
		uasort($listT, [$this, 'cmpTerritorial']);

		/*foreach ($list as $key => $value) {
			echo $key . ' => ' . $value['general'] . '<br/>';
		}*/

		# put the position in each array
		$listG = $this->setPositions($listG, 'general');
		$listW = $this->setPositions($listW, 'wealth');
		$listT = $this->setPositions($listT, 'territorial');

		#-------------------------------- POINTS RANKING -----------------------------#

		# faire ce classement uniquement après x jours de jeu
		if (Utils::interval($serverStartTime, Utils::now(), 'h') > $hoursBeforeStartOfRanking) {
			# points qu'on gagne en fonction de sa place dans le classement
			$pointsToEarn = [40, 30, 20, 10, 0, 0, 0, 0, 0, 0, 0];
			$coefG = 0.1; # 4 3 2 1 0 ...
			$coefW = 0.4; # 16 12 8 4 0 ...
			$coefT = 0.5; # 20 15 10 5 0 ...

			foreach ($factions as $faction) {
				$factionId = $faction->id;
				$additionalPoints = 0;

				# general
				$additionalPoints += intval($pointsToEarn[$listG[$factionId]['position'] - 1] * $coefG);

				# wealth
				$additionalPoints += intval($pointsToEarn[$listW[$factionId]['position'] - 1] * $coefW);

				# territorial
				$additionalPoints += intval($pointsToEarn[$listT[$factionId]['position'] - 1] * $coefT);

				$this->results[$factionId]['points'] += $additionalPoints;
			}
		}


		#---------------- LAST COMPUTING -------------------#

		$listP = $this->results;
		uasort($listP, [$this, 'cmpPoints']);

		$position = 1;
		foreach ($listP as $key => $value) { $listP[$key]['position'] = $position++;}

		#---------------- SAVING -------------------#

		$rankings = [];

		foreach ($factions as $faction) {
			$factionId = $faction->getId();
			$fr = new FactionRanking();
			$fr->rRanking = $ranking->getId();
			$fr->rFaction = $factionId; 

			$firstRanking = true;
			for ($i = 0; $i < $factionRankingManager->size(); $i++) {
				if ($factionRankingManager->get($i)->rFaction == $factionId) {
					$oldRanking = $factionRankingManager->get($i);
					$firstRanking = false;
					break;
				}
			}

			$fr->general = $listG[$factionId]['general'];
			$fr->generalPosition = $listG[$factionId]['position'];
			$fr->generalVariation = $firstRanking ? 0 : $oldRanking->generalPosition - $fr->generalPosition;

			$fr->wealth = $listW[$factionId]['wealth'];
			$fr->wealthPosition = $listW[$factionId]['position'];
			$fr->wealthVariation = $firstRanking ? 0 : $oldRanking->wealthPosition - $fr->wealthPosition;

			$fr->territorial = $listT[$factionId]['territorial'];
			$fr->territorialPosition = $listT[$factionId]['position'];
			$fr->territorialVariation = $firstRanking ? 0 : $oldRanking->territorialPosition - $fr->territorialPosition;

			if ($this->isGameOver === true) {
				$fr->points = $oldRanking->points;
				$fr->pointsPosition = $oldRanking->pointsPosition;
				$fr->pointsVariation = 0;
				$fr->newPoints = 0;
			} else {
				$fr->points = $listP[$factionId]['points'];
				$fr->pointsPosition = $listP[$factionId]['position'];
				$fr->pointsVariation = $firstRanking ? 0 : $oldRanking->pointsPosition - $fr->pointsPosition;
				$fr->newPoints = $firstRanking ? $fr->points : $fr->points - $oldRanking->points;
			}

			# update faction infos
			$faction->rankingPoints = $listP[$factionId]['points'];
			$faction->points = $listG[$factionId]['general'];
			$faction->sectors = $listT[$factionId]['territorial'];

			$rankings[] = $fr;
			$factionRankingManager->add($fr);
		}

		if ($this->isGameOver === false) {
			# check if a faction wins the game
			$winRanking = NULL;
			foreach ($rankings as $ranking) {
				if ($ranking->points >= $pointsToWin) {
					if ($winRanking !== NULL) {
						if ($winRanking->points < $ranking->points) {
							return $ranking->rFaction;
						}
					} else {
						return $ranking->rFaction;
					}
				}
			}
		}
		return null;
	}
	
	protected function calculateGeneralRanking($playerRankings)
	{
		foreach ($playerRankings as $playerRanking) {
			$player = $playerRanking->getPlayer();

			if (isset($player->rColor)) {
				$this->results[$player->rColor]['general'] += $playerRanking->general;
			}
		}
	}
	
	protected function calculateWealthRanking(Color $faction, $routesIncome)
	{
		if ($routesIncome['income'] == NULL) {
			$income = 0;
		} else {
			$income = $routesIncome['income'];
		}
		$this->results[$faction->getId()]['wealth'] = $income;
	}
	
	protected function calculateTerritorialRanking($faction, $sectors)
	{
		foreach ($sectors as $sector) {
			if ($sector->rColor === $faction->getId()) {
				$this->results[$sector->rColor]['territorial'] += $sector->points;
			}
		}
	}
	
	protected function cmpFactionGeneral($a, $b) {
		if ($a['general'] == $b['general']) {
			return 0;
		}
		return ($a['general'] > $b['general']) ? -1 : 1;
	}

	protected function cmpWealth($a, $b) {
		if ($a['wealth'] == $b['wealth']) {
			return 0;
		}
		return ($a['wealth'] > $b['wealth']) ? -1 : 1;
	}

	protected function cmpTerritorial($a, $b) {
		if ($a['territorial'] == $b['territorial']) {
			return 0;
		}
		return ($a['territorial'] > $b['territorial']) ? -1 : 1;
	}

	protected function cmpPoints($a, $b) {
		if ($a['points'] == $b['points']) {
			return 0;
		}
		return ($a['points'] > $b['points']) ? -1 : 1;
	}

	protected function setPositions($list, $attribute) {
		$position = 1;
		$index = 1;
		$previous = PHP_INT_MAX;
		foreach ($list as $key => $value) { 
			if ($previous > $list[$key][$attribute]) {
				$position = $index;
			}
			$list[$key]['position'] = $position;
			$index++;
			$previous = $list[$key][$attribute];
		}
		return $list;
	}
	
	public function getResults()
	{
		return $this->results;
	}
}
