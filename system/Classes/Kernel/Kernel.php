<?php

namespace Asylamba\Classes\Kernel;

use Asylamba\Classes\Library\Module;
use Asylamba\Classes\Worker\Manager;
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
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;

abstract class Kernel implements KernelInterface
{
	protected ContainerBuilder $container;
	protected string $projectDir;
	protected array $modules = [];

	protected function buildContainer(): ContainerBuilder
	{
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->set('container', $containerBuilder);
		$containerBuilder->setParameter('root_path', $this->projectDir);
		$this->buildEventDispatcher($containerBuilder);
		$this->buildMessenger($containerBuilder);

		$this->loadEnvironment($containerBuilder);

		$loader = new YamlFileLoader($containerBuilder, new FileLocator($this->projectDir . '/config/'));
		$loader->load('services.yml');

		$this->registerModules($containerBuilder);
		$containerBuilder->registerForAutoconfiguration(Manager::class)->addTag('app.stateful_manager');

		return $containerBuilder;
	}

	protected function buildEventDispatcher(ContainerBuilder $containerBuilder): void
	{
		$containerBuilder->addCompilerPass(new RegisterListenersPass());
		$containerBuilder->register(EventDispatcherInterface::class, EventDispatcher::class);
		$containerBuilder->setAlias('event_dispatcher', EventDispatcherInterface::class);
	}

	protected function buildMessenger(ContainerBuilder $containerBuilder): void
	{
		$containerBuilder->addCompilerPass(new MessengerPass());
	}

	protected function loadEnvironment(ContainerBuilder $container): void
	{
		foreach(explode(',', getenv('SYMFONY_DOTENV_VARS')) as $key) {
			$container->setParameter(strtolower($key), getenv($key));
		}
	}

	protected function registerModules(ContainerBuilder $containerBuilder): void
	{
		foreach ($this->getRegisteredModules() as $moduleClass) {
			/** @var Module $module */
			$module = new $moduleClass();
			$module->configure($containerBuilder, $this->projectDir);

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

	public function getModules(): array
	{
		return $this->modules;
	}

	public function getModule(string $name): Module
	{
		return $this->modules[$name];
	}

	public function getContainer(): ContainerBuilder
	{
		return $this->container;
	}
}
