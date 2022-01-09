<?php

namespace App\Modules\Promethee\Repository;

use App\Classes\Entity\AbstractRepository;

use App\Modules\Promethee\Model\TechnologyQueue;

class TechnologyQueueRepository extends AbstractRepository
{
	/**
	 * @param int $id
	 * @return TechnologyQueue $technologyQueue
	 */
	public function get($id)
	{
		if (($tq = $this->unitOfWork->getObject(TechnologyQueue::class, $id)) !== null) {
			return $tq;
		}
		$statement = $this->connection->prepare('SELECT * FROM technologyQueue WHERE id = :id');
		$statement->execute(['id' => $id]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$technologyQueue = $this->format($row);
		$this->unitOfWork->addObject($technologyQueue);
		return $technologyQueue;
	}
	
	/**
	 * @param int $playerId
	 * @param int $technology
	 * @return TechnologyQueue $technologyQueue
	 */
	public function getPlayerTechnologyQueue($playerId, $technology)
	{
		$statement = $this->connection->prepare('SELECT * FROM technologyQueue WHERE rPlayer = :player_id AND technology = :technology');
		$statement->execute(['player_id' => $playerId, 'technology' => $technology]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		if (($tq = $this->unitOfWork->getObject(TechnologyQueue::class, (int) $row['id'])) !== null) {
			return $tq;
		}
		$technologyQueue = $this->format($row);
		$this->unitOfWork->addObject($technologyQueue);
		return $technologyQueue;
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		$statement = $this->connection->query('SELECT * FROM technologyQueue');
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($tq = $this->unitOfWork->getObject(TechnologyQueue::class, (int) $row['id'])) !== null) {
				$data[] = $tq;
				continue;
			}
			$technologyQueue = $this->format($row);
			$this->unitOfWork->addObject($technologyQueue);
			$data[] = $technologyQueue;
		}
		return $data;
	}
	
	/**
	 * @param int $placeId
	 * @return array
	 */
	public function getPlaceQueues($placeId)
	{
		$statement = $this->connection->prepare('SELECT * FROM technologyQueue WHERE rPlace = :place_id ORDER BY dEnd');
		$statement->execute(['place_id' => $placeId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($tq = $this->unitOfWork->getObject(TechnologyQueue::class, (int) $row['id'])) !== null) {
				$data[] = $tq;
				continue;
			}
			$technologyQueue = $this->format($row);
			$this->unitOfWork->addObject($technologyQueue);
			$data[] = $technologyQueue;
		}
		return $data;
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerQueues($playerId)
	{
		$statement = $this->connection->prepare('SELECT * FROM technologyQueue WHERE rPlayer = :player_id');
		$statement->execute(['player_id' => $playerId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($tq = $this->unitOfWork->getObject(TechnologyQueue::class, (int) $row['id'])) !== null) {
				$data[] = $tq;
				continue;
			}
			$technologyQueue = $this->format($row);
			$this->unitOfWork->addObject($technologyQueue);
			$data[] = $technologyQueue;
		}
		return $data;
	}
	
	/**
	 * @param TechnologyQueue $technologyQueue
	 */
	public function insert($technologyQueue)
	{
		$qr = $this->connection->prepare('INSERT INTO
			technologyQueue(rPlayer, rPlace, technology, targetLevel, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$technologyQueue->getPlayerId(),
			$technologyQueue->getPlaceId(),
			$technologyQueue->getTechnology(),
			$technologyQueue->getTargetLevel(),
			$technologyQueue->getCreatedAt(),
			$technologyQueue->getEndedAt()
		));
		$technologyQueue->setId($this->connection->lastInsertId());
	}
	
	public function update($technologyQueue)
	{
		$statement = $this->connection->prepare(
			'UPDATE technologyQueue SET
				targetlevel = ?,
				dStart = ?,
				dEnd = ?
			WHERE id = ?');
		$statement->execute(array(
			$technologyQueue->getTargetLevel(),
			$technologyQueue->getCreatedAt(),
			$technologyQueue->getEndedAt(),
			$technologyQueue->getId()
		));
	}
	
	/**
	 * @param TechnologyQueue $technologyQueue
	 */
	public function remove($technologyQueue)
	{
		$statement = $this->connection->prepare('DELETE FROM technologyQueue WHERE id = :id');
		$statement->execute(['id' => $technologyQueue->getId()]);
	}
	
	/**
	 * @param array $data
	 * @return TechnologyQueue
	 */
	public function format($data)
	{
		return
			(new TechnologyQueue())
			->setId((int) $data['id'])
			->setPlayerId((int) $data['rPlayer'])
			->setTechnology((int) $data['technology'])
			->setTargetLevel((int) $data['targetLevel'])
			->setCreatedAt($data['dStart'])
			->setEndedAt($data['dEnd'])
		;
	}
}
