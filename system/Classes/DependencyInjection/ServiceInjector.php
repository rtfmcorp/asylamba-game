<?php

namespace Asylamba\Classes\DependencyInjection;

use Asylamba\Classes\Worker\Manager;

use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class ServiceInjector {
	/** @var Container **/
	protected $container;
	/** @var LazyLoadingGhostFactory **/
	protected $serviceFactory;
	/** @var array **/
	protected $pile = [];

	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->serviceFactory = new LazyLoadingGhostFactory();
	}
	
    /**
     * @param string $key
	 * @param array &$service
     */
    public function loadService($key, &$service)
    {
		// This pile is for a safety check to ensure that a dependency service will not call another one
		// which needs the primary service that the injector is currently instanciating
		// Each instanciating service key is added to the pile and removed when instanciated
		// If an argument key is in the pile, the dependency will be faked with a Ghost object
		$this->pile[] = "@$key";
        $args = $this->parseArguments($service['arguments']);
		$this->instanciateService($service, $args);
		array_pop($this->pile);
    }
	
	/**
	 * @param array $service
	 * @return object
	 */
	protected function instanciateService(&$service, $args)
	{
		$service['instance'] = 
			(count($args) === 0)
			? new $service['class']()
			: new $service['class'](...$args)
		;
	}
    
	/**
	 * @param string $serviceKey
	 * @param array $tags
	 */
	public function parseTags($serviceKey, $tags)
	{
		foreach($tags as $tag) {
			if ($tag['type'] === 'event_listener') {
				$this->container->get('event_dispatcher')->registerListener([
					'key' => $serviceKey,
					'method' => $tag['method']
				], $tag['event']);
			}
		}
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
                $args[] = $this->container->getParameter(ltrim($argument, '%'));
                continue;
            }
            if ($argument{0} === '@') {
				$args[] = $this->prepareServiceInjection($argument);
                continue;
            }
			$args[] = $argument;
        }
        return $args;
    }
	
	/**
	 * @param array $argument
	 * @return object
	 */
	protected function prepareServiceInjection($argument)
	{
		$key = ltrim($argument, '@');
		$injectedService = &$this->container->getServiceDefinition($key);
			
		if(!isset($injectedService['arguments'])) {
			return $this->container->get($key);
		}
		// This part of the code ensures that there is no circular dependency between two services
		// If one is found, 
		foreach($injectedService['arguments'] as $argument) {
			if (in_array($argument, $this->pile)) {
				$this->handleCircularDependency($key, $injectedService);
			}
		}
		return $this->container->get($key);
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
		) use ($key, &$dependency) {
			
			// Disable the proxy initialization. It prevents it from executing the closure again
			$initializer = null;
			// For each property of the proxy class, we check if it matchs with the service arguments
			foreach($properties as $property => $value) {
				// First, we convert the property from CamelCase to snake_case
				$snakeCaseProperty = $this->container->formatToSnakeCase($property);
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
							? $this->container->get($argumentKey)
							: $this->container->getParameter($argumentKey)
						;
						// Avoid useless iterations
						break;
					}
				}
			}
			
			if ($ghostObject instanceof Manager) {
				$ghostObject->newSession();
			}
			$dependency['instance'] = $ghostObject;
			return true; // confirm that initialization occurred correctly
		});
	}
}
