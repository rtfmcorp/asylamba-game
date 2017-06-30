<?php

namespace Tests\Asylamba\Classes\Process;

use Asylamba\Classes\Process\LoadBalancer;
use Asylamba\Classes\Process\Process;

use Asylamba\Modules\Gaia\Model\Place;

use Asylamba\Classes\Task\RealTimeTask;
use Asylamba\Classes\Task\CyclicTask;

class LoadBalancerTest extends \PHPUnit\Framework\TestCase
{
    /** @var LoadBalancer **/
    protected $loadBalancer;
    /** @var array **/
    protected $processes = [];
    
    public function setUp()
    {
        $this->loadBalancer = new LoadBalancer($this->getContainerMock());
    }
    
    public function testAffectTask()
    {
        $task = $this->getTaskMock();
        $this->loadBalancer->affectTask($task);
        
        $process = $this->processes[0];
        $this->assertCount(4, $process->getTasks());
        $this->assertEquals(1.006, $process->getExpectedWorkTime());
        $this->assertEquals($task, $process->getTasks()['Process_er14g45hg']);
        $this->assertTrue($process->hasContext([
            'class' => Place::class,
            'id' => 23
        ]));
    }
    
    public function testAffectTaskWithContext()
    {
        $task = $this->getTaskMock();
        $task->setContext([
            'class' => Place::class,
            'id' => 25
        ]);
        $this->loadBalancer->affectTask($task);
        
        $process = $this->processes[1];
        $this->assertCount(3, $process->getTasks());
        $this->assertEquals(324.452, $process->getExpectedWorkTime());
        $this->assertEquals($task, $process->getTasks()['Process_er14g45hg']);
        $this->assertTrue($process->hasContext([
            'class' => Place::class,
            'id' => 25
        ]));
    }
    
    public function testStoreStats()
    {
        $this->loadBalancer->storeStats($this->getTaskMock());
        $this->loadBalancer->storeStats($this->getTaskMock()->setTime(0.25));
        $this->loadBalancer->storeStats($this->getTaskMock());
        $this->loadBalancer->storeStats($this->getTaskMock()->setTime(0.37));
        $this->loadBalancer->storeStats($this->getTaskMock());
        $this->loadBalancer->storeStats($this->getTaskMock());
        
        $stats = $this->loadBalancer->getStats();
        $this->assertCount(5, $stats['ares.commander_manager.uLoot']);
        $this->assertEquals(0.25, $stats['ares.commander_manager.uLoot'][0]);
        $this->assertEquals(0.35, $stats['ares.commander_manager.uLoot'][1]);
        $this->assertEquals(0.37, $stats['ares.commander_manager.uLoot'][2]);
    }
    
    public function testEstimateTime()
    {
        $this->loadBalancer->storeStats($this->getTaskMock());
        $this->loadBalancer->storeStats($this->getTaskMock()->setTime(0.25));
        $this->loadBalancer->storeStats($this->getTaskMock());
        $this->loadBalancer->storeStats($this->getTaskMock()->setTime(0.37));
        
        $task = $this->getTaskMock();
        $this->loadBalancer->estimateTime($task);
        
        $this->assertEquals(0.33, $task->getEstimatedTime());
    }
    
    public function getContainerMock()
    {
        $containerMock = $this
            ->getMockBuilder('Asylamba\Classes\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $containerMock
            ->expects($this->any())
            ->method('getParameter')
            ->willReturn(5)
        ;
        $containerMock
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback([$this, 'getProcessManagerMock'])
        ;
        return $containerMock;
    }
    
    public function getProcessManagerMock()
    {
        $processManagerMock = $this
            ->getMockBuilder('Asylamba\Classes\Process\ProcessManager')
            ->disableOriginalConstructor()
            // Avoid to mock the affectTask method
            ->setMethods(['getProcesses', 'getGateway'])
            ->getMock()
        ;
        $processManagerMock
            ->expects($this->any())
            ->method('getProcesses')
            ->willReturnCallback([$this, 'getProcessesMock'])
        ;
        $processManagerMock
            ->expects($this->any())
            ->method('getGateway')
            ->willReturnCallback([$this, 'getGatewayMock'])
        ;
        return $processManagerMock;
    }
    
    public function getGatewayMock()
    {
        $gatewayMock = $this
            ->getMockBuilder('Asylamba\Classes\Process\ProcessGateway')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $gatewayMock
            ->expects($this->any())
            ->method('writeTo')
            ->willReturn(true)
        ;
        return $gatewayMock;
    }
    
    public function getProcessesMock()
    {
        if (count($this->processes) === 0) {
            $this->processes = [
                (new Process())
                ->setName('Process 1')
                ->addContext([
                    'class' => Place::class,
                    'id' => 1
                ])
                ->addContext([
                    'class' => Place::class,
                    'id' => 16
                ])
                ->addTask(
                    (new RealTimeTask())
                    ->setId('process_0325sd21')
                    ->setManager('athena.orbital_base_manager')
                    ->setMethod('uBuilding')
                    ->setDate((new \DateTime('-2 minutes'))->format('Y-m-d H:i:s'))
                    ->setContext([
                        'class' => Place::class,
                        'id' => 1
                    ])
                    ->setObjectId(156)
                    ->setTime(0.002)
                )
                ->addTask(
                    (new RealTimeTask())
                    ->setId('process_0387221')
                    ->setManager('athena.orbital_base_manager')
                    ->setMethod('uBuilding')
                    ->setDate((new \DateTime('-1 minutes'))->format('Y-m-d H:i:s'))
                    ->setContext([
                        'class' => Place::class,
                        'id' => 1
                    ])
                    ->setObjectId(157)
                    ->setTime(0.002)
                )
                ->addTask(
                    (new RealTimeTask())
                    ->setId('process_032572371')
                    ->setManager('athena.orbital_base_manager')
                    ->setMethod('uBuilding')
                    ->setDate((new \DateTime('-30 seconds'))->format('Y-m-d H:i:s'))
                    ->setContext([
                        'class' => Place::class,
                        'id' => 16
                    ])
                    ->setObjectId(189)
                    ->setTime(0.002)
                )
                ->setExpectedWorkTime(0.006),
                (new Process())
                ->setName('Process 2')
                ->addContext([
                    'class' => Place::class,
                    'id' => 25
                ])
                ->addTask(
                    (new CyclicTask())
                    ->setId('process_032542722')
                    ->setManager('gaia.place_manager')
                    ->setMethod('updateNpcPlaces')
                    ->setEstimatedTime(323.45)
                )
                ->addTask(
                    (new RealTimeTask())
                    ->setId('process_03257215271')
                    ->setManager('athena.orbital_base_manager')
                    ->setMethod('uBuilding')
                    ->setDate((new \DateTime('-3 minutes'))->format('Y-m-d H:i:s'))
                    ->setContext([
                        'class' => Place::class,
                        'id' => 25
                    ])
                    ->setObjectId(192)
                    ->setTime(0.002)
                )
                ->setExpectedWorkTime(323.452),
            ];
        }
        return $this->processes;
    }
    
    public function getTaskMock()
    {
        return
            (new RealTimeTask())
            ->setId('Process_er14g45hg')
            ->setManager('ares.commander_manager')
            ->setMethod('uLoot')
            ->setContext([
                'class' => Place::class,
                'id' => 23
            ])
            ->setEstimatedTime(0.30)
            ->setTime(0.35)
        ;
    }
}