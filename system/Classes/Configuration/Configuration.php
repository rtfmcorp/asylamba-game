<?php

namespace Asylamba\Classes\Configuration;

use Asylamba\Classes\Configuration\Loader\YamlLoader;
use Asylamba\Classes\DependencyInjection\Container;
use Symfony\Component\Config\FileLocatorInterface;

class Configuration {
    /** @var FileLocatorInterface **/
    protected $locator;
    /** @var YamlLoader **/
    protected $loader;
    
    /**
     * @param FileLocatorInterface $locator
     * @param string $format
     */
    public function __construct(FileLocatorInterface $locator) {
        $this->locator = $locator;
        $this->loader = new YamlLoader($locator);
    }
    
    /**
     * @param array $configurationFiles
     */
    public function buildContainer(Container $container, $configurationFiles) {
        foreach($configurationFiles as $configurationFile) {
            $config = $this->loader->load($configurationFile);
			$this->processParameters($container, $config);
			$this->processServices($container, $config);
        }
    }
	
	/**
	 * @param Container $container
	 * @param array $config
	 * @return null
	 */
	protected function processParameters(Container $container, $config)
	{
		if(!isset($config['parameters'])) {
			return;
		}
		foreach($config['parameters'] as $key => $value) {
			$container->setParameter($key, $value);
		}
	}
	
	/**
	 * @param Container $container
	 * @param array $config
	 * @return null
	 */
	protected function processServices(Container $container, $config)
	{
		if(!isset($config['services'])) {
			return;
		}
		foreach($config['services'] as $key => $definition) {
			$container->setServiceDefinition($key, $definition);
		}
	} 
}