<?php

namespace App\Modules\Athena\Repository;

use App\Classes\Entity\AbstractRepository;

use \App\Modules\Athena\Model\BuildingQueue;

class BuildingQueueRepository extends AbstractRepository
{
	/**
	 * @param int $id
	 * @return BuildingQueue
	 */
	public function get($id)
	{
		if (($bq = $this->unitOfWork->getObject(BuildingQueue::class, $id)) !== null) {
			return $bq;
		}
		$statement = $this->connection->prepare('SELECT * FROM orbitalBaseBuildingQueue WHERE id = :id');
		$statement->execute(['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$buildingQueue = $this->format($row);
		$this->unitOfWork->addObject($buildingQueue);
		return $buildingQueue;
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getBaseQueues($baseId)
	{
		$statement = $this->connection->prepare("SELECT * FROM orbitalBaseBuildingQueue WHERE rOrbitalBase = :base_id ORDER BY dEnd");
		$statement->execute(['base_id' => $baseId]);
		
		$data = [];
		while($row = $statement->fetch()) {
			if (($cr = $this->unitOfWork->getObject(BuildingQueue::class, $row['id'])) !== null) {
				$data[] = $cr;
				continue;
			}
			$buildingQueue = $this->format($row);
			$this->unitOfWork->addObject($buildingQueue);
			$data[] = $buildingQueue;
		}
		return $data;
	}
	
	/** 
	 * @return array
	 */
	public function getAll()
	{
		$query = $this->connection->query('SELECT * FROM orbitalBaseBuildingQueue');
		
		$data = [];
		while($row = $query->fetch()) {
			if (($cr = $this->unitOfWork->getObject(BuildingQueue::class, $row['id'])) !== null) {
				$data[] = $cr;
				continue;
			}
			$buildingQueue = $this->format($row);
			$this->unitOfWork->addObject($buildingQueue);
			$data[] = $buildingQueue;
		}
		return $data;
	}
	
	public function insert($buildingQueue)
	{
		$statement = $this->connection->prepare('INSERT INTO
			orbitalBaseBuildingQueue(rOrbitalBase, buildingNumber, targetLevel, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?)');
		$statement->execute(array(
			$buildingQueue->rOrbitalBase,
			$buildingQueue->buildingNumber,
			$buildingQueue->targetLevel,
			$buildingQueue->dStart,
			$buildingQueue->dEnd
		));
		$buildingQueue->id = $this->connection->lastInsertId();
	}
	
	public function update($buildingQueue)
	{
		$statement = $this->connection->prepare('UPDATE orbitalBaseBuildingQueue
			SET	id = ?,
				rOrbitalBase = ?,
				buildingNumber = ?,
				targetlevel = ?,
				dStart = ?,
				dEnd = ?
			WHERE id = ?');
		$statement->execute(array(
			$buildingQueue->id,
			$buildingQueue->rOrbitalBase,
			$buildingQueue->buildingNumber,
			$buildingQueue->targetLevel,
			$buildingQueue->dStart,
			$buildingQueue->dEnd,
			$buildingQueue->id
		));
	}
	
	public function remove($buildingQueue)
	{
		$qr = $this->connection->prepare('DELETE FROM orbitalBaseBuildingQueue WHERE id = ?');
		$qr->execute(array($buildingQueue->id));
	}
	
	public function format($data)
	{
		$buildingQueue = new BuildingQueue();

		$buildingQueue->id = (int) $data['id'];
		$buildingQueue->rOrbitalBase = (int) $data['rOrbitalBase'];
		$buildingQueue->buildingNumber = (int) $data['buildingNumber'];
		$buildingQueue->targetLevel = (int) $data['targetLevel'];
		$buildingQueue->dStart = $data['dStart'];
		$buildingQueue->dEnd = $data['dEnd'];

		return $buildingQueue;
	}
}
