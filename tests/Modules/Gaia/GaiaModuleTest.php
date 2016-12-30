<?php

namespace Tests\Asylamba\Modules\Gaia;

use Asylamba\Modules\Gaia\GaiaModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\DependencyInjection\Container;

class GaiaModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var GaiaModule **/
	protected $module;
	
	public function setUp()
	{
		$this->module = new GaiaModule($this->getApplicationMock());
	}
	
	public function testGetName()
	{
		$this->assertEquals('Gaia', $this->module->getName());
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