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
	
	public function defineOldConstants()
	{
		# définition des ROOT
		define('SYSTEMR',		__DIR__ . '/../../');
		define('CLASSES',       SYSTEMR . 'Classes/');

		define('MODULES', 		SYSTEMR . 'Modules/');
		define('LIB', 			CLASSES . 'lib/');
		define('CONFIG', 		SYSTEMR . 'config/');
		define('EVENT', 		SYSTEMR . 'event/');

		define('INSCRIPTION', 	SYSTEMR . 'inscription/');
		define('CONNECTION', 	SYSTEMR . 'connection/');

		define('ACTION', 		SYSTEMR . 'action/std/');
		define('AJAX', 			SYSTEMR . 'action/ajax/');

		define('API', 			SYSTEMR . 'api/');
		define('SCRIPT',		SYSTEMR . 'script/');
		define('BUFFER',		SYSTEMR . 'buffer/');

		define('TEMPLATE', 		SYSTEMR . 'views/templates/');
		define('PAGES', 		SYSTEMR . 'views/pages/');
		define('COMPONENT', 	SYSTEMR . 'views/components/');

		# inclusion des fichiers de configurations
		include CONFIG . 'app.config.local.php';
		include CONFIG . 'app.config.global.php';
	}
}