<?php

namespace Tests\Asylamba\Classes\Worker;

use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
	protected $application;
	
	public function setUp()
	{
		$this->application = new Application();
	}
	
	public function testBoot()
	{
		$this->application->boot();
		
		$this->assertInstanceOf(Container::class, $this->application->getContainer());
	}
}