<?php

namespace Asylamba\Classes\Kernel;

use Asylamba\Classes\Daemon\Server;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Event\ServerInitEvent;
use Asylamba\Classes\Scheduler\CyclicActionScheduler;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationKernel extends Kernel
{
    protected ContainerBuilder $container;

	public function __construct(protected string $projectDir)
	{
	}
    
    public function boot(): void
    {
		define('PROCESS_NAME', 'application');

		$this->container = $this->buildContainer();
		$this->container->compile(true);
		$this->init();
    }
	
	public function init(): void
	{
		$this->container->get(Database::class)->init($this->container->getParameter('root_path') . '/build/database/structure.sql');
		$this->container->get(EntityManager::class)->init();
		$this->container->get(CyclicActionScheduler::class)->init();
        $this->container->get(SectorManager::class)->initOwnershipData();

		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $this->container->get(EventDispatcherInterface::class);
		$eventDispatcher->dispatch(new ServerInitEvent(), ServerInitEvent::NAME);

		$server = $this->container->get(Server::class);
		$server->createHttpServer();
		$server->listen();
	}
}
