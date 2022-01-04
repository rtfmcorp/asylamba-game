<?php

namespace Asylamba\Classes\Kernel;

use Asylamba\Classes\Daemon\Server;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Process\ProcessManager;
use Asylamba\Classes\Scheduler\CyclicActionScheduler;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
        $this->container->get(ProcessManager::class)->launchProcesses();
		$this->container->get(RealTimeActionScheduler::class)->init();
		$this->container->get(CyclicActionScheduler::class)->init();
        $this->container->get(SectorManager::class)->initOwnershipData();
        
		$server = $this->container->get(Server::class);
		$server->createHttpServer();
		$server->listen();
	}
}
