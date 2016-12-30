<?php

namespace Tests\Asylamba\Modules\Ares;

use Asylamba\Modules\Ares\AresModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\DependencyInjection\Container;

class AresModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var AresModule **/
	protected $module;
	
	public function setUp()
	{
		$this->module = new AresModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Ares', $this->module->getName());
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