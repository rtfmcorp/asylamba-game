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
	
	public function insert($system)
	{
		
	}
	
	public function update($system)
	{
		$statement = $this->connection->prepare('UPDATE system SET rColor = ? WHERE id = ?');
		$statement->execute(array(
			$system->rColor,
			$system->id
		));
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