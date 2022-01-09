<?php

namespace Tests\App\Modules\Demeter;

use App\Modules\Demeter\DemeterModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class DemeterModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var DemeterModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new DemeterModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Demeter', $this->module->getName());
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
