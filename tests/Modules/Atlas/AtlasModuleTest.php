<?php

namespace Tests\Asylamba\Modules\Atlas;

use Asylamba\Modules\Atlas\AtlasModule;
use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

class AtlasModuleTest extends \PHPUnit_Framework_TestCase
{
	/** @var AtlasModule **/
	protected $module;
	
	public function setUp()
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