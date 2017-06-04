<?php

namespace Asylamba\Modules\Demeter\Repository\Law;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Law\Law;

class LawRepository extends AbstractRepository {
	public function select($clause, $params)
	{
		$statement = $this->connection->prepare(
			'SELECT l.*,
				(SELECT COUNT(v.id) FROM voteLaw AS v WHERE rLaw = l.id AND vote = 1) AS forVote,
				(SELECT COUNT(v.id) FROM voteLaw AS v WHERE rLaw = l.id AND vote = 0) AS againstVote
			FROM law AS l ' . $clause
		);
		$statement->execute($params);
		return $statement;
	}

	/**
	 * @param int $id
	 * @return Law
	 */
	public function get($id)
	{
		if(($l = $this->unitOfWork->getObject(Law::class, $id)) !== null) {
			return $l;
		}
		if (($row = $this->select('WHERE l.id = :id', ['id' => $id])->fetch()) === false) {
			return null;
		}
		$law = $this->format($row);
		$this->unitOfWork->addObject($law);
		return $law;
	}
	
	/**
	 * @param int $factionId
	 * @param array $statements
	 * @return array
	 */
	public function getByFactionAndStatements($factionId, $statements = [])
	{
		$statement = $this->select('WHERE l.rColor = :faction_id AND l.statement IN (' . implode(',', $statements) . ')', ['faction_id' => $factionId]);
		
		$data = [];
		while($row = $statement->fetch()) {
			if (($l = $this->unitOfWork->getObject(Law::class, $row['id'])) !== null) {
				$data[] = $l;
				continue;
			}
			$law = $this->format($row);
			$this->unitOfWork->addObject($law);
			$data[] = $law;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @param string $type
	 * @return bool
	 */
	public function lawExists($factionId, $type)
	{
		$statement = $this->connection->prepare(
			'SELECT COUNT(*) AS nb_laws FROM law WHERE rColor = :faction_id AND type = :type AND statement IN (' . implode(',', [Law::EFFECTIVE, Law::VOTATION]) . ')'
		);
		$statement->execute(['faction_id' => $factionId, 'type' => $type]);
		return ($statement->fetch()['nb_laws'] > 0);
	}
	
	public function insert($law)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO law SET
				rColor = :faction_id,
				type = :type,
				statement = :statement,
				options = :options,
				dEnd = :ended_at,
				dEndVotation = :vote_ended_at,
				dCreation = :created_at'
		);
		$statement->execute([
			'faction_id' => $law->rColor,
			'type' => $law->type,
			'statement' => $law->statement,
			'options' => $law->options,
			'ended_at' => $law->dEnd,
			'vote_ended_at' => $law->dEndVotation,
			'created_at' => $law->dCreation
		]);
		$law->id = $this->connection->lastInsertId();
	}
	
	public function update($law)
	{
		$statement = $this->connection->prepare(
			'UPDATE law SET
				rColor = :faction_id,
				type = :type,
				statement = :statement,
				dEnd = :ended_at,
				dEndVotation = :vote_ended_at,
				dCreation = :created_at
			WHERE id = :id');
		$statement->execute([
			'faction_id' => $law->rColor,
			'type' => $law->type,
			'statement' => $law->statement,
			'ended_at' => $law->dEnd,
			'vote_ended_at' => $law->dEndVotation,
			'created_at' => $law->dCreation,
			'id' => $law->id
		]);
	}
	
	public function remove($law)
	{
		$statement = $this->connection->prepare('DELETE FROM law WHERE id = :id');
		$statement->execute(['id' => $law->id]);
	}
	
	public function format($data)
	{
		$law = new Law();
		$law->id = (int) $data['id'];
		$law->rColor = (int) $data['rColor'];
		$law->type = $data['type'];
		$law->options = unserialize($data['options']);
		$law->statement = $data['statement'];
		$law->dEndVotation = $data['dEndVotation'];
		$law->dEnd = $data['dEnd'];
		$law->dCreation = $data['dCreation'];
		$law->forVote = $data['forVote'];
		$law->againstVote = $data['againstVote'];
		return $law;
	}
}