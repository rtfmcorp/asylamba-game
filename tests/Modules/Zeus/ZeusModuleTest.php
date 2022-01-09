<?php

namespace Tests\Asylamba\Modules\Zeus;

use App\Modules\Zeus\ZeusModule;
use App\Classes\Worker\Application;
use App\Classes\DependencyInjection\Container;

class ZeusModuleTest extends \PHPUnit\Framework\TestCase
{
	/** @var ZeusModule **/
	protected $module;
	
	public function setUp(): void
	{
		$this->module = new ZeusModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Zeus', $this->module->getName());
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
