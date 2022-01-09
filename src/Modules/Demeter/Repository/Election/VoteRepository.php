<?php

namespace App\Modules\Demeter\Repository\Election;

use App\Classes\Entity\AbstractRepository;

use App\Modules\Demeter\Model\Election\Vote;

class VoteRepository extends AbstractRepository {
	/**
	 * @param int $playerId
	 * @param int $electionId
	 * @return Vote
	 */
	public function getPlayerVote($playerId, $electionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM vote WHERE rElection = :election_id AND rPlayer = :player_id');
		$statement->execute(['election_id' => $electionId, 'player_id' => $playerId]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		if (($v = $this->unitOfWork->getObject(Vote::class, $row['id'])) !== null) {
			return $v;
		}
		$vote = $this->format($row);
		$this->unitOfWork->addObject($vote);
		return $vote;
	}
	
	/**
	 * @param int $electionId
	 * @return array
	 */
	public function getElectionVotes($electionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM vote WHERE rElection = :election_id');
		$statement->execute(['election_id' => $electionId]);
		
		$data = [];
		while (($row = $statement->fetch()) !== false) {
			if (($v = $this->unitOfWork->getObject(Vote::class, $row['id'])) !== null) {
				$data[] = $v;
				continue;
			}
			$vote = $this->format($row);
			$this->unitOfWork->addObject($vote);
			$data[] = $vote;
		}
		return $data;
	}
	
	/**
	 * @param Vote $vote
	 */
	public function insert($vote)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO vote SET
			rCandidate = :candidate_id,
			rPlayer = :player_id,
			rElection = :election_id,
			dVotation = :voted_at'
		);
		$statement->execute(array(
			'candidate_id' => $vote->rCandidate,
			'player_id' => $vote->rPlayer,
			'election_id' => $vote->rElection,
			'voted_at' => $vote->dVotation
		));
		$vote->id = $this->connection->lastInsertId();
	}
	
	/**
	 * @param Vote $vote
	 */
	public function update($vote)
	{
		$statement = $this->connection->prepare(
			'UPDATE vote SET
				rCandidate = :candidate_id,
				rPlayer = :player_id,
				dVotation = :voted_at
			WHERE id = :id');
		$statement->execute(array(
			'candidate_id' => $vote->rCandidate,
			'player_id' => $vote->rPlayer,
			'voted_at' => $vote->dVotation,
			'id' => $vote->id
		));
	}
	
	/**
	 * @param Vote $vote
	 */
	public function remove($vote)
	{
		$statement = $this->connection->prepare('DELETE FROM vote WHERE id = :id');
		$statement->execute(['id' => $vote->id]);
	}
	
	/**
	 * @param array $data
	 * @return Vote
	 */
	public function format($data)
	{
		$vote = new Vote();
		$vote->id = (int) $data['id'];
		$vote->rCandidate = (int) $data['rCandidate'];
		$vote->rPlayer = (int) $data['rPlayer'];
		$vote->relection = (int) $data['rElection'];
		$vote->dVotation = $data['dVotation'];
		return $vote;
	}
}
