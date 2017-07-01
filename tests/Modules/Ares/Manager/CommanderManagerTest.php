<?php

namespace Tests\Asylamba\Modules\Ares\Manager;

use Asylamba\Modules\Ares\Manager\CommanderManager;

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Zeus\Model\PlayerBonus;

class CommanderManagerTest extends \PHPUnit\Framework\TestCase
{
    protected $manager;
    
    public function setUp()
    {
        $this->manager = new CommanderManager(
            $this->getEntityManagerMock(),
            $this->getFightManagerMock(),
            $this->getReportManagerMock(),
            $this->getOrbitalBaseManagerMock(),
            $this->getPlayerManagerMock(),
            $this->getPlayerBonusManagerMock(),
            $this->getPlaceManagerMock(),
            $this->getColorManagerMock(),
            $this->getNotificationManagerMock(),
            $this->getSchedulerMock(),
            $this->getEventDispatcherMock(),
            100
        );
    }
    
    public function testGet()
    {
        $commander = $this->manager->get(1);
        
        $this->assertInstanceOf(Commander::class, $commander);
        $this->assertEquals(1, $commander->getId());
        $this->assertEquals('Avla', $commander->getName());
    }
    
    public function testGetBaseCommanders()
    {
        $commanders = $this->manager->getBaseCommanders(1);
        
        $this->assertCount(3, $commanders);
        $this->assertEquals(1, $commanders[0]->getRBase());
        $this->assertEquals(2, $commanders[1]->getId());
        $this->assertEquals('Nievra', $commanders[2]->getName());
    }
    
    /**
     * @dataProvider providePlaces
     * @param Place $place
     * @param array $expected
     */
    public function testCreateVirtualCommander(Place $place, $expected)
    {
        $commander = $this->manager->createVirtualCommander($place);
        
        $this->assertEquals('Null', $commander->getId());
        $this->assertEquals('rebelle', $commander->getName());
        $this->assertEquals(ID_GAIA, $commander->getRPlayer());
		$this->assertEquals($expected['level'], $commander->getLevel());
        $this->assertEquals($expected['pev_in_begin'], $commander->getPevInBegin());
        $this->assertEquals($expected['pev'], $commander->getPev());
        $this->assertCount($expected['nb_squadrons'], $commander->squadronsIds);
    }
    
	/**
	 * @return array
	 */
    public function providePlaces()
    {
        return [
            [
                (new Place())
                ->setPopulation(25)
                ->setCoefHistory(32)
                ->setCoefResources(60)
                ->setPosition(3443)
                ->setDanger(4)
                ->setMaxDanger(15)
                ,
                [
                    'level' => 3,
                    'pev_in_begin' => 18,
					'pev' => 18,
					'nb_squadrons' => 3
                ]
            ],
            [
                (new Place())
                ->setPopulation(60)
                ->setCoefHistory(14)
                ->setCoefResources(70)
                ->setPosition(34565)
                ->setDanger(3)
                ->setMaxDanger(15)
                ,
                [
                    'level' => 4,
                    'pev_in_begin' => 23,
					'pev' => 23,
					'nb_squadrons' => 4
                ]
            ],
            [
                (new Place())
                ->setPopulation(214)
                ->setCoefHistory(14)
                ->setCoefResources(84)
                ->setPosition(2789)
                ->setDanger(12)
                ->setMaxDanger(15)
                ,
                [
					'level' => 5,
                    'pev_in_begin' => 200,
					'pev' => 200,
					'nb_squadrons' => 5
                ]
            ],
            [
                (new Place())
                ->setPopulation(50)
                ->setCoefHistory(20)
                ->setCoefResources(85)
                ->setPosition(26553)
                ->setDanger(15)
                ->setMaxDanger(15)
                ,
                [
					'level' => 4,
                    'pev_in_begin' => 92,
					'pev' => 92,
					'nb_squadrons' => 4
                ]
            ],
        ];
    }
	
	/**
	 * @dataProvider provideMovements
	 * @param array $data
	 * @param array $expected
	 */
	public function testMove($data, $expected)
	{
		$commander = $data['commander'];
		
		$this->manager->move(
			$commander,
			$data['destination_place_id'],
			$data['start_place_id'],
			$data['type'],
			$data['length'],
			$data['duration']
		);
		
		$this->assertEquals(Commander::MOVING, $commander->getStatement());
		$this->assertEquals($data['destination_place_id'], $commander->getRPlaceDestination());
		$this->assertEquals($data['start_place_id'], $commander->getStartPlaceId());
		$this->assertEquals($data['type'], $commander->getTravelType());
		$this->assertEquals($data['length'], $commander->getTravelLength());
		
		$date = new \DateTime($commander->getStartedAt());
		$date->modify('+' . $data['duration'] . 'second');
		$this->assertEquals($commander->getArrivalDate(), $date->format('Y-m-d H:i:s'));
	}
	
	public function provideMovements()
	{
		return [
			[
				[
					'commander' => $this->getCommanderMock(1),
					'start_place_id' => 12,
					'destination_place_id' => 156,
					'type' => Commander::LOOT,
					'length' => 18,
					'duration' => 1800
				],
				[
					
				]
			],
			[
				[
					'commander' => $this->getCommanderMock(2),
					'start_place_id' => 132,
					'destination_place_id' => 546,
					'type' => Commander::LOOT,
					'length' => 24,
					'duration' => 2400
				],
				[
					
				]
			],
			[
				[
					'commander' => $this->getCommanderMock(64),
					'start_place_id' => 16,
					'destination_place_id' => 653,
					'type' => Commander::BACK,
					'length' => 30,
					'duration' => 3800
				],
				[
					
				]
			],
			[
				[
					'commander' => $this->getCommanderMock(12),
					'start_place_id' => 145,
					'destination_place_id' => 236,
					'type' => Commander::COLO,
					'length' => 30,
					'duration' => 3600
				],
				[
					
				]
			],
		];
	}
	
