<?php

namespace Asylamba\Classes\Worker;


use Asylamba\Classes\Daemon\WorkerServer;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Module;
use Asylamba\Modules\Ares\AresModule;
use Asylamba\Modules\Artemis\ArtemisModule;
use Asylamba\Modules\Athena\AthenaModule;
use Asylamba\Modules\Atlas\AtlasModule;
use Asylamba\Modules\Demeter\DemeterModule;
use Asylamba\Modules\Gaia\GaiaModule;
use Asylamba\Modules\Hephaistos\HephaistosModule;
use Asylamba\Modules\Hermes\HermesModule;
use Asylamba\Modules\Promethee\PrometheeModule;
use Asylamba\Modules\Zeus\ZeusModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Worker implements ApplicationInterface
{
    protected ContainerBuilder $container;
    protected array $modules;

    public function __construct(
		protected string $name,
		protected string $projectDir,
	) {
		define('PROCESS_NAME', $name);
    }
    
    public function boot()
    {
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->setParameter('root_path', $this->projectDir);
		$containerBuilder->set('container', $containerBuilder);
		$this->loadEnvironment($containerBuilder);
		$loader = new YamlFileLoader($containerBuilder, new FileLocator($this->projectDir . '/config/'));
		$loader->load('services.yml');

		$this->container = $containerBuilder;
		$this->container->setParameter('app.name', $this->name);
		$this->registerModules();
		$containerBuilder->registerForAutoconfiguration(Manager::class)->addTag('app.stateful_manager');
		$containerBuilder->compile(true);
		$this->init();
    }

	public function loadEnvironment(ContainerBuilder $container): void
	{
		foreach(explode(',', getenv('SYMFONY_DOTENV_VARS')) as $key) {
			$container->setParameter(strtolower($key), getenv($key));
		}
	}

	public function registerModules()
	{
		foreach ($this->getRegisteredModules() as $moduleClass) {
			/** @var Module $module */
			$module = new $moduleClass();
			$module->configure($this->container, $this->projectDir);

			$this->modules[strtolower($module->getName())] = $module;
		}
	}

	protected function getRegisteredModules(): array
	{
		return [
			AresModule::class,
			ArtemisModule::class,
			AthenaModule::class,
			AtlasModule::class,
			DemeterModule::class,
			GaiaModule::class,
			HephaistosModule::class,
			HermesModule::class,
			PrometheeModule::class,
			ZeusModule::class,
		];
	}

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
	
	public function getModules(): array
	{
		return $this->modules;
	}

	public function getModule(string $name): Module
	{
		return $this->modules[$name];
	}
	
	public function init()
	{
		$this->container->get(Database::class)->init($this->container->getParameter('root_path') . '/build/database/structure.sql');
		$this->container->get(EntityManager::class)->init();
		$this->container->get(WorkerServer::class)->listen();
	}
}
