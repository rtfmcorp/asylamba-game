<?php

namespace Asylamba\Modules\Atlas\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Atlas\Model\PlayerRanking;

use Asylamba\Classes\Library\Utils;

class PlayerRankingRepository extends AbstractRepository
{
    public function getFactionPlayerRankings(Color $faction)
    {
        $statement = $this->connection->prepare(
            'SELECT p.id as player_id, p.rColor as player_faction_id, pr.id as player_ranking_id, pr.general as player_general_ranking 
			FROM player p
			RIGHT JOIN playerRanking pr ON pr.rPlayer = p.id
			WHERE p.rColor = :faction_id'
        );
        $statement->execute(['faction_id' => $faction->getId()]);
        
        return $this->formatPlayerData($statement);
    }
    
    public function getAttackersButcherRanking()
    {
        return $this->connection->query(
            'SELECT
				p.id AS player,
				(SUM(pevInBeginA) - SUM(`pevAtEndA`)) AS lostPEV,
				(SUM(pevInBeginD) - SUM(`pevAtEndD`)) AS destroyedPEV
			FROM report AS r
			RIGHT JOIN player AS p
				ON p.id = r.rPlayerAttacker
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY p.id
			ORDER BY p.id'
        );
    }
    
    public function getDefendersButcherRanking()
    {
        return $this->connection->query(
            'SELECT
				p.id AS player,
				(SUM(pevInBeginD) - SUM(`pevAtEndD`)) AS lostPEV,
				(SUM(pevInBeginA) - SUM(`pevAtEndA`)) AS destroyedPEV,
				((SUM(pevInBeginD) - SUM(`pevAtEndD`)) - (SUM(pevInBeginA) - SUM(`pevAtEndA`))) AS score
			FROM report AS r
			RIGHT JOIN player AS p
				ON p.id = r.rPlayerDefender
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY p.id
			ORDER BY p.id'
        );
    }
    
    public function getPlayersResources()
    {
        return $this->connection->query(
            'SELECT p.id AS player,
				ob.levelRefinery AS levelRefinery,
				pl.coefResources AS coefResources
			FROM orbitalBase AS ob 
			LEFT JOIN place AS pl
				ON pl.id = ob.rPlace
			LEFT JOIN player AS p
				on p.id = ob.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')'
        );
    }
    
    public function getPlayersResourcesData()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				SUM(ob.resourcesStorage) AS sumResources
			FROM orbitalBase AS ob 
			LEFT JOIN player AS p
				on p.id = ob.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY ob.rPlace'
        );
    }
    
    public function getPlayersGeneralData()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				SUM(ob.points) AS points,
				SUM(ob.resourcesStorage) AS resources,
				SUM(ob.pegaseStorage) AS s0,
				SUM(ob.satyreStorage) AS s1,
				SUM(ob.sireneStorage) AS s3,
				SUM(ob.dryadeStorage) AS s4,
				SUM(ob.chimereStorage) AS s2,
				SUM(ob.meduseStorage) AS s5,
				SUM(ob.griffonStorage) AS s6,
				SUM(ob.cyclopeStorage) AS s7,
				SUM(ob.minotaureStorage) AS s8,
				SUM(ob.hydreStorage) AS s9,
				SUM(ob.cerbereStorage) AS s10,
				SUM(ob.phenixStorage) AS s11
			FROM orbitalBase AS ob 
			LEFT JOIN player AS p
				ON p.id = ob.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY p.id'
        );
    }
    
    public function getPlayersArmiesData()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				SUM(sq.ship0) as s0,
				SUM(sq.ship1) as s1,
				SUM(sq.ship2) as s2,
				SUM(sq.ship3) as s3,
				SUM(sq.ship4) as s4,
				SUM(sq.ship5) as s5,
				SUM(sq.ship6) as s6,
				SUM(sq.ship7) as s7,
				SUM(sq.ship8) as s8,
				SUM(sq.ship9) as s9,
				SUM(sq.ship10) as s10,
				SUM(sq.ship11) as s11
			FROM squadron AS sq 
			LEFT JOIN commander AS c
				ON c.id = sq.rCommander
			LEFT JOIN player AS p
				ON p.id = c.rPlayer
			WHERE c.statement IN (' . implode(',', [Commander::AFFECTED, Commander::MOVING]) . ')
			GROUP BY p.id'
        );
    }
    
    public function getPlayersPlanetData()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				COUNT(ob.rPlace) AS sumPlanets
			FROM orbitalBase AS ob
			LEFT JOIN player AS p
				on p.id = ob.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY ob.rPlace'
        );
    }
    
    public function getPlayersTradeRoutes()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				SUM(income) AS income
			FROM commercialRoute AS c
			LEFT JOIN orbitalBase AS o
				ON o.rPlace = c.rOrbitalBase
				RIGHT JOIN player AS p
					ON p.id = o.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY p.id
			ORDER BY p.id'
        );
    }
    
    public function getPlayersLinkedTradeRoutes()
    {
        return $this->connection->query(
            'SELECT 
				p.id AS player,
				SUM(income) AS income
			FROM `commercialRoute` AS c
			LEFT JOIN orbitalBase AS o
				ON o.rPlace = c.rOrbitalBaseLinked
				RIGHT JOIN player AS p
					ON p.id = o.rPlayer
			WHERE p.statement IN (' . implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			GROUP BY p.id
			ORDER BY p.id'
        );
    }
    
    public function insert($ranking)
    {
    }
    
    public function insertDataAnalysis(Player $player, PlayerRanking $playerRanking, $resources, $planetNumber)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO 
			DA_PlayerDaily(rPlayer, credit, experience, level, victory, defeat, status, resources, fleetSize, nbPlanet, planetPoints, rkGeneral, rkFighter, rkProducer, rkButcher, rkTrader, dStorage)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $player->id,
            $player->credit,
            $player->experience,
            $player->level,
            $player->victory,
            $player->defeat,
            $player->status,
            $resources,
            $pr->armies,
            $planetNumber,
            $pr->general / $planetNumber,
            $pr->general,
            $pr->fight,
            $pr->resources,
            $pr->butcher,
            $pr->trader,
            Utils::now()
        ]);
    }
    
    public function update($ranking)
    {
    }
    
    public function remove($ranking)
    {
    }
    
    public function formatPlayerData($statement)
    {
        $results = [];
        $currentPlayer = null;
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if (!$currentPlayer instanceof Player || $currentPlayer->getId() !== (int) $row['player_id']) {
                $currentPlayer =
                    (new Player())
                    ->setId((int) $row['player_id'])
                    ->setRColor((int) $row['player_faction_id'])
                ;
            }
            $results[] =
                (new PlayerRanking())
                ->setId((int) $row['player_ranking_id'])
                ->setPlayer($currentPlayer)
                ->setGeneral((int) $row['player_general_ranking'])
            ;
        }
        return $results;
    }
}
