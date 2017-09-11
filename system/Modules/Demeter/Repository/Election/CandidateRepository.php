<?php

namespace Asylamba\Modules\Demeter\Repository\Election;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Election\Candidate;

class CandidateRepository extends AbstractRepository
{
    /**
     * @param string $clause
     * @param array $params
     * @return \PDOStatement
     */
    public function select($clause, $params)
    {
        $statement = $this->connection->prepare(
            'SELECT c.*,
			p.name AS pName,
			p.avatar AS pAvatar,
			p.factionPoint AS pFactionPoint,
			p.status AS pStatus
			FROM candidate AS c
			LEFT JOIN player AS p ON p.id = c.rPlayer ' . $clause
        );
        $statement->execute($params);
        return $statement;
    }
    
    /**
     * @param int $id
     * @return Candidate
     */
    public function get($id)
    {
        if (($c = $this->unitOfWork->getObject(Candidate::class, $id)) !== null) {
            return $c;
        }
        if (($row = $this->select('WHERE c.id = :id', ['id' => $id])->fetch()) === false) {
            return null;
        }
        $candidate = $this->format($row);
        $this->unitOfWork->addObject($candidate);
        return $candidate;
    }
    
    /**
     * @param int $electionId
     * @param int $playerId
     * @return Candidate
     */
    public function getByElectionAndPlayer($electionId, $playerId)
    {
        $statement = $this->select('WHERE c.rElection = :election_id AND c.rPlayer = :player_id', ['election_id' => $electionId, 'player_id' => $playerId]);
        
        if (($row = $statement->fetch()) === false) {
            return null;
        }
        if (($c = $this->unitOfWork->getObject(Candidate::class, $row['id'])) !== null) {
            return $c;
        }
        $candidate = $this->format($row);
        $this->unitOfWork->addObject($candidate);
        return $candidate;
    }
    
    /**
     * @param int $electionId
     * @return array
     */
    public function getByElection($electionId)
    {
        $statement = $this->select('WHERE c.rElection = :election_id', ['election_id' => $electionId]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($c = $this->unitOfWork->getObject(Candidate::class, $row['id']))) {
                $data[] = $c;
                continue;
            }
            $candidate = $this->format($row);
            $this->unitOfWork->addObject($candidate);
            $data[] = $candidate;
        }
        return $data;
    }
    
    public function insert($candidate)
    {
        $qr = $this->connection->prepare(
            'INSERT INTO candidate SET
				rElection = :election_id,
				rPlayer = :player_id,
				chiefChoice = :chief_choice,
				treasurerChoice = :treasurer_choice,
				warlordChoice = :warlord_choice,
				ministerChoice = :minister_choice,
				program = :program,
				dPresentation = :presented_at'
        );

        $qr->execute(array(
            'election_id' => $candidate->rElection,
            'player_id' => $candidate->rPlayer,
            'chief_choice' => $candidate->chiefChoice,
            'treasurer_choice' => $candidate->treasurerChoice,
            'warlord_choice' => $candidate->warlordChoice,
            'minister_choice' => $candidate->ministerChoice,
            'program' => $candidate->program,
            'presented_at' => $candidate->dPresentation,
        ));
        $candidate->id = $this->connection->lastInsertId();
    }
    
    public function update($candidate)
    {
        $qr = $this->connection->prepare(
            'UPDATE candidate SET
				rElection = :election_id,
				rPlayer = :player_id,
				chiefChoice = :chief_choice,
				treasurerChoice = :treasurer_choice,
				warlordChoice = :warlord_choice,
				ministerChoice = :minister_choice,
				dPresentation = :presented_at
			WHERE id = :id'
        );
        $qr->execute(array(
            'election_id' => $candidate->rElection,
            'player_id' => $candidate->rPlayer,
            'chief_choice' => $candidate->chiefChoice,
            'treasurer_choice' => $candidate->treasurerChoice,
            'warlord_choice' => $candidate->warlordChoice,
            'minister_choice' => $candidate->ministerChoice,
            'presented_at' => $candidate->dPresentation,
            'id' => $candidate->id
        ));
    }
    
    public function remove($candidate)
    {
    }
    
    public function format($data)
    {
        $candidate = new Candidate();
        $candidate->id = $data['id'];
        $candidate->rElection = $data['rElection'];
        $candidate->rPlayer = $data['rPlayer'];
        $candidate->chiefChoice = $data['chiefChoice'];
        $candidate->treasurerChoice = $data['treasurerChoice'];
        $candidate->warlordChoice = $data['warlordChoice'];
        $candidate->ministerChoice = $data['ministerChoice'];
        $candidate->program = $data['program'];
        $candidate->dPresentation = $data['dPresentation'];
        $candidate->name = $data['pName'];
        $candidate->avatar = $data['pAvatar'];
        $candidate->factionPoint = $data['pFactionPoint'];
        $candidate->status = $data['pStatus'];
        return $candidate;
    }
}
