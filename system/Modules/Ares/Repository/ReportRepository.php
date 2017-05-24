<?php

namespace Asylamba\Modules\Ares\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Ares\Model\Report;

class ReportRepository extends AbstractRepository {
	/**
	 * @param int $id
	 * @return Report
	 */
	public function get($id) {
		if (($report = $this->unitOfWork->getObject(Report::class, $id)) !== null) {
			return $report;
		}
		$query = $this->connection->prepare(
			'SELECT r.*,
				sq.id AS sqId,
				sq.position AS sqPosition,
				sq.rReport AS sqRReport,
				sq.round AS sqRound,
				sq.rCommander AS sqRCommander,
				sq.ship0 AS sqShip0,
				sq.ship1 AS sqShip1,
				sq.ship2 AS sqShip2,
				sq.ship3 AS sqShip3,
				sq.ship4 AS sqShip4,
				sq.ship5 AS sqShip5,
				sq.ship6 AS sqShip6,
				sq.ship7 AS sqShip7,
				sq.ship8 AS sqShip8,
				sq.ship9 AS sqShip9,
				sq.ship10 AS sqShip10,
				sq.ship11 AS sqShip11
			FROM report AS r
			LEFT JOIN squadronReport AS sq ON sq.rReport = r.id
			WHERE r.id = :id'
		);
		$query->execute(['id' => $id]);
		$result = $this->formatResult($query);
		return (isset($result[0])) ? $result[0] : null;
	}
	
	/**
	 * @param int $attackerId
	 * @param int $placeId
	 * @param string $dFight
	 * @return array
	 */
	public function getByAttackerAndPlace($attackerId, $placeId, $dFight)
	{
		$query = $this->connection->prepare(
			'SELECT r.*,
				sq.id AS sqId,
				sq.position AS sqPosition,
				sq.rReport AS sqRReport,
				sq.round AS sqRound,
				sq.rCommander AS sqRCommander,
				sq.ship0 AS sqShip0,
				sq.ship1 AS sqShip1,
				sq.ship2 AS sqShip2,
				sq.ship3 AS sqShip3,
				sq.ship4 AS sqShip4,
				sq.ship5 AS sqShip5,
				sq.ship6 AS sqShip6,
				sq.ship7 AS sqShip7,
				sq.ship8 AS sqShip8,
				sq.ship9 AS sqShip9,
				sq.ship10 AS sqShip10,
				sq.ship11 AS sqShip11
			FROM report AS r
			LEFT JOIN squadronReport AS sq ON sq.rReport = r.id
			WHERE r.rPlayerAttacker = :attacker_id AND r.rPlace = :place_id AND r.dFight = :fight'
		);
		$query->execute([
			'attacker_id' => $attackerId,
			'place_id' => $placeId,
			'fight' => $dFight
		]);
		return $this->formatResult($query);
	}
	
	public function formatResult($statement)
	{
		$currentId = null;
		$data = [];
		while ($row = $statement->fetch()) {
			// @TODO get the existing object if it is in UnitOfWork already
			// This code block is executed one time only, when the current row concerns the next report
			if ($currentId !== (int) $row['id']) {
				// $currentReport is the previous report here
				// We set the army with the squadron data and set it to the results
				if (isset($currentReport)) {
					$currentReport->setArmies();
					$data[] = $currentReport;
					if (!$this->unitOfWork->hasObject($currentReport)) {
						$this->unitOfWork->addObject($currentReport);
					}
				}
				// Now the variable represents the new report
				$currentReport = $this->format($row);
				$currentId = $currentReport->id;
			}
			// Here, $currentReport is the right report
			if (!empty($row['sqId'])) {
				$currentReport->squadrons[] = $this->formatSquadron($row);
			}
		}
		// Set the armies of the last report
		// The isset ensures that at least one report was fetched
		if (isset($currentReport)) {
			$currentReport->setArmies();
			$data[] = $currentReport;
			if (!$this->unitOfWork->hasObject($currentReport)) {
				$this->unitOfWork->addObject($currentReport);
			}
		}
		return $data;
	}
	
