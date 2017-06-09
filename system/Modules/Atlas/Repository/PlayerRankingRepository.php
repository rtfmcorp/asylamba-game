<?php

namespace Asylamba\Modules\Atlas\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Atlas\Model\PlayerRanking;

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
	
	public function insert($ranking)
	{
		
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
		while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
		{
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