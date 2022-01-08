<?php

namespace Asylamba\Classes\Kernel;

use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Symfony\Component\Messenger\Worker;

class WorkerKernel extends Kernel
{
    public function __construct(
		protected string $name,
		protected string $projectDir,
	) {
		define('PROCESS_NAME', $name);
    }
    
    public function boot(): void
    {
		$this->container = $this->buildContainer();
		$this->container->setParameter('app.name', $this->name);
		$this->container->compile(true);
		$this->init();
    }
	
	public function init(): void
	{
		if (!empty($sentryDsn = $this->container->getParameter('sentry_dsn'))) {
			$this->initSentry($sentryDsn);
		}
		$this->container->get(Database::class)->init($this->container->getParameter('root_path') . '/build/database/structure.sql');
		$this->container->get(EntityManager::class)->init();
		$this->container->get(Worker::class)->run();
	}
}
