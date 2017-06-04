<?php

namespace Asylamba\Classes\DependencyInjection;

use Asylamba\Classes\Worker\Manager;

class Container {
	/** @var ServiceInjector **/
	protected $serviceInjector;
    /** @var array **/
    protected $services = [];
    /** @var array **/
    protected $parameters = [];

	public function __construct()
	{
		$this->serviceInjector = new ServiceInjector($this);
	}
	
	public function cleanApplication()
	{
		$this->get('entity_manager')->clear();
		
		foreach($this->services as $service) {
			if ($service['instance'] instanceof Manager) {
				$service['instance']->save();
				$service['instance']->clean();
			}
		}
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
			$this->serviceInjector->parseTags($key, $definition['tags']);
		}
    }
	
	/**
	 * @param string $key
	 * @return array
	 */
	public function &getServiceDefinition($key) {
		return $this->services[$key];
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
            $this->serviceInjector->loadService($key, $this->services[$key]);
        }
        return $this->services[$key]['instance'];
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
	
	public function save()
	{
		foreach($this->services as $service)
		{
			if(!$service['instance'] instanceof Manager) {
				continue;
			}
			$service['instance']->save();
		}
	}
	
	function formatToSnakeCase($property) {
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $property, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
}

