<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\RecyclingMission;

class RecyclingMissionRepository extends AbstractRepository
{
	public function select($clause = '', $parameters = [])
	{
		$statement = $this->connection->prepare(
			"SELECT rm.*,
				p.typeOfPlace AS typeOfPlace,
				p.position AS position,
				p.population AS population,
				p.coefResources AS coefResources,
				p.coefHistory AS coefHistory,
				p.resources AS resources,
				p.rSystem AS systemId,
				s.xPosition AS xPosition,
				s.yPosition AS yPosition,
				s.typeOfSystem AS typeOfSystem,
				s.rSector AS sectorId
			FROM recyclingMission AS rm
			LEFT JOIN place AS p
				ON rm.rTarget = p.id
				LEFT JOIN system AS s
					ON p.rSystem = s.id $clause"
		);
		$statement->execute($parameters);
		return $statement;
	}
	
	public function get($id)
	{
		if (($rm = $this->unitOfWork->getObject(RecyclingMission::class, $id)) !== null) {
			return $rm;
		}
		$statement = $this->select('WHERE rm.id = :id', ['id' => $id]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$recyclingMission = $this->format($row);
		$this->unitOfWork->addObject($recyclingMission);
		return $recyclingMission;
	}
	
	public function getAll()
	{
		$statement = $this->select();
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($rm = $this->unitOfWork->getObject(RecyclingMission::class, (int) $row['id'])) !== null) {
				$data[] = $rm;
				continue;
			}
			$recyclingMission = $this->format($row);
			$this->unitOfWork->addObject($recyclingMission);
			$data[] = $recyclingMission;
		}
		return $data;
	}
	
	public function getBaseMissions($baseId)
	{
		$statement = $this->select('WHERE rm.rBase = :base_id', ['base_id' => $baseId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($rm = $this->unitOfWork->getObject(RecyclingMission::class, (int) $row['id'])) !== null) {
				$data[] = $rm;
				continue;
			}
			$recyclingMission = $this->format($row);
			$this->unitOfWork->addObject($recyclingMission);
			$data[] = $recyclingMission;
		}
		return $data;
	}
	
	public function getBaseActiveMissions($baseId)
	{
		$statement = $this->select(
			'WHERE rm.rBase = :base_id AND rm.statement IN (' . implode(',', [RecyclingMission::ST_ACTIVE, RecyclingMission::ST_BEING_DELETED]) . ')',
			['base_id' => $baseId]
		);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($rm = $this->unitOfWork->getObject(RecyclingMission::class, (int) $row['id'])) !== null) {
				$data[] = $rm;
				continue;
			}
			$recyclingMission = $this->format($row);
			$this->unitOfWork->addObject($recyclingMission);
			$data[] = $recyclingMission;
		}
		return $data;
	}
	
	public function insert($recyclingMission)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO
			recyclingMission(rBase, rTarget, cycleTime, recyclerQuantity, addToNextMission, uRecycling, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?)'
		);
		$statement->execute(array(
			$recyclingMission->rBase,
			$recyclingMission->rTarget,
			$recyclingMission->cycleTime,
			$recyclingMission->recyclerQuantity,
			$recyclingMission->addToNextMission,
			$recyclingMission->uRecycling,
			$recyclingMission->statement
		));
		$recyclingMission->id = $this->connection->lastInsertId();
	}
	
	public function update($recyclingMission)
	{
		$statement = $this->connection->prepare(
			'UPDATE recyclingMission SET
				rBase = ?,
				rTarget = ?,
				cycleTime = ?,
				recyclerQuantity = ?,
				addToNextMission = ?,
				uRecycling = ?,
				statement = ?
			WHERE id = ?');
		$statement->execute(array(
			$recyclingMission->rBase,
			$recyclingMission->rTarget,
			$recyclingMission->cycleTime,
			$recyclingMission->recyclerQuantity,
			$recyclingMission->addToNextMission,
			$recyclingMission->uRecycling,
			$recyclingMission->statement,
			$recyclingMission->id
		));
	}
	
	public function remove($recyclingMission)
	{
		$statement = $this->connection->prepare('DELETE FROM recyclingMission WHERE id = :id');
		$statement->execute(['id' => $recyclingMission->id]);
	}
	
	public function removeBaseMissions($baseId)
	{
		$statement = $this->connection->prepare(
			'DELETE rm, rl FROM recyclingMission rm INNER JOIN recyclingLog rl ON rl.rRecycling = rm.id WHERE rm.rBase = :base_id'
		);
		$statement->execute(['base_id' => $baseId]);
	}
	
	public function format($data)
	{
		$recyclingMission = new RecyclingMission();

		$recyclingMission->id = (int) $data['id'];
		$recyclingMission->rBase = (int) $data['rBase'];
		$recyclingMission->rTarget = (int) $data['rTarget'];
		$recyclingMission->cycleTime = $data['cycleTime'];
		$recyclingMission->recyclerQuantity = (int) $data['recyclerQuantity'];
		$recyclingMission->addToNextMission = (int) $data['addToNextMission'];
		$recyclingMission->uRecycling = $data['uRecycling'];
		$recyclingMission->statement = (int) $data['statement'];

		$recyclingMission->typeOfPlace = (int) $data['typeOfPlace'];
		$recyclingMission->position = (int) $data['position'];
		$recyclingMission->population = (int) $data['population'];
		$recyclingMission->coefResources = (int) $data['coefResources'];
		$recyclingMission->coefHistory = (int) $data['coefHistory'];
		$recyclingMission->resources = (int) $data['resources'];
		$recyclingMission->systemId = (int) $data['systemId'];
		$recyclingMission->xSystem = (int) $data['xPosition'];
		$recyclingMission->ySystem = (int) $data['yPosition'];
		$recyclingMission->typeOfSystem = (int) $data['typeOfSystem'];
		$recyclingMission->sectorId = (int) $data['sectorId'];
		
		return $recyclingMission;
	}
}