	/**
	 * @param Report $report
	 */
	public function insert($report)
	{
		$query = $this->connection->prepare('INSERT INTO report SET
			rPlayerAttacker = :attacker_player_id, rPlayerDefender = :defender_player_id,
			rPlayerWinner = :winner_player_id, avatarA = :attacker_avatar, avatarD = :defender_avatar,
			nameA = :attacker_name, nameD = :defender_name, levelA = :attacker_level,
			levelD = :defender_level, experienceA = :attacker_experience, experienceD = :defender_experience,
			palmaresA = :attacker_palmares, palmaresD = :defender_palmares, resources = :resources,
			expCom = :commander_experience, expPlayerA = :attacker_player_experience, expPlayerD = :defender_player_experience,
			rPlace = :place_id, placeName = :place_name, type = :type, isLegal = :is_legal, hasBeenPunished = :has_been_punished,
			round = :round, importance = :importance, pevInBeginA = :attacker_pev_in_begin,
			pevInBeginD = :defender_pev_in_begin, pevAtEndA = :attacker_pev_at_end, pevAtEndD = :defender_pev_at_end,
			statementAttacker = :attacker_statement, statementDefender = :defender_statement, dFight = :fight'
		);
		$query->execute(array(
			'attacker_player_id' => $report->rPlayerAttacker,
			'defender_player_id' => $report->rPlayerDefender,
			'winner_player_id' => $report->rPlayerWinner,
			'attacker_avatar' => $report->avatarA,
			'defender_avatar' => $report->avatarD,
			'attacker_name' => $report->nameA,
			'defender_name' => $report->nameD,
			'attacker_level' => $report->levelA,
			'defender_level' => $report->levelD,
			'attacker_experience' => $report->experienceA,
			'defender_experience' => $report->experienceD,
			'attacker_palmares' => $report->palmaresA,
			'defender_palmares' => $report->palmaresD,
			'resources' => $report->resources,
			'commander_experience' => $report->expCom,
			'attacker_player_experience' => $report->expPlayerA,
			'defender_player_experience' => $report->expPlayerD,
			'place_id' => $report->rPlace,
			'place_name' => $report->placeName,
			'type' => $report->type,
			'is_legal' => $report->isLegal,
			'has_been_punished' => $report->hasBeenPunished,
			'round' => $report->round,
			'importance' => $report->importance,
			'attacker_pev_in_begin' => $report->pevInBeginA,
			'defender_pev_in_begin' => $report->pevInBeginD,
			'attacker_pev_at_end' => $report->pevAtEndA,
			'defender_pev_at_end' => $report->pevAtEndD,
			'attacker_statement' => $report->statementAttacker,
			'defender_statement' => $report->statementDefender,
			'fight' => $report->dFight
		));
		$report->id = $this->connection->lastInsertId();
		$this->insertSquadrons($report);
	}
	
	/**
	 * @param Report $report
	 */
	public function insertSquadrons(Report $report)
	{
		$nbSquadrons = count($report->squadrons);
		if ($nbSquadrons === 0) {
			return;
		}
		for ($i = 0; $i < $nbSquadrons; $i++) {
			$report->squadrons[$i][2] = $report->id;
		}
		$query = 'INSERT INTO squadronReport (position, rReport, round, rCommander, ship0, ship1, ship2, ship3, ship4, ship5, ship6, ship7, ship8, ship9, ship10, ship11) 
		VALUES';
		for ($j = 0; $j < count($report->squadrons); $j++) {
			$query .= ' (' . $report->squadrons[$j][1];
				for ($i = 2; $i < 17; $i++) {
					$query .= ', ' . $report->squadrons[$j][$i];
				}
			$query .= ($j == count($report->squadrons) - 1) ? ');' : '),';
		}

		$statement = $this->connection->prepare($query);
		$statement->execute();
	}
	
