<?php

namespace Tests\Asylamba\Modules\Hermes;

use Asylamba\Modules\Hermes\HermesModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\DependencyInjection\Container;

class HermesModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var HermesModule **/
	protected $module;
	
	public function setUp()
	{
		$this->module = new HermesModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Hermes', $this->module->getName());
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