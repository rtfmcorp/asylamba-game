<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;

class Application {
    /** @var Container **/
    protected $container;
	/** @var array **/
	protected $modules;
    
    public function boot()
    {
        $this->container = new Container();
		$this->configure();
		$this->registerModules();
    }
	
	public function configure()
	{
		$configurationFiles = [
			__DIR__ . '/../../../config/parameters.yml',
			__DIR__ . '/../../../config/services.yml'
		];
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
		
		$this->container->setParameter('root_path', realpath('.'));
	}
	
	public function registerModules()
	{
		$this->modules = [
			'ares' => new \Asylamba\Modules\Ares\AresModule($this),
			'artemis' => new \Asylamba\Modules\Artemis\ArtemisModule($this),
			'athena' => new \Asylamba\Modules\Athena\AthenaModule($this),
			'atlas' => new \Asylamba\Modules\Atlas\AtlasModule($this),
			'demeter' => new \Asylamba\Modules\Demeter\DemeterModule($this),
			'gaia' => new \Asylamba\Modules\Gaia\GaiaModule($this),
			'hermes' => new \Asylamba\Modules\Hermes\HermesModule($this),
			'promethee' => new \Asylamba\Modules\Promethee\PrometheeModule($this),
			'zeus' => new \Asylamba\Modules\Zeus\ZeusModule($this)
		];
	}
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
	
	/**
	 * @return array
	 */
	public function getModules()
	{
		return $this->modules;
	}
	
	/**
	 * @param string $name
	 * @return \Asylamba\Classes\Library\Module
	 */
	public function getModule($name)
	{
		return $this->modules[$name];
	}
}