	public function testEndTravel()
	{
		$commander = $this->getCommanderMock(1);
		
		$this->manager->endTravel($commander, Commander::AFFECTED);
		
		$this->assertEquals(Commander::AFFECTED, $commander->getStatement());
		$this->assertNull($commander->getStartPlaceId());
		$this->assertNull($commander->getRPlaceDestination());
		$this->assertNull($commander->getStartedAt());
		$this->assertNull($commander->getArrivalDate());
		$this->assertNull($commander->getTravelType());
		$this->assertNull($commander->getTravelLength());
	}
	
	public function testEndTravelToReserve()
	{
		$commander = $this->getCommanderMock(1);
		
		$this->manager->endTravel($commander, Commander::RESERVE);
		
		$this->assertEquals(Commander::RESERVE, $commander->getStatement());
		$this->assertNull($commander->getStartPlaceId());
		$this->assertNull($commander->getRPlaceDestination());
		$this->assertNull($commander->getStartedAt());
		$this->assertNull($commander->getArrivalDate());
		$this->assertNull($commander->getTravelType());
		$this->assertNull($commander->getTravelLength());
	}
    
    public function getEntityManagerMock()
    {
        $entityManagerMock = $this
            ->getMockBuilder('\Asylamba\Classes\Entity\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $entityManagerMock
            ->expects($this->any())
            ->method('persist')
            ->willReturn(true)
        ;
        $entityManagerMock
            ->expects($this->any())
            ->method('remove')
            ->willReturn(true)
        ;
        $entityManagerMock
            ->expects($this->any())
            ->method('flush')
            ->willReturn(true)
        ;
        $entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnCallback([$this, 'getRepositoryMock'])
        ;
        return $entityManagerMock;
    }
    
    public function getRepositoryMock($repository)
    {
        return $this->{[
            'Asylamba\Modules\Ares\Model\Commander' => 'getCommanderRepositoryMock'
        ][$repository]}();
    }
    
    public function getCommanderRepositoryMock()
    {
        $repositoryMock = $this
            ->getMockBuilder('\Asylamba\Modules\Ares\Repository\CommanderRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback([$this, 'getCommanderMock'])
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('getBaseCommanders')
            ->willReturnCallback([$this, 'getCommandersMock'])
        ;
        return $repositoryMock;
    }
    
    public function getCommanderMock($id)
    {
        return
            (new Commander())
            ->setId($id)
            ->setName('Avla')
			->setRPlaceDestination(15)
			->setStartPlaceId(16)
			->setTravelLength(18)
			->setTravelType(Commander::LOOT)
			->setArrivalDate((new \DateTime('+2 hours'))->format('Y-m-d H:i:s'))
        ;
    }
    
    public function getCommandersMock()
    {
        return [
            (new Commander())
            ->setId(1)
            ->setName('Anvla')
            ->setRBase(1),
            (new Commander())
            ->setId(2)
            ->setName('Zania')
            ->setRBase(1),
            (new Commander())
            ->setId(3)
            ->setName('Nievra')
            ->setRBase(1),
        ];
    }
    
    public function getFightManagerMock()
    {
        $fightManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Ares\Manager\FightManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $fightManagerMock;
    }
    
    public function getReportManagerMock()
    {
        $reportManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Ares\Manager\ReportManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $reportManagerMock;
    }
    
    public function getOrbitalBaseManagerMock()
    {
        $orbitalBaseManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Athena\Manager\OrbitalBaseManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $orbitalBaseManagerMock;
    }
    
    public function getPlayerManagerMock()
    {
        $playerManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Zeus\Manager\PlayerManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $playerManagerMock
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback([$this, 'getPlayerMock'])
        ;
        return $playerManagerMock;
    }
    
    public function getPlayerMock()
    {
        return
            (new Player())
        ;
    }
    
    public function getPlayerBonusManagerMock()
    {
        $playerBonusManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Zeus\Manager\PlayerBonusManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $playerBonusManagerMock;
    }
    
    public function getPlaceManagerMock()
    {
        $placeManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Gaia\Manager\PlaceManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $placeManagerMock
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback([$this, 'getPlaceMock'])
        ;
        return $placeManagerMock;
    }
    
    public function getPlaceMock()
    {
        return
            (new Place())
        ;
    }
    
    public function getColorManagerMock()
    {
        $colorManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Demeter\Manager\ColorManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $colorManagerMock;
    }
    
    public function getNotificationManagerMock()
    {
        $notificationManagerMock = $this
            ->getMockBuilder('Asylamba\Modules\Hermes\Manager\NotificationManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $notificationManagerMock;
    }
    
    public function getSessionMock()
    {
        $sessionMock = $this
            ->getMockBuilder('Asylamba\Classes\Library\Session\SessionWrapper')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $sessionMock;
    }
    
    public function getSchedulerMock()
    {
        $schedulerMock = $this
            ->getMockBuilder('\Asylamba\Classes\Scheduler\RealTimeActionScheduler')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $schedulerMock
            ->expects($this->any())
            ->method('schedule')
            ->willReturn(true)
        ;
        return $schedulerMock;
    }
    
    public function getEventDispatcherMock()
    {
        $eventDispatcherMock = $this
            ->getMockBuilder('Asylamba\Classes\Worker\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $eventDispatcherMock;
    }
}