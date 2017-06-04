<?php

namespace Tests\Asylamba\Classes\DependencyInjection;

use Asylamba\Classes\DependencyInjection\Container;

class ContainerTest extends \PHPUnit\Framework\TestCase {
    /** @var Container **/
    protected $container;
    
    public function setUp()
    {
        $this->container = new Container();
    }
    
    public function testSetServiceDefinition()
    {
        $this->assertFalse($this->container->hasService('test_service'));
        
        $this->container->setServiceDefinition('test_service', [
            'class' => Container::class,
            'arguments' => []
        ]);
        
        $this->assertTrue($this->container->hasService('test_service'));
    }
	
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Service test_service is already defined
	 */
	public function testSetServiceDefinitionTwice()
	{
		$this->container->setServiceDefinition('test_service', [
            'class' => Container::class,
            'arguments' => []
        ]);
		$this->container->setServiceDefinition('test_service', [
            'class' => Container::class,
            'arguments' => []
        ]);
	}
    
    public function testGet() {
		$this->container->setParameter('test_parameter', 'value1');
		$this->container->setServiceDefinition('child_container', [
			'class' => Container::class,
			'arguments' => []
		]);
        $this->container->setServiceDefinition('test_service', [
            'class' => Container::class,
            'arguments' => [
				'%test_parameter',
				'@child_container'
			]
        ]);
        $this->assertInstanceOf(Container::class, $this->container->get('test_service'));
    }
	
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Service child_container not found
     */
	public function testGetWithMissingArguments() {
		$this->container->setParameter('test_parameter', 'value1');
        $this->container->setServiceDefinition('test_service', [
            'class' => Container::class,
            'arguments' => [
				'%test_parameter',
				'@child_container'
			]
        ]);
		$this->container->get('test_service');
	}
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Service test_service not found
     */
    public function testGetUndefinedService()
    {
        $this->container->get('test_service');
    }
    
    public function testSetParameter()
    {
        $this->assertFalse($this->container->hasParameter('test_parameter'));
        
        $this->container->setParameter('test_parameter', 'value1');
        
        $this->assertEquals('value1', $this->container->getParameter('test_parameter'));
        
        $this->container->setParameter('test_parameter', 'value2');
        
        $this->assertEquals('value2', $this->container->getParameter('test_parameter'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter test_parameter not found
     */
    public function testGetMissingParameter()
    {
        $this->container->getParameter('test_parameter');
    }
}