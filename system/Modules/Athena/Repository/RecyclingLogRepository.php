<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\RecyclingLog;

class RecyclingLogRepository extends AbstractRepository
{
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getBaseActiveMissionsLogs($baseId)
	{
		$statement = $this->connection->prepare(
			'SELECT rl.* FROM recyclingLog rl
			INNER JOIN recyclingMission rm ON rm.id = rl.rRecycling
			INNER JOIN place p ON p.id = rm.rBase
			WHERE p.id = :base_id
			ORDER BY rl.dLog DESC'
		);
		$statement->execute(['base_id' => $baseId]);
		$data = [];
		while ($row = $statement->fetch()) {
			$data[] = $this->format($row);
		}
		return $data;
	}
	
	public function insert($recyclingLog)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO
			recyclingLog(rRecycling, resources, credits, ship0, ship1, ship2, ship3, ship4, ship5, ship6, ship7,
				ship8, ship9, ship10, ship11, dLog)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
		);
		$statement->execute(array(
			$recyclingLog->rRecycling,
			$recyclingLog->resources,
			$recyclingLog->credits,
			$recyclingLog->ship0,
			$recyclingLog->ship1,
			$recyclingLog->ship2,
			$recyclingLog->ship3,
			$recyclingLog->ship4,
			$recyclingLog->ship5,
			$recyclingLog->ship6,
			$recyclingLog->ship7,
			$recyclingLog->ship8,
			$recyclingLog->ship9,
			$recyclingLog->ship10,
			$recyclingLog->ship11,
			$recyclingLog->dLog
		));
		$recyclingLog->id = $this->connection->lastInsertId();
	}
	
	public function update($recyclingLog)
	{
//		$statement = $this->connection->prepare(
//			'UPDATE recyclingLog SET
//				rRecycling = ?,
//				resources = ?,
//				credits = ?,
//				ship0 = ?,
//				ship1 = ?,
//				ship2 = ?,
//				ship3 = ?,
//				ship4 = ?,
//				ship5 = ?,
//				ship6 = ?,
//				ship7 = ?,
//				ship8 = ?,
//				ship9 = ?,
//				ship10 = ?,
//				ship11 = ?,
//				dLog = ?
//			WHERE id = ?');
//		$statement->execute(array(
//			$recyclingLog->rRecycling,
//			$recyclingLog->resources,
//			$recyclingLog->credits,
//			$recyclingLog->ship0,
//			$recyclingLog->ship1,
//			$recyclingLog->ship2,
//			$recyclingLog->ship3,
//			$recyclingLog->ship4,
//			$recyclingLog->ship5,
//			$recyclingLog->ship6,
//			$recyclingLog->ship7,
//			$recyclingLog->ship8,
//			$recyclingLog->ship9,
//			$recyclingLog->ship10,
//			$recyclingLog->ship11,
//			$recyclingLog->dLog,
//			$recyclingLog->id
//		));
	}
	
	public function remove($recyclingLog)
	{
		$statement = $this->connection->prepare('DELETE FROM recyclingLog WHERE id = :id');
		$statement->execute(['id' => $recyclingLog->id]);
	}
	
	public function removeMissionLogs($recyclingMissionId)
	{
		$statement = $this->connection->prepare('DELETE FROM recyclingLog WHERE rRecycling = :mission_id');
		$statement->execute(['mission_id' => $recyclingMissionId]);
	}
	
	public function format($data)
	{
		$recyclingLog = new RecyclingLog();

		$recyclingLog->id = (int) $data['id'];
		$recyclingLog->rRecycling = (int) $data['rRecycling'];
		$recyclingLog->resources = (int) $data['resources'];
		$recyclingLog->credits = (int) $data['credits'];
		$recyclingLog->ship0 = (int) $data['ship0'];
		$recyclingLog->ship1 = (int) $data['ship1'];
		$recyclingLog->ship2 = (int) $data['ship2'];
		$recyclingLog->ship3 = (int) $data['ship3'];
		$recyclingLog->ship4 = (int) $data['ship4'];
		$recyclingLog->ship5 = (int) $data['ship5'];
		$recyclingLog->ship6 = (int) $data['ship6'];
		$recyclingLog->ship7 = (int) $data['ship7'];
		$recyclingLog->ship8 = (int) $data['ship8'];
		$recyclingLog->ship9 = (int) $data['ship9'];
		$recyclingLog->ship10 = (int) $data['ship10'];
		$recyclingLog->ship11 = (int) $data['ship11'];
		$recyclingLog->dLog = $data['dLog'];
		
		return $recyclingLog;
	}
}