	/**
	 * @param Report $report
	 */
	public function update($report)
	{
		$query = $this->connection->prepare('UPDATE report SET
			rPlayerAttacker = :attacker_player_id, rPlayerDefender = :defender_player_id,
			rPlayerWinner = :winner_player_id, avatarA = :attacker_avatar, avatarD = :defender_avatar,
			nameA = :attacker_name, nameD = :defender_name, levelA = :attacker_level,
			levelD = :defender_level, experienceA = :attacker_experience, experienceD = :defender_experience,
			palmaresA = :attacker_palmares, palmaresD = :defender_palmares, resources = :resources,
			expCom = :commander_experience, expPlayerA = :attacker_player_experience, expPlayerD = :defender_player_experience,
			rPlace = :place_id, placeName = :place_name, type = :type, isLegal = :is_legal, hasBeenPunished = :has_been_punished,
			round = :round, importance = :importance, pevInBeginA = :attacker_pev_in_begin,
			pevInBeginD = :defender_pev_in_begin, pevAtEndA = :attacker_pev_at_end, pevAtEndD = :defender_pev_at_end,
			statementAttacker = :attacker_statement, statementDefender = :defender_statement, dFight = :fight
			WHERE id = :id');
		$query->execute(array(
			'attacker_player_id' => $report->rPlayerAttacker,
			'defender_player_id' => $report->rPlayerDefender,
			'winner_player_id' => $report->rPlayerWinner,
			'attacker_avatar' => $report->avatarA,
			'defender_avatar' => $report->avatarD,
			'attacker_name' => $report->nameA,
			'defender_name' => $report->nameD,
			'attacker_level' => $report->levelA,
			'defender_level' => $report->levelD,
			'attacker_experience' => $report->experienceA,
			'defender_experience' => $report->experienceD,
			'attacker_palmares' => $report->palmaresA,
			'defender_palmares' => $report->palmaresD,
			'resources' => $report->resources,
			'commander_experience' => $report->expCom,
			'attacker_player_experience' => $report->expPlayerA,
			'defender_player_experience' => $report->expPlayerD,
			'place_id' => $report->rPlace,
			'place_name' => $report->placeName,
			'type' => $report->type,
			'is_legal' => $report->isLegal,
			'has_been_punished' => (int) $report->hasBeenPunished,
			'round' => $report->round,
			'importance' => $report->importance,
			'attacker_pev_in_begin' => $report->pevInBeginA,
			'defender_pev_in_begin' => $report->pevInBeginD,
			'attacker_pev_at_end' => $report->pevAtEndA,
			'defender_pev_at_end' => $report->pevAtEndD,
			'attacker_statement' => $report->statementAttacker,
			'defender_statement' => $report->statementDefender,
			'fight' => $report->dFight,
			'id' => $report->id
		));
	}
	
	/**
	 * @param Report $report
	 */
	public function remove($report)
	{
		$query = $this->connection->prepare('DELETE FROM report WHERE id = :id');
		$query->execute(['id' => $report->id]);
		
		$squadronQuery = $this->connection->prepare('DELETE FROM squadronReport WHERE rReport = :report_id');
		$squadronQuery->execute(['report_id' => $report->id]);
	}
	
	/**
	 * @param array $data
	 * @return Report
	 */
	public function format($data)
	{
		$report = new Report();
		$report->id = (int) $data['id'];
		$report->rPlayerAttacker = (int) $data['rPlayerAttacker'];
		$report->rPlayerDefender = (int) $data['rPlayerDefender'];
		$report->rPlayerWinner = (int) $data['rPlayerWinner'];
		$report->avatarA = $data['avatarA'];
		$report->avatarD = $data['avatarD'];
		$report->nameA = $data['nameA'];
		$report->nameD = $data['nameD'];
		$report->levelA = $data['levelA'];
		$report->levelD = $data['levelD'];
		$report->experienceA = (int) $data['experienceA'];
		$report->experienceD = (int) $data['experienceD'];
		$report->palmaresA = $data['palmaresA'];
		$report->palmaresD = $data['palmaresD'];
		$report->resources = (int) $data['resources'];
		$report->expCom = (int) $data['expCom'];
		$report->expPlayerA = (int) $data['expPlayerA'];
		$report->expPlayerD = (int) $data['expPlayerD'];
		$report->rPlace = (int) $data['rPlace'];
		$report->placeName = $data['placeName'];
		$report->type = $data['type'];
		$report->isLegal = (bool) $data['isLegal'];
		$report->hasBeenPunished = (bool) $data['hasBeenPunished'];
		$report->round = $data['round'];
		$report->importance = $data['importance'];
		$report->pevInBeginA = (int) $data['pevInBeginA'];
		$report->pevInBeginD = (int) $data['pevInBeginD'];
		$report->pevAtEndA = (int) $data['pevAtEndA'];
		$report->pevAtEndD = (int) $data['pevAtEndD'];
		$report->statementAttacker = $data['statementAttacker'];
		$report->statementDefender = $data['statementDefender'];
		$report->dFight = $data['dFight'];
		
		return $report;
	}
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function formatSquadron($data)
	{
		return [
			$data['sqId'], 
			$data['sqPosition'], 
			$data['sqRReport'], 
			$data['sqRound'],
			$data['sqRCommander'],
			$data['sqShip0'],
			$data['sqShip1'], 
			$data['sqShip2'], 
			$data['sqShip3'], 
			$data['sqShip4'], 
			$data['sqShip5'], 
			$data['sqShip6'], 
			$data['sqShip7'], 
			$data['sqShip8'], 
			$data['sqShip9'], 
			$data['sqShip10'], 
			$data['sqShip11']
		];
	}
}