<?php

namespace Asylamba\Classes\Worker;

use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

use Asylamba\Classes\Exception\CriticalException;

class Container {
	/** @var LazyLoadingGhostFactory **/
	protected $serviceFactory;
    /** @var array **/
    protected $services = [];
    /** @var array **/
    protected $parameters = [];

	public function __construct()
	{
		$this->serviceFactory = new LazyLoadingGhostFactory();
	}
	
    /**
     * @param string $key
     * @param array $definition
     * @throws \InvalidArgumentException
     */
    public function setServiceDefinition($key, $definition)
    {
        if ($this->hasService($key)) {
            throw new \InvalidArgumentException("Service $key is already defined");
        }
        $this->services[$key] = [
			'class' => ($definition['class']{0} === '%') ? $this->getParameter(ltrim($definition['class'], '%')) : $definition['class'],
            'arguments' => (isset($definition['arguments'])) ? $definition['arguments'] : [],
			'tags' => [],
            'instance' => null
        ];
		if (isset($definition['tags'])) {
			$this->services[$key]['tags'] = $definition['tags'];
			$this->parseTags($key, $definition['tags']);
		}
    }
    
    /**
     * @param string $key
     * @param object $service
     * @return \Asylamba\Classes\Worker\Container
     */
    public function set($key, $service)
    {
		// This way of doing things ensures that we do not erase service previous data
        $this->services[$key]['class'] = get_class($service);
		$this->services[$key]['instance'] = $service;
        
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasService($key)
    {
        return isset($this->services[$key]);
    }
    
    /**
     * @param string $key
     * @return object
     * @throws \InvalidArgumentException
     */
    public function get($key)
    {
        if (!$this->hasService($key)) {
            throw new \InvalidArgumentException("Service $key not found");
        }
        if (empty($this->services[$key]['instance'])) {
            $this->loadService($key);
        }
        return $this->services[$key]['instance'];
    }
    
    /**
     * @param string $key
     */
    protected function loadService($key)
    {
        $service = &$this->services[$key];
        $args = $this->parseArguments($key, $service['arguments']);
		$service['instance'] = $this->instanciateService($service, $args);
    }
	
	/**
	 * @param array $service
	 * @return object
	 */
	protected function instanciateService($service, $args)
	{
		if (count($args) === 0) {
			return new $service['class']();
		}
		return new $service['class'](...$args);
	}
	
	/**
	 * @param string $serviceKey
	 * @param array $tags
	 */
	public function parseTags($serviceKey, $tags)
	{
		foreach($tags as $tag) {
			if ($tag['type'] === 'event_listener') {
				$this->get('event_dispatcher')->registerListener([
					'key' => $serviceKey,
					'method' => $tag['method']
				], $tag['event']);
			}
		}
	}
    
    /**
	 * @param string $serviceKey
     * @param array $arguments
     * @return array
     */
    protected function parseArguments($serviceKey, $arguments)
    {
        $args = [];
        foreach ($arguments as $argument) {
            if ($argument{0} === '%') {
                $args[] = $this->getParameter(ltrim($argument, '%'));
                continue;
            }
            if ($argument{0} === '@') {
				$args[] = $this->prepareServiceInjection($serviceKey, $argument);
                continue;
            }
			$args[] = $argument;
        }
        return $args;
    }
	
	/**
	 * 
	 * @param string $serviceKey
	 * @param array $argument
	 * @return object
	 */
	protected function prepareServiceInjection($serviceKey, $argument)
	{
		$key = ltrim($argument, '@');
		$injectedService = &$this->services[$key];
		
		if(!isset($injectedService['arguments'])) {
			return $this->get($key);
		}
		// This part of the code ensures that there is no circular dependency between two services
		// If one is found, 
		foreach($injectedService['arguments'] as $argument) {
			if ($argument === "@$serviceKey") {
				$this->handleCircularDependency($injectedService);
			}
		}
		return $this->get($key);
	}
	
	/**
	 * @param array $dependency
	 */
	public function handleCircularDependency(&$dependency)
	{
		$dependency['instance'] = $this->serviceFactory->createProxy($dependency['class'], function (
			GhostObjectInterface $ghostObject,
			string $method,
			array $parameters,
			& $initializer,
			array $properties
		) {
			$initializer   = null; // disable initialization

			// load data and modify the object here

			// you may also call methods on the object, but remember that
			// the constructor was not called yet:

			return true; // confirm that initialization occurred correctly
		});
	}
    
    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
        
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasParameter($key)
    {
        return isset($this->parameters[$key]);
    }
    
    /**
     * @param string $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getParameter($key)
    {
        if(!$this->hasParameter($key)) {
            throw new \InvalidArgumentException("Parameter $key not found");
        }
        return $this->parameters[$key];
    }
    
    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

