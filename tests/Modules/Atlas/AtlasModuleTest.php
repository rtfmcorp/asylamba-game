<?php

namespace Tests\App\Modules\Atlas;

use App\Modules\Atlas\AtlasModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class AtlasModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var AtlasModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new AtlasModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Atlas', $this->module->getName());
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
