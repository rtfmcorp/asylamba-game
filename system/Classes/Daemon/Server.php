<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Classes\Router\Router;
use Asylamba\Classes\Library\Http\RequestFactory;
use Asylamba\Classes\Library\Http\ResponseFactory;

use Asylamba\Classes\Scheduler\CyclicActionScheduler;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Worker\Manager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Server
{
    protected bool $shutdown = false;
    protected array $connections;
    protected array $inputs = [];
    protected array $outputs = [];
    protected int $nbUncollectedCycles = 0;

    public function __construct(
		protected Container $container,
		protected Router $router,
		protected RequestFactory $requestFactory,
		protected ResponseFactory $responseFactory,
		protected ClientManager $clientManager,
		protected CyclicActionScheduler $cyclicActionScheduler,
		protected EventDispatcherInterface $eventDispatcher,
		protected iterable $statefulManagers,
		protected int $serverCycleTimeout,
		protected int $port,
		protected int $collectionCyclesNumber
	) {
    }

	public function cleanApplication()
	{
		$this->container->get(EntityManager::class)->clear();

		/** @var Manager $manager */
		foreach($this->statefulManagers as $manager) {
			$manager->save();
			$manager->clean();
		}
	}
	
    public function shutdown(): void
    {
        $this->shutdown = true;
        foreach($this->inputs as $input) {
            fclose($input);
        }
        foreach($this->outputs as $output) {
            fclose($output);
        }
    }

    public function createHttpServer(): void
    {
        $stream = stream_socket_server("tcp://0.0.0.0:{$this->port}", $errno, $errstr);
        if (!$stream) {
            throw new ErrorException("$errstr ($errno)");
        }
        $this->inputs['http_server'] = $stream;
    }

    public function listen()
    {
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
        \gc_disable();
        while ($this->shutdown === false && ($nbUpgradedStreams = \stream_select($inputs, $outputs, $errors, $this->serverCycleTimeout)) !== false) {
            if ($nbUpgradedStreams === 0) {
                $this->prepareStreamsState($inputs, $outputs, $errors);
                continue;
            }
            foreach ($inputs as $stream) {
				$name = \array_search($stream, $this->inputs);
				if ($name === 'http_server') {
					$this->treatHttpInput(\stream_socket_accept($stream));
				}
            }
            $this->prepareStreamsState($inputs, $outputs, $errors);
        }
    }
	
	protected function treatHttpInput($input)
	{
		$client = $request = $response = null;
		try {
			$data = \fread($input, 8192);
			if (empty($data)) {
				\fclose($input);
				return;
			}
			$request = $this->requestFactory->createRequestFromInput($data);
			$this->container->set('app.request', $request);
			if (($client = $this->clientManager->getClient($request)) === null) {
				$client = $this->clientManager->createClient($request);
			}
			$response = $this->router->processRequest($request);
			$this->container->set('app.response', $response);
			$this->responseFactory->processResponse($request, $response, $client);
		} catch (\Exception $ex) {
			$this->eventDispatcher->dispatch($event = new ExceptionEvent($request, $ex), ExceptionEvent::NAME);
			$response = $event->getResponse();
			$this->responseFactory->processResponse($request, $response, $client);
		} catch (\Error $err) {
			$this->eventDispatcher->dispatch($event = new ErrorEvent($request, $err), ErrorEvent::NAME);
			$response = $event->getResponse();
			$this->responseFactory->processResponse($request, $response, $client);
		} finally {
			// @TODO Needs further investigation
			if ($response !== null) {
				\fputs($input, $response->send());
				\fclose($input);
			}
			$this->nbUncollectedCycles++;
			$this->container->get(SessionWrapper::class)->clearWrapper();
		}
	}
    
    protected function prepareStreamsState(array &$inputs, array &$outputs, ?array &$errors)
    {
		$this->cleanApplication();
		
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
		
		$this->cyclicActionScheduler->execute();
		
        if ($this->nbUncollectedCycles > $this->collectionCyclesNumber) {
			$this->container->get(Database::class)->refresh();
			$this->clientManager->clear();
            \gc_collect_cycles();
            $this->nbUncollectedCycles = 0;
        }
    }

	/**
	 * @param resource $input
	 */
    public function addInput(string $name, $input): void
    {
        $this->inputs[$name] = $input;
    }
    
	/**
	 * @param resource $output
	 */
    public function addOutput(string $name, $output): void
    {
        $this->outputs[$name] = $output;
    }
    
    public function removeInput(string $name): void
    {
		unset($this->inputs[$name]);
    }
    
    public function removeOutput($name): void
    {
		unset($this->outputs[$name]);
    }
}
