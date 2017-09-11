<?php

namespace Tests\Asylamba\Modules\Ares\Model;

use Asylamba\Modules\Ares\Model\Ship;

class ShipTest extends \PHPUnit\Framework\TestCase
{
    public function testEntity()
    {
        $createdAt = '2017-05-01 21:20:30';
        $affectedAt = '2017-05-02 21:20:30';
        $arrivedAt = '2017-05-06 21:20:30';
        $updatedAt = '2017-05-06 20:20:30';

        $ship =new Ship(8, false);
        $ship->setId(1);

        $this->assertEquals(1, $ship->getId());
        $this->assertEquals('Destroyer', $ship->getName());
        $this->assertEquals('Minotaure', $ship->getCodeName());
        $this->assertEquals(8, $ship->getNbrName());
        $this->assertEquals(1200, $ship->getLife());
        $this->assertEquals(88, $ship->getSpeed());
        $this->assertEquals(array(35, 35, 35, 35, 25, 10, 10), $ship->getAttack());
        $this->assertEquals(120, $ship->getDefense());
        $this->assertEquals(75, $ship->getPev());
    }

    public function testFight()
    {
        /*$ship = new ship();
        $ship->engage($this->getSquadronMock());*/
    }

    public function testBonus()
    {
    }

    protected function chooseEnemyMock($enemySquadron)
    {
        return 0;
    }

    public function getSquadronMock()
    {
        return [
                'id' => 1,
                'data' => [
                    2,
                    0,
                    17,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    '2017-05-16 20:00:00',
                    '2017-05-16 20:00:00'
                ]
      ];
    }
}
