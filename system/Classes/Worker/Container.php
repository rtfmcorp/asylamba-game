<?php

namespace Asylamba\Classes\Worker;

class Container {
    /** @var array **/
    protected $services = [];
    /** @var array **/
    protected $parameters = [];

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
            'class' => $definition['class'],
            'arguments' => (isset($definition['arguments'])) ? $definition['arguments'] : [],
            'instance' => null
        ];
    }
    
    /**
     * @param string $key
     * @param object $service
     * @return \Asylamba\Classes\Worker\Container
     */
    public function set($key, $service)
    {
        $this->services[$key] = $service;
        
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
        $args = $this->parseArguments($service['arguments']);
		
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
     * @param array $arguments
     * @return array
     */
    protected function parseArguments($arguments)
    {
        $args = [];
        foreach ($arguments as $argument) {
            if ($argument{0} === '%') {
                $args[] = $this->getParameter(ltrim($argument, '%'));
                continue;
            }
            if ($argument{0} === '@') {
                $args[] = $this->get(ltrim($argument, '@'));
                continue;
            }
			$args[] = $argument;
        }
        return $args;
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

