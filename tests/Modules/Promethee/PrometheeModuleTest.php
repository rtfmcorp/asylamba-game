<?php

namespace Tests\Asylamba\Modules\Promethee;

use Asylamba\Modules\Promethee\PrometheeModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

class PrometheeModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var PrometheeModule **/
	protected $module;
	
	public function setUp()
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