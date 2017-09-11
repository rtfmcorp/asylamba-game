<?php

namespace Tests\Asylamba\Classes\Process;

use Asylamba\Classes\Process\ProcessManager;
use Asylamba\Classes\Process\Process;

class ProcessManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ProcessManager **/
    protected $manager;
    
    public function setUp()
    {
        $this->manager = new ProcessManager(
            $this->getServerMock(),
            $this->getMemoryManagerMock(),
            $this->getGatewayMock(),
            '/',
            'tests/logs',
            3
        );
    }
    
    public function testAddProcess()
    {
        $process = $this->manager->addProcess();
        
        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals('process_1', $process->getName());
        $this->assertInstanceOf('DateTime', $process->getStartTime());
        
        $inputMetaData = stream_get_meta_data($process->getInput());
        
        $this->assertFalse($inputMetaData['blocked']);
        $this->assertEquals('r', $inputMetaData['mode']);
        
        $outputMetaData = stream_get_meta_data($process->getOutput());
        
        $this->assertTrue($outputMetaData['blocked']);
        $this->assertEquals('w', $outputMetaData['mode']);
    }
    
    public function getServerMock()
    {
        $serverMock = $this
            ->getMockBuilder('Asylamba\Classes\Daemon\Server')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $serverMock;
    }
    
    public function getMemoryManagerMock()
    {
        $memoryManagerMock = $this
            ->getMockBuilder('Asylamba\Classes\Memory\MemoryManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $memoryManagerMock;
    }
    
    public function getGatewayMock()
    {
        $gatewayMock = $this
            ->getMockBuilder('Asylamba\Classes\Process\ProcessGateway')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $gatewayMock;
    }
}
