<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;

class Application {
    /** @var Container **/
    protected $container;
    
    public function boot()
    {
        $this->container = new Container();
		$this->configure();
    }
	
	public function configure()
	{
		$configurationFiles = [
			__DIR__ . '/../../../config/parameters.yml',
			__DIR__ . '/../../../config/services.yml'
		];
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
	}
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}