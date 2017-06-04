<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\ShipQueue;

class ShipQueueRepository extends AbstractRepository
{
	/**
	 * @param int $id
	 * @return ShipQueue
	 */
	public function get($id)
	{
		if (($sq = $this->unitOfWork->getObject(ShipQueue::class, $id)) !== null) {
			return $sq;
		}
		$statement = $this->connection->prepare('SELECT * FROM orbitalBaseShipQueue WHERE id = :id');
		$statement->execute(['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$shipQueue = $this->format($row);
		$this->unitOfWork->addObject($shipQueue);
		return $shipQueue;
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		$statement = $this->connection->query("SELECT * FROM orbitalBaseShipQueue");
		
		$data = [];
		while($row = $statement->fetch()) {
			if (($sq = $this->unitOfWork->getObject(ShipQueue::class, $row['id'])) !== null) {
				$data[] = $sq;
				continue;
			}
			$shipQueue = $this->format($row);
			$this->unitOfWork->addObject($shipQueue);
			$data[] = $shipQueue;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getBaseQueues($baseId)
	{
		$statement = $this->connection->prepare("SELECT * FROM orbitalBaseShipQueue WHERE rOrbitalBase = :base_id");
		$statement->execute(['base_id' => $baseId]);
		
		$data = [];
		while($row = $statement->fetch()) {
			if (($sq = $this->unitOfWork->getObject(ShipQueue::class, $row['id'])) !== null) {
				$data[] = $sq;
				continue;
			}
			$shipQueue = $this->format($row);
			$this->unitOfWork->addObject($shipQueue);
			$data[] = $shipQueue;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @param int $dockType
	 * @return array
	 */
	public function getByBaseAndDockType($baseId, $dockType)
	{
		$statement = $this->connection->prepare("SELECT * FROM orbitalBaseShipQueue WHERE rOrbitalBase = :base_id AND dockType = :dock_type ORDER BY dEnd");
		$statement->execute(['base_id' => $baseId, 'dock_type' => $dockType]);
		
		$data = [];
		while($row = $statement->fetch()) {
			if (($sq = $this->unitOfWork->getObject(ShipQueue::class, $row['id'])) !== null) {
				$data[] = $sq;
				continue;
			}
			$shipQueue = $this->format($row);
			$this->unitOfWork->addObject($shipQueue);
			$data[] = $shipQueue;
		}
		return $data;
	}
	
	/**
	 * @param ShipQueue $shipQueue
	 */
	public function insert($shipQueue)
	{
		$statement = $this->connection->prepare('INSERT INTO
			orbitalBaseShipQueue(rOrbitalBase, dockType, shipNumber, quantity, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?, ?)');
		$statement->execute(array(
			$shipQueue->rOrbitalBase,
			$shipQueue->dockType,
			$shipQueue->shipNumber,
			$shipQueue->quantity,
			$shipQueue->dStart,
			$shipQueue->dEnd
		));

		$shipQueue->id = $this->connection->lastInsertId();
	}
	
	/**
	 * @param ShipQueue $shipQueue
	 */
	public function update($shipQueue)
	{
		$statement = $this->connection->prepare('UPDATE orbitalBaseShipQueue
			SET	id = ?,
				rOrbitalBase = ?,
				dockType = ?,
				shipNumber = ?,
				quantity = ?,
				dStart = ?,
				dEnd = ?
			WHERE id = ?');
		$statement->execute(array(
			$shipQueue->id,
			$shipQueue->rOrbitalBase,
			$shipQueue->dockType,
			$shipQueue->shipNumber,
			$shipQueue->quantity,
			$shipQueue->dStart,
			$shipQueue->dEnd,
			$shipQueue->id
		));
	}
	
	/**
	 * @param ShipQueue $shipQueue
	 */
	public function remove($shipQueue)
	{
		$statement = $this->connection->prepare('DELETE FROM orbitalBaseShipQueue WHERE id = ?');
		$statement->execute(array($shipQueue->id));
	}
	
	/**
	 * @param array $data
	 * @return ShipQueue
	 */
	public function format($data)
	{
		$shipQueue = new ShipQueue();

		$shipQueue->id = (int) $data['id'];
		$shipQueue->rOrbitalBase = (int) $data['rOrbitalBase'];
		$shipQueue->dockType = (int) $data['dockType'];
		$shipQueue->shipNumber = (int) $data['shipNumber'];
		$shipQueue->quantity = (int) $data['quantity'];
		$shipQueue->dStart = $data['dStart'];
		$shipQueue->dEnd = $data['dEnd'];
		
		return $shipQueue;
	}
}