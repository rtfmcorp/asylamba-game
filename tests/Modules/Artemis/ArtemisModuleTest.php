<?php

namespace Tests\App\Modules\Artemis;

use App\Modules\Artemis\ArtemisModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class ArtemisModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var ArtemisModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new ArtemisModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Artemis', $this->module->getName());
	}
	
	public function getApplicationMock()
	{
		$applicationMock = $this
			->getMockBuilder(Application::class)
			->disableOriginalConstructor()
			->getMock()
		;
		$applicationMock
			->expects($this->any())
			->method('getContainer')
			->willReturnCallback([$this, 'getContainerMock'])
		;
		return $applicationMock;
	}
	
	public function getContainerMock()
	{
		$containerMock = $this
			->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->getMock()
		;
		$containerMock
			->expects($this->any())
			->method('getParameter')
			->willReturn(realpath('.'))
		;
		return $containerMock;
	}
}
