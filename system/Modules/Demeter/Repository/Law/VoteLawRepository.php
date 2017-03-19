<?php

namespace Asylamba\Modules\Demeter\Repository\Law;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Law\VoteLaw;

class VoteLawRepository extends AbstractRepository {
	
	public function getLawVotes($lawId)
	{
		$statement = $this->connection->prepare('SELECT * FROM voteLaw WHERE rLaw = :law_id');
		$statement->execute(['law_id' => $lawId]);
		
		$data = [];
		while($row = $statement->fetch())
		{
			if (($lv = $this->unitOfWork->getObject(LawVote::class, $row['id']))) {
				$data[] = $lv;
				continue;
			}
			$lawVote = $this->format($row);
			$this->unitOfWork->addObject($lawVote);
			$data[] = $lawVote;
		}
		return $data;
	}
	
	public function hasVoted($playerId, $lawId)
	{
		$statement = $this->connection->prepare('SELECT COUNT(*) AS nb_votes FROM voteLaw WHERE rPlayer = :player_id AND rLaw = :law_id');
		$statement->execute(['player_id' => $playerId, 'law_id' => $lawId]);
		return ((int) $statement->fetch()['nb_votes'] > 0);
	}
	
	public function insert($voteLaw)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO voteLaw SET
			rLaw = :law_id,
			rPlayer = :player_id,
			vote = :vote,
			dVotation = :voted_at'
		);
		$statement->execute([
			'law_id' => $voteLaw->rLaw,
			'player_id' => $voteLaw->rPlayer,
			'vote' => $voteLaw->vote,
			'voted_at' => $voteLaw->dVotation
		]);
		$voteLaw->id = $this->connection->lastInsertId();
	}
	
	public function update($voteLaw)
	{
		$statement = $this->connection->prepare(
			'UPDATE voteLaw SET
				rLaw = :law_id,
				rPlayer = :player_id,
				vote = :vote,
				dVotation = :voted_at
			WHERE id = :id'
		);
		$statement->execute([
			'law_id' => $voteLaw->rLaw,
			'player_id' => $voteLaw->rPlayer,
			'vote' => $voteLaw->vote,
			'voted_at' => $voteLaw->dVotation,
			'id' => $voteLaw->id
		]);
	}
	
	public function remove($voteLaw)
	{
		$qr = $this->connection->prepare('DELETE FROM voteLaw WHERE id = :id');
		$qr->execute(['id' => $voteLaw->id]);
	}
	
	public function format($data)
	{
		$voteLaw = new VoteLaw();
		$voteLaw->id = (int) $data['id'];
		$voteLaw->rLaw = (int) $data['rLaw'];
		$voteLaw->rPlayer = (int) $data['rPlayer'];
		$voteLaw->vote = $data['vote'];
		$voteLaw->dVotation = $data['dVotation'];
		return $voteLaw;
	}
}