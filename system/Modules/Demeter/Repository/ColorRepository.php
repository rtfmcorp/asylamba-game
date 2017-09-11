<?php

namespace Asylamba\Modules\Demeter\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;

class ColorRepository extends AbstractRepository
{
    public function getFactionLinks($factionId)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM colorLink WHERE rColor = :faction_id ORDER BY rColorLinked'
        );
        $statement->execute(['faction_id' => $factionId]);
        return $statement;
    }
    
    public function get($id)
    {
        if (($f = $this->unitOfWork->getObject(Color::class, $id)) !== null) {
            return $f;
        }
        $statement = $this->connection->prepare('SELECT * FROM color WHERE id = :id');
        $statement->execute(['id' => $id]);
        
        if (($row = $statement->fetch()) === false) {
            return null;
        }
        $faction = $this->format($row);
        $this->unitOfWork->addObject($faction);
        return $faction;
    }
    
    public function getAll()
    {
        $statement = $this->connection->prepare('SELECT * FROM color');
        $statement->execute();
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($f = $this->unitOfWork->getObject(Color::class, $row['id'])) !== null) {
                $data[] = $f;
                continue;
            }
            $faction = $this->format($row);
            $this->unitOfWork->addObject($faction);
            $data[] = $faction;
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getInGameFactions()
    {
        $statement = $this->connection->prepare('SELECT * FROM color WHERE isInGame = 1');
        $statement->execute();
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($f = $this->unitOfWork->getObject(Color::class, $row['id'])) !== null) {
                $data[] = $f;
                continue;
            }
            $faction = $this->format($row);
            $this->unitOfWork->addObject($faction);
            $data[] = $faction;
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getOpenFactions()
    {
        $statement = $this->connection->prepare('SELECT * FROM color WHERE isClosed = 0');
        $statement->execute();
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($f = $this->unitOfWork->getObject(Color::class, $row['id'])) !== null) {
                $data[] = $f;
                continue;
            }
            $faction = $this->format($row);
            $this->unitOfWork->addObject($faction);
            $data[] = $faction;
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getAllByActivePlayersNumber()
    {
        $statement = $this->connection->prepare('SELECT * FROM color ORDER BY activePlayers ASC');
        $statement->execute();
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($f = $this->unitOfWork->getObject(Color::class, $row['id'])) !== null) {
                $data[] = $f;
                continue;
            }
            $faction = $this->format($row);
            $this->unitOfWork->addObject($faction);
            $data[] = $faction;
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getByRegimeAndElectionStatement($regimes, $electionStatements)
    {
        $statement = $this->connection->query(
            'SELECT * FROM color WHERE regime IN (' . implode(',', $regimes) . ') AND electionStatement IN (' . implode(',', $electionStatements) . ')'
        );
        $data = [];
        while ($row = $statement->fetch()) {
            if (($f = $this->unitOfWork->getObject(Color::class, $row['id'])) !== null) {
                $data[] = $f;
                continue;
            }
            $faction = $this->format($row);
            $this->unitOfWork->addObject($faction);
            $data[] = $faction;
        }
        return $data;
    }
    
    public function insert($faction)
    {
        $qr = $this->connection->prepare(
            'INSERT INTO color SET
				id = :id,
				alive = :alive,
				isWinner = :is_winner,
				credits = :credits,
				players = :nb_players,		
				activePlayers = :nb_active_players,
				rankingPoints = :ranking_points,
				points = :points,
				sectors = :nb_sectors,
				electionStatement = :election_statement,
				isClosed = :is_closed,
				isInGame = :is_in_game,
				description = :description,
				dClaimVictory = :victory_claimed_at,
				dLastElection = :last_election_at'
        );
        $qr->execute(array(
            'id' => $faction->id,
            'alive' => $faction->alive,
            'is_winner' => $faction->isWinner,
            'credits' => $faction->credits,
            'nb_players' => $faction->players,
            'nb_active_players' => $faction->activePlayers,
            'ranking_points' => $faction->rankingPoints,
            'points' => $faction->points,
            'sectors' => $faction->sectors,
            'election_statement' => $faction->electionStatement,
            'is_closed' => $faction->isClosed,
            'is_in_game' => $faction->isInGame,
            'description' => $faction->description,
            'victory_claimed_at' => $faction->dClaimVictory,
            'last_election_at' => $faction->dLastElection
        ));
        $faction->id = $this->connection->lastInsertId();
    }
    
    public function update($faction)
    {
        $statement = $this->connection->prepare(
            'UPDATE color SET
				alive = :alive,
				isWinner = :is_winner,
				credits = :credits,
				players = :nb_players,	
				activePlayers = :nb_active_players,
				rankingPoints = :ranking_points,
				points = :points,
				sectors = :sectors,
				electionStatement = :election_statement,
				isClosed = :is_closed,
				isInGame = :is_in_game,
				description = :description,
				dClaimVictory = :victory_claimed_at,
				dLastElection = :last_election_at
				WHERE id = :id'
        );
        $statement->execute(array(
            'alive' => $faction->alive,
            'is_winner' => (int) $faction->isWinner,
            'credits' => $faction->credits,
            'nb_players' => $faction->players,
            'nb_active_players' => $faction->activePlayers,
            'ranking_points' => $faction->rankingPoints,
            'points' => $faction->points,
            'sectors' => $faction->sectors,
            'election_statement' => $faction->electionStatement,
            'is_closed' => (int) $faction->isClosed,
            'is_in_game' => (int) $faction->isInGame,
            'description' => $faction->description,
            'victory_claimed_at' => $faction->dClaimVictory,
            'last_election_at' => $faction->dLastElection,
            'id' => $faction->id
        ));
        $factionLinkStatement = $this->connection->prepare('UPDATE colorLink SET
			statement = :statement WHERE rColor = :faction_id AND rColorLinked = :color_linked
		');
        foreach ($faction->colorLink as $key => $value) {
            $factionLinkStatement->execute(array(
                'statement' => $value,
                'faction_id' => $faction->id,
                'color_linked' => $key
            ));
        }
    }
    
    public function remove($faction)
    {
        $statement = $this->connection->prepare('DELETE FROM color WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
    
    public function format($data)
    {
        $faction = new Color();
        $faction->id = $data['id'];
        $faction->alive = $data['alive'];
        $faction->isWinner = (bool) $data['isWinner'];
        $faction->credits = $data['credits'];
        $faction->players = $data['players'];
        $faction->activePlayers = $data['activePlayers'];
        $faction->rankingPoints = $data['rankingPoints'];
        $faction->points = $data['points'];
        $faction->sectors = $data['sectors'];
        $faction->electionStatement = $data['electionStatement'];
        $faction->isClosed = (bool) $data['isClosed'];
        $faction->description = $data['description'];
        $faction->dClaimVictory = $data['dClaimVictory'];
        $faction->dLastElection = $data['dLastElection'];
        $faction->isInGame = (bool) $data['isInGame'];
        $faction->colorLink[0] = Color::NEUTRAL;

        $faction->officialName = ColorResource::getInfo($faction->id, 'officialName');
        $faction->popularName = ColorResource::getInfo($faction->id, 'popularName');
        $faction->government = ColorResource::getInfo($faction->id, 'government');
        $faction->demonym = ColorResource::getInfo($faction->id, 'demonym');
        $faction->factionPoint = ColorResource::getInfo($faction->id, 'factionPoint');
        $faction->status = ColorResource::getInfo($faction->id, 'status');
        $faction->regime = ColorResource::getInfo($faction->id, 'regime');
        $faction->devise = ColorResource::getInfo($faction->id, 'devise');
        $faction->desc1 = ColorResource::getInfo($faction->id, 'desc1');
        $faction->desc2 = ColorResource::getInfo($faction->id, 'desc2');
        $faction->desc3 = ColorResource::getInfo($faction->id, 'desc3');
        $faction->desc4 = ColorResource::getInfo($faction->id, 'desc4');
        $faction->bonus = ColorResource::getInfo($faction->id, 'bonus');
        $faction->mandateDuration = ColorResource::getInfo($faction->id, 'mandateDuration');
        $faction->senateDesc = ColorResource::getInfo($faction->id, 'senateDesc');
        $faction->campaignDesc = ColorResource::getInfo($faction->id, 'campaignDesc');

        $faction->bonusText = [];
        foreach (ColorResource::getInfo($faction->id, 'bonus') as $k) {
            $faction->bonusText[] = ColorResource::getBonus($k);
        }

        if ($faction->id != 0) {
            $factionLinksStatement = $this->getFactionLinks($faction->id);
            while ($factionLink = $factionLinksStatement->fetch()) {
                if ($factionLink['rColor'] == $faction->id) {
                    $faction->colorLink[$factionLink['rColorLinked']] = $factionLink['statement'];
                }
            }
        } else {
            $faction->colorLink = array(Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL);
        }
        return $faction;
    }
}
