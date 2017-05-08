<?php

namespace Asylamba\Modules\Ares\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Modules\Ares\Model\LiveReport;

class LiveReportRepository extends AbstractRepository
{
	public function select($clause, $params = [])
	{
		$statement = $this->connection->prepare(
			'SELECT r.*,
				p1.rColor AS colorA,
				p2.rColor AS colorD,
				p1.name AS playerNameA,
				p2.name AS playerNameD
			FROM report AS r
			LEFT JOIN player AS p1
				ON p1.id = r.rPlayerAttacker
			LEFT JOIN player AS p2
				ON p2.id = r.rPlayerDefender
			WHERE ' . $clause
		);
		$statement->execute($params);
		return $statement;
	}
	
	public function get($id)
	{
		if (($lr = $this->unitOfWork->getObject(LiveReport::class, $id)) !== null) {
			return $lr;
		}
		$query = $this->select('r.id = :id', ['id' => $id]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$liveReport = $this->format($row);
		$this->unitOfWork->addObject($liveReport);
		return $liveReport;
	}
	
	public function getPlayerReports($playerId)
	{
		$statement = $this->select(
			'(rPlayerAttacker = ? AND statementAttacker = ?) OR (rPlayerDefender = ? AND statementDefender = ?)'
			, [
			$playerId,
			Report::STANDARD,
			$playerId,
			Report::STANDARD
		]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function getAttackReportsByPlaces($playerId, $places)
	{
		$statement = $this->select(
			'r.rPlayerAttacker = ? AND r.rPlace IN (' . implode(',', $places) . ') ORDER BY r.dFight DESC LIMIT 30'
		, [$playerId]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function getAttackReportsByMode($playerId, $hasRebels, $isArchived)
	{
		$statement = $this->select(
			'rPlayerAttacker = ? AND statementAttacker = ? ' . (($hasRebels) ? '' : 'AND p2.rColor != 0') . ' ORDER BY dFight DESC LIMIT 0, 50'
		, [$playerId, $isArchived]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function getDefenseReportsByMode($playerId, $hasRebels, $isArchived)
	{
		$statement = $this->select(
			'rPlayerDefender = ? AND statementDefender = ? ' . (($hasRebels) ? '' : 'AND p2.rColor != 0') . ' ORDER BY dFight DESC LIMIT 0, 50'
		, [$playerId, $isArchived]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function getFactionAttackReports($factionId)
	{
		$statement = $this->select(
			'p1.rColor = ? AND p2.rColor != 0 ORDER BY dFight DESC LIMIT 0, 30'
		, [$factionId]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function getFactionDefenseReports($factionId)
	{
		$statement = $this->select(
			'p2.rColor = ? AND p1.rColor != 0 ORDER BY dFight DESC LIMIT 0, 30'
		, [$factionId]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($lr = $this->unitOfWork->getObject(LiveReport::class, $row['id'])) !== null) {
				$data[] = $lr;
				continue;
			}
			$liveReport = $this->format($row);
			$this->unitOfWork->addObject($liveReport);
			$data[] = $liveReport;
		}
		return $data;
	}
	
	public function insert($report)
	{
		
	}
	
	public function update($report)
	{
		$statement = $this->connection->prepare(
			'UPDATE report SET
				rPlayerAttacker = ?,
				rPlayerDefender = ?,
				rPlayerWinner = ?,
				avatarA = ?,
				avatarD = ?,
				nameA = ?,
				nameD = ?,
				levelA = ?,
				levelD = ?,
				experienceA = ?,
				experienceD = ?,
				palmaresA = ?,
				palmaresD = ?,
				resources = ?,
				expCom = ?,
				expPlayerA = ?,
				expPlayerD = ?,
				rPlace = ?,
				isLegal = ?,
				placeName = ?,
				type = ?,
				round = ?,
				importance = ?,
				statementAttacker = ?,
				statementDefender = ?,
				dFight = ?
			WHERE id = ?'
		);
		$aw = $statement->execute(array(
			$report->rPlayerAttacker,
			$report->rPlayerDefender,
			$report->rPlayerWinner,
			$report->avatarA,
			$report->avatarD,
			$report->nameA,
			$report->nameD,
			$report->levelA,
			$report->levelD,
			$report->experienceA,
			$report->experienceD,
			$report->palmaresA,
			$report->palmaresD,
			$report->resources,
			$report->expCom,
			$report->expPlayerA,
			$report->expPlayerD,
			$report->rPlace,
			$report->isLegal,
			$report->placeName,
			$report->type,
			$report->round,
			$report->importance,
			$report->statementAttacker,
			$report->statementDefender,
			$report->dFight,
			$report->id
			)
		);
	}
	
	public function remove($report)
	{
		
	}
	
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
		$report->levelA = (int) $data['levelA'];
		$report->levelD = (int) $data['levelD'];
		$report->experienceA = (int) $data['experienceA'];
		$report->experienceD = (int) $data['experienceD'];
		$report->palmaresA = $data['palmaresA'];
		$report->palmaresD = $data['palmaresD'];
		$report->resources = (int) $data['resources'];
		$report->expCom = (int) $data['expCom'];
		$report->expPlayerA = (int) $data['expPlayerA'];
		$report->expPlayerD = (int) $data['expPlayerD'];
		$report->rPlace = (int) $data['rPlace'];
		$report->isLegal = (bool) $data['isLegal'];
		$report->placeName = $data['placeName'];
		$report->type = $data['type'];
		$report->round = $data['round'];
		$report->importance = $data['importance'];
		$report->statementAttacker = $data['statementAttacker'];
		$report->statementDefender = $data['statementDefender'];
		$report->dFight = $data['dFight'];

		$report->colorA = $data['colorA'];
		$report->colorD = $data['colorD'];
		$report->playerNameA = $data['playerNameA'];
		$report->playerNameD = $data['playerNameD'];
		
		return $report;
	}
}