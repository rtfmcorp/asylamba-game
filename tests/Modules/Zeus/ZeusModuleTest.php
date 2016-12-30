<?php

namespace Tests\Asylamba\Modules\Zeus;

use Asylamba\Modules\Zeus\ZeusModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\DependencyInjection\Container;

class ZeusModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var ZeusModule **/
	protected $module;
	
	public function setUp()
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