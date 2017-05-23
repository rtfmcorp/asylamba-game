<?php

namespace Tests\Asylamba\Modules\Ares\Model;

use Asylamba\Modules\Ares\Model\Commander;

class CommanderTest extends \PHPUnit_Framework_TestCase
{
    public function testEntity()
    {
        $createdAt = '2017-05-01 21:20:30';
        $affectedAt = '2017-05-02 21:20:30';
        $arrivedAt = '2017-05-06 21:20:30';
        $updatedAt = '2017-05-06 20:20:30';
        
        $commander =
            (new Commander())
            ->setId(1)
            ->setName('Theodorus')
            ->setAge(40)
            ->setSexe(1)
            ->setAvatar('comm-12-1.png')
            ->setLevel(2)
            ->setExperience(200)
            ->setComment('On my way !')
            ->setDCreation($createdAt)
            ->setDAffectation($affectedAt)
            ->setDDeath(null)
            ->setArrivalDate($arrivedAt)
            ->setTravelType(Commander::LOOT)
            ->setLengthTravel(15)
            ->setRPlayer(1)
            ->setRBase(18)
            ->setBaseName('Zambia')
            ->setRPlaceDestination(23)
            ->setDestinationPlaceName('Planète rebelle')
            ->setIsAttacker(true)
            ->setPalmares(1)
            ->setStatement(Commander::MOVING)
            ->setUCommander($updatedAt)
        ;
        $this->assertEquals(1, $commander->getId());
        $this->assertEquals('Theodorus', $commander->getName());
        $this->assertEquals(40, $commander->getAge());
        $this->assertEquals(1, $commander->getSexe());
        $this->assertEquals('comm-12-1.png', $commander->getAvatar());
        $this->assertEquals(2, $commander->getLevel());
        $this->assertEquals(200, $commander->getExperience());
        $this->assertEquals('On my way !', $commander->getComment());
        $this->assertEquals($createdAt, $commander->getDCreation());
        $this->assertEquals($affectedAt, $commander->getDAffectation());
        $this->assertNull($commander->getDDeath());
        $this->assertEquals($arrivedAt, $commander->getArrivalDate());
        $this->assertEquals(Commander::LOOT, $commander->getTravelType());
        $this->assertEquals(15, $commander->getLengthTravel());
        $this->assertEquals(1, $commander->getRPlayer());
        $this->assertEquals(18, $commander->getRBase());
        $this->assertEquals('Zambia', $commander->getBaseName());
        $this->assertEquals(23, $commander->getRPlaceDestination());
        $this->assertEquals('Planète rebelle', $commander->getDestinationPlaceName());
        $this->assertTrue($commander->getIsAttacker());
        $this->assertEquals(1, $commander->getPalmares());
        $this->assertEquals(Commander::MOVING, $commander->getStatement());
        $this->assertEquals($updatedAt, $commander->getUMethod());
    }
    
    public function testArmy()
    {
        $commander = new Commander();
        foreach ($this->getSquadronsMock() as $squadron) {
            $commander->addSquadronId($squadron['id']);
            $commander->addArmyInBegin($squadron['data']);
        }
        $commander->setPevInBegin();
        
        $this->assertCount(3, $commander->getArmyInBegin());
        $this->assertEquals(286, $commander->getPevInBegin());
        $this->assertEquals(3, $commander->getSizeArmy());
        $commander->setArmy();
        $this->assertCount(3, $commander->getArmy());
    }
    
    public function getSquadronsMock()
    {
        return [
			[
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
			], [
				'id' => 2,
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
			], [
				'id' => 3,
				'data' => [
					0,
					0,
					0,
					0,
					0,
					0, 
					0, 
					0,
					0,
					1,
					0,
					0,
					'2017-05-16 20:00:00', 
					'2017-05-16 20:00:00'
				]
			]
        ];
    }
}