<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

use Asylamba\Classes\DependencyInjection\Container;

class Application implements ApplicationInterface {
    /** @var Container **/
    protected $container;
    /** @var array **/
    protected $modules;
    
    public function boot()
    {
		$this->container = new Container();
		$this->container->set('app.container', $this->container);
		$this->configure();
		$this->registerModules();
		$this->init();
    }
	
	public function configure()
	{
		$rootPath = dirname($_SERVER['SCRIPT_NAME']);
		$configurationFiles = [
			$rootPath . '/config/parameters.yml',
			$rootPath . '/config/services.yml'
		];
		$this->container->setParameter('root_path', $rootPath);
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
		$configuration->defineOldConstants();
		
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
	
	public function init()
	{
		if ($this->container->getParameter('environment') === 'dev') {
			set_error_handler(function($errno, $errstr, $errfile, $errline) {
				throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
			});
		}
		$this->container->get('entity_manager')->init();
		$server = $this->container->get('server');
		$server->createHttpServer();
		$server->listen();
	}
}