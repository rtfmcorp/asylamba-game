<?php

namespace Asylamba\Modules\Atlas\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

class RankingRepository extends AbstractRepository
{
    public function hasBeenAlreadyProcessed($isPlayer, $isFaction)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ranking WHERE player = :is_player AND faction = :is_faction AND dRanking >= CURDATE()'
        );
        $statement->execute([
            'is_player' => (int) $isPlayer,
            'is_faction' => (int) $isFaction
        ]);
        return $statement->rowCount() > 0;
    }
    
    public function insert($ranking)
    {
        # create a new ranking
        $qr = $this->connection->prepare('INSERT INTO ranking(dRanking, player, faction) VALUES (:created_at, :is_player, :is_faction)');
        $qr->execute([
            'created_at' => $ranking->getCreatedAt(),
            'is_player' => (int) $ranking->getIsPlayer(),
            'is_faction' => (int) $ranking->getIsFaction()
        ]);

        $ranking->setId($this->connection->lastInsertId());
    }
    
    public function update($ranking)
    {
    }
    
    public function remove($ranking)
    {
    }
}
