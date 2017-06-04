<?php

namespace Tests\Asylamba\Classes\Worker;

use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

use Asylamba\Modules\Ares\AresModule;

class ApplicationTest extends \PHPUnit\Framework\TestCase {
	protected $application;
	
	public function setUp()
	{
		$this->application = new Application();
	}
	
	public function testBoot()
	{
		$this->markTestIncomplete('Need to implement a complete configuration system with handling of the test environment');
		$this->application->boot();
		
		$container = $this->application->getContainer();
		$this->assertInstanceOf(Container::class, $container);
		$this->assertCount(74, $container->getParameters());
		$this->assertCount(9, $this->application->getModules());
		$this->assertInstanceOf(AresModule::class, $this->application->getModule('ares'));
	}
}