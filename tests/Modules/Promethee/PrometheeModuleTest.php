<?php

namespace Tests\Asylamba\Modules\Promethee;

use App\Modules\Promethee\PrometheeModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class PrometheeModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var PrometheeModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new PrometheeModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Promethee', $this->module->getName());
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
