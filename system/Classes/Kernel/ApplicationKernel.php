<?php

namespace Asylamba\Classes\Kernel;

use Asylamba\Classes\Daemon\Server;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Event\ServerInitEvent;
use Asylamba\Classes\Scheduler\CyclicActionScheduler;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

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

		$this->buildTwig();

		$this->init();
    }

	public function buildTwig(): void
	{
		$loader = new \Twig\Loader\FilesystemLoader($this->projectDir.'/templates');
		$twig = new Environment($loader, [
			'cache' => $this->projectDir . '/var/cache/twig',
		]);
		$this->container->set(Environment::class, $twig);

		foreach (['app_description', 'app_subname', 'media', 'css'] as $variable) {
			$twig->addGlobal($variable, $this->container->getParameter($variable));
		}
	}

	public function init(): void
	{
		if (!empty($sentryDsn = $this->container->getParameter('sentry_dsn'))) {
			$this->initSentry($sentryDsn);
		}
		$this->container->get(Database::class)->init($this->container->getParameter('root_path') . '/build/database/structure.sql');
		$this->container->get(EntityManager::class)->init();
		$this->container->get(CyclicActionScheduler::class)->init();
		$this->container->get(SectorManager::class)->initOwnershipData();

		$this->bootSymfonyKernel();
	}

	public function bootSymfonyKernel()
	{
		$loader = new YamlFileLoader(new FileLocator($this->projectDir . '/config/routes/'));
		$routes = $loader->load('routes.yaml');
		$matcher = new UrlMatcher($routes, new RequestContext());

		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $this->container->get(EventDispatcherInterface::class);
		$eventDispatcher->dispatch(new ServerInitEvent(), ServerInitEvent::NAME);
		$eventDispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

		$request = Request::createFromGlobals();
		$this->container->set(Request::class, $request);

		// create your controller and argument resolvers
		$controllerResolver = new ControllerResolver();
		$argumentResolver = new ArgumentResolver();

		// instantiate the kernel
		$kernel = new HttpKernel($eventDispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

		// actually execute the kernel, which turns the request into a response
		// by dispatching events, calling a controller, and returning the response
		$response = $kernel->handle($request);

		// send the headers and echo the content
		$response->send();

		// trigger the kernel.terminate event
		$kernel->terminate($request, $response);
	}
}
