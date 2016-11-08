<?php

namespace Tests\Asylamba\Classes\Worker;

use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

use Asylamba\Modules\Ares\AresModule;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	
	public function setUp()
	{
		$this->application = new Application();
	}
	
	public function testBoot()
	{
		$this->application->boot();
		
		$container = $this->application->getContainer();
		$this->assertInstanceOf(Container::class, $container);
		$this->assertCount(74, $container->getParameters());
		$this->assertCount(9, $this->application->getModules());
		$this->assertINstanceOf(AresModule::class, $this->application->getModule('ares'));
	}
}