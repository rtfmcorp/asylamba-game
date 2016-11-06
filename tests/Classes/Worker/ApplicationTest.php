<?php

namespace Tests\Asylamba\Classes\Worker;

use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;

use Asylamba\Classes\Library\Parser;

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
		$this->assertInstanceOf(Parser::class, $container->get('parser'));
		$this->assertCount(6, $container->getParameters());
	}
}