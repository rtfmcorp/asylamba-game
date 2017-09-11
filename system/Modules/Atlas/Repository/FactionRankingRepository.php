<?php

namespace Asylamba\Modules\Atlas\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Demeter\Model\Color;

class FactionRankingRepository extends AbstractRepository
{
    public function getRoutesIncome(Color $faction)
    {
        $qr = $this->connection->prepare(
            'SELECT COUNT(cr.id) AS nb,
				SUM(cr.income) AS income
			FROM commercialRoute AS cr
			LEFT JOIN orbitalBase AS ob1
				ON cr.rOrbitalBase = ob1.rPlace
				LEFT JOIN player AS pl1
					ON ob1.rPlayer = pl1.id
			LEFT JOIN orbitalBase AS ob2
				ON cr.rOrbitalBaseLinked = ob2.rPlace
				LEFT JOIN player AS pl2
					ON ob2.rPlayer = pl2.id
			WHERE (pl1.rColor = ? OR pl2.rColor = ?) AND cr.statement = ?'
        );
        # hint : en fait ça compte qu'une fois une route interfaction, mais chut
        $qr->execute([$faction->getId(), $faction->getId(), CommercialRoute::ACTIVE]);
        return $qr->fetch();
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
}
