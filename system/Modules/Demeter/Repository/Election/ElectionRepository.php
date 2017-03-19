<?php

namespace Asylamba\Modules\Demeter\Repository\Election;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Election\Election;

class ElectionRepository extends AbstractRepository {
	/**
	 * @param int $id
	 * @return Election
	 */
	public function get($id)
	{
		if (($e = $this->unitOfWork->getObject(Election::class, $id)) !== null) {
			return $e;
		}
		$statement = $this->connection->prepare('SELECT * FROM election WHERE id = :id');
		$statement->execute(['id' => $id]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$election = $this->format($row);
		$this->unitOfWork->addObject($election);
		return $election;
	}
	
	/**
	 * @param int $factionId
	 * @return Election
	 */
	public function getFactionLastElection($factionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM election WHERE rColor = :faction_id ORDER BY id DESC LIMIT 1');
		$statement->execute(['faction_id' => $factionId]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		if (($e = $this->unitOfWork->getObject(Election::class, $row['id'])) !== null) {
			return $e;
		}
		$election = $this->format($row);
		$this->unitOfWork->addObject($election);
		return $election;
	}
	
	/**
	 * @param Election $election
	 */
	public function insert($election)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO election SET rColor = :faction_id, dElection = :date'
		);
		$statement->execute(array(
			'faction_id' => $election->rColor,
			'date' => $election->dElection->format('Y-m-d H:i:s')
		));
		$election->id = $this->connection->lastInsertId();
	}
	
	/**
	 * @param Election $election
	 */
	public function update($election)
	{
		$statement = $this->connection->prepare('UPDATE election SET rColor = :faction_id, dElection = :date WHERE id = :id');
		$statement->execute(array(
			'faction_id' => $election->rColor,
			'date' => $election->dElection,
			'id' => $election->id
		));
	}
	
	/**
	 * @param Election $election
	 */
	public function remove($election)
	{
		$statement = $this->connection->prepare('DELETE FROM election WHERE id = :id');
		$statement->execute(['id' => $id]);
	}
	
	/**
	 * @param array $data
	 * @return Election
	 */
	public function format($data)
	{
		$election = new Election();
		$election->id = (int) $data['id'];
		$election->rColor = (int) $data['rColor'];
		$election->dElection = $data['dElection'];
		return $election;
	}
}