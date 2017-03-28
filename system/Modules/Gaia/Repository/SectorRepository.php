<?php

namespace Asylamba\Modules\Gaia\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Gaia\Model\Sector;

class SectorRepository extends AbstractRepository
{
	/**
	 * @param int $id
	 * @return Sector
	 */
	public function get($id)
	{
		if (($s = $this->unitOfWork->getObject(Sector::class, $id)) !== null) {
			return $s;
		}
		$statement = $this->connection->prepare('SELECT * FROM sector WHERE id = :id', ['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$sector = $this->format($row);
		$this->unitOfWork->addObject($sector);
		return $sector;
	}
	
	public function getFactionSectors($factionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM sector WHERE rColor = :faction_id');
		$statement->execute(['faction_id' => $factionId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($s = $this->unitOfWork->getObject(Sector::class, $row['id'])) !== null) {
				$data[] = $s;
				continue;
			}
			$sector = $this->format($row);
			$this->unitOfWork->addObject($sector);
			$data[] = $sector;
		}
		return $data;
	}
	
	public function getAll()
	{
		$statement = $this->connection->query('SELECT * FROM sector');
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($s = $this->unitOfWork->getObject(Sector::class, $row['id'])) !== null) {
				$data[] = $s;
				continue;
			}
			$sector = $this->format($row);
			$this->unitOfWork->addObject($sector);
			$data[] = $sector;
		}
		return $data;
	}
	
	public function insert($sector)
	{
		
	}
	
	public function update($sector)
	{
		$statement = $this->connection->prepare(
			'UPDATE sector SET
				rSurrender = :surrender_id,
				tax = :tax,
				name = :name
			WHERE id = :id');
		$statement->execute(array(
			'surrender_id' => $sector->rSurrender,
			'tax' => $sector->tax,
			'name' => $sector->name,
			'id' => $sector->id
		));
	}
	
	public function remove($sector)
	{
		
	}
	
	public function format($data)
	{
		$sector = new Sector();
		$sector->setId($data['id']);
		$sector->setRColor($data['rColor']);
		$sector->rSurrender = $data['rSurrender'];
		$sector->setXPosition($data['xPosition']);
		$sector->setYPosition($data['yPosition']);
		$sector->setXBarycentric($data['xBarycentric']);
		$sector->setYBarycentric($data['yBarycentric']);
		$sector->setTax($data['tax']);
		$sector->setName($data['name']);
		$sector->setPoints($data['points']);
		$sector->setPopulation($data['population']);
		$sector->setLifePlanet($data['lifePlanet']);
		return $sector;
	}
}