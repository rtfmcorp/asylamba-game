<?php

namespace Tests\Asylamba\Modules\Demeter\Model;

use Asylamba\Modules\Demeter\Model\Color;

class ColorTest extends \PHPUnit\Framework\TestCase
{
    public function testEntity()
    {

        $color =
            (new Color())
            ->setId(1)
            -setAlive(TRUE)
            ->setisWinner(FALSE)
            ->setCredits(1000)
            //->setPlayers()
            //->setActivePlayers()
            ->setRankingPoints(820)
            ->setPoints(1200)
            //->setSectors()
            //->setElectionStatement()
            ->setIsClosed(TRUE)
            ->setDescription("Descrip")
            ->setDClaimVictory(TRUE)
            ->setDLastElection('2017-05-06 21:20:30')
            ->setIsInGame(TRUE)
            ;

            $this->assertEquals(1, $color->getId());
            $this->assertEquals(TRUE, $color->getAlive());
            $this->assertEquals(FALSE, $color->getIsWinner());
            $this->assertEquals(1000, $color->getCredits());
            //$this->assertEquals(, $color->getPlayers());
            //$this->assertEquals(, $color->getActivePlayers());
            $this->assertEquals(820, $color->getRankingPoints());
            $this->assertEquals(1200, $color->getPoints());
            //$this->assertEquals(, $color->getSectors());
            //$this->assertEquals(, $color->getElectionStatement());
            $this->assertEquals(TRUE, $color->getIsClosed());
            $this->assertEquals("Descrip", $color->getDescription());
            $this->assertEquals(TRUE, $color->getDClaimVictory());
            $this->assertEquals('2017-05-06 21:20:30', $color->getDLastElection());
            $this->assertEquals(TRUE, $color->getIsInGame());
    }

    public function testIncreaseCredit()
    {
      $color = new color();
      $this->assertEquals(2234, $color->increaseCredit(1324));
    }

    public function testDecreaseCredit()
    {
      $color = new color();
      $this->assertEquals(505, $color->decreaseCredit(495));
    }
}
