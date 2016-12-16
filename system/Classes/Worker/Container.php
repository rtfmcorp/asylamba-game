<?php

namespace Asylamba\Classes\Worker;

use ProxyManager\Factory\LazyLoadingGhostFactory;
use Asylamba\Classes\Worker\Manager;
use ProxyManager\Proxy\GhostObjectInterface;

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
    public function get($key, $force = false)
    {
        if (!$this->hasService($key)) {
            throw new \InvalidArgumentException("Service $key not found");
        }
		// In case a proxy service was generated to avoid circular dependency, we have to generate the right service then
		if ($this->services[$key]['instance'] instanceof GhostObjectInterface && $force === true) {
			echo("get<br>");
			var_dump(spl_object_hash($this->services[$key]['instance']));
			echo("<br>");
            $this->loadService($key);
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
    protected function parseArguments($serviceKey, $arguments, $debug = false)
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
				$this->handleCircularDependency($key, $injectedService);
			}
		}
		return $this->get($key);
	}
	
	/**
	 * @param string $key
	 * @param array $dependency
	 */
	public function handleCircularDependency($key, &$dependency)
	{
		// This factory is based on a closure which will be called when a proxy object property will be handled
		// This closure is not called when the service is instanciated
		// It is called when the service is used and its internal properties are used
		$dependency['instance'] = $this->serviceFactory->createProxy($dependency['class'], function (
			GhostObjectInterface $ghostObject,
			string $method,
			array $parameters,
			&$initializer,
			array $properties
		) use ($key, $dependency) {
			// Disable the proxy initialization. It prevents it from executing the closure again
			$initializer = null;
			// For each property of the proxy class, we check if it matchs with the service arguments
			foreach($properties as $property => $value) {
				// First, we convert the property from CamelCase to snake_case
				$snakeCaseProperty = $this->formatToSnakeCase($property);
				// Then we loop through the service arguments looking for a match
				foreach($dependency['arguments'] as $argument) {
					$argumentKey =
						($argument{0} === '@')
						? ltrim($argument, '@')
						: ltrim($argument, '%')
					;
					// Remove module name to match with the property name format
					$data = explode('.', $argumentKey);
					
					if(count($data) > 1) {
						array_shift($data);
					}
					// The last element of the array will always be the property name candidate
					// We remove the module name and replace the remaining dots
					if (str_replace('.', '_', implode('.', $data)) === $snakeCaseProperty) {
						// The $properties array are linked to the proxy object properties
						// It is the proxy properties we are dynamically affecting here
						$properties[$property] =
							($argument{0} === '@')
							? $this->get($argumentKey)
							: $this->getParameter($argumentKey)
						;
						// Avoid useless iterations
						break;
					}
				}
			}
			
			if ($ghostObject instanceof Manager) {
				$ghostObject->newSession();
			}
			
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

