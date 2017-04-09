<?php

namespace Asylamba\Modules\Gaia\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Gaia\Model\System;

class SystemRepository extends AbstractRepository
{
	public function get($id)
	{
		if (($s = $this->unitOfWork->getObject(System::class, $id)) !== null) {
			return $s;
		}
		$statement = $this->connection->prepare('SELECT * FROM system WHERE id = :id');
		$statement->execute(['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$system = $this->format($row);
		$this->unitOfWork->addObject($system);
		return $system;
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		$statement = $this->connection->query('SELECT * FROM system');
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($s = $this->unitOfWork->getObject(System::class, $row['id'])) !== null) {
				$data[] = $s;
				continue;
			}
			$system = $this->format($row);
			$this->unitOfWork->addObject($system);
			$data[] = $system;
		}
		return $data;
	}
	
	public function insert($system)
	{
		
	}
	
	public function update($system)
	{
		$statement = $this->connection->prepare('UPDATE system SET rColor = :faction_id WHERE id = :id');
		$statement->execute([
			'faction_id' => $system->rColor,
			'id' => $system->id
		]);
	}
	
	public function remove($system)
	{
		
	}
	
	public function format($data)
	{
		$system = new System();
		$system->id = $data['id'];
		$system->rSector = $data['rSector'];
		$system->rColor = $data['rColor'];
		$system->xPosition = $data['xPosition'];
		$system->yPosition = $data['yPosition'];
		$system->typeOfSystem = $data['typeOfSystem'];
		return $system;
	}
}