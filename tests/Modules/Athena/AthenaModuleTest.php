<?php

namespace Tests\App\Modules\Athena;

use App\Modules\Athena\AthenaModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class AthenaModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var AthenaModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new AthenaModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Athena', $this->module->getName());
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
