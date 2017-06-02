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
        $this->assertEquals(18, $commander->getPevInBegin());
        $this->assertEquals(18, $commander->getPev());
        $this->assertCount(3, $commander->squadronsIds);
    }
    
    public function providePlaces()
    {
        return [
            [
                (new Place())
                ->setPopulation('25')
                ->setCoefHistory(32)
                ->setCoefResources(60)
                ->setPosition(3443)
                ->setDanger(4)
                ->setMaxDanger(15)
                ,
                [
                    
                ]
            ]
        ];
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