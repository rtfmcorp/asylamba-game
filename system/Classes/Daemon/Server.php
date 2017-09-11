<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Router\Router;
use Asylamba\Classes\Library\Http\RequestFactory;
use Asylamba\Classes\Library\Http\ResponseFactory;
use Asylamba\Classes\Daemon\ClientManager;

use Asylamba\Classes\Scheduler\RealTimeActionScheduler;
use Asylamba\Classes\Scheduler\CyclicActionScheduler;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Process\ProcessManager;
use Asylamba\Classes\Task\TaskManager;

class Server
{
    /** @var Container **/
    protected $container;
    /** @var Router **/
    protected $router;
    /** @var RequestFactory **/
    protected $requestFactory;
    /** @var ResponseFactory **/
    protected $responseFactory;
    /** @var ClientManager **/
    protected $clientManager;
    /** @var RealTimeActionScheduler **/
    protected $realTimeActionScheduler;
    /** @var CyclicActionScheduler **/
    protected $cyclicActionScheduler;
    /** @var ProcessManager **/
    protected $processManager;
    /** @var TaskManager **/
    protected $taskManager;
    /** @var int **/
    protected $serverCycleTimeout;
    /** @var int **/
    protected $port;
    /** @var boolean **/
    protected $shutdown = false;
    /** @var array **/
    protected $connections;
    /** @var array **/
    protected $inputs = [];
    /** @var array **/
    protected $outputs = [];
    /** @var int **/
    protected $nbUncollectedCycles = 0;
    /** @var int **/
    protected $collectionCyclesNumber;
    /** @var array **/
    protected $ws = [];

    /**
     * @param Container $container
     * @param int $serverCycleTimeout
     * @param int $port
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->requestFactory = $container->get('request_factory');
        $this->responseFactory = $container->get('response_factory');
        $this->clientManager = $container->get('client_manager');
        $this->connectionHandler = $container->get('ws_connection_handler');
        $this->realTimeActionScheduler = $container->get('realtime_action_scheduler');
        $this->cyclicActionScheduler = $container->get('cyclic_action_scheduler');
        $this->serverCycleTimeout = $container->getParameter('server_cycle_timeout');
        $this->port = $container->getParameter('server_port');
        $this->collectionCyclesNumber = $container->getParameter('server_collection_cycles_number');
    }
    
    public function shutdown()
    {
        $this->shutdown = true;
        foreach ($this->inputs as $input) {
            fclose($input);
        }
        foreach ($this->outputs as $output) {
            fclose($output);
        }
    }

    public function createHttpServer()
    {
        $stream = stream_socket_server("tcp://0.0.0.0:{$this->port}", $errno, $errstr);
        if (!$stream) {
            throw new ErrorException("$errstr ($errno)");
        }
        $this->inputs['http_server'] = $stream;
    }

    public function listen()
    {
        $this->processManager = $this->container->get('process_manager');
        $this->taskManager = $this->container->get('task_manager');
        $inputs = array_merge($this->inputs, $this->ws);
        $outputs = $this->outputs;
        $errors = null;
        gc_disable();
        while ($this->shutdown === false && ($nbUpgradedStreams = stream_select($inputs, $outputs, $errors, $this->serverCycleTimeout)) !== false) {
            if ($nbUpgradedStreams === 0) {
                $this->prepareStreamsState($inputs, $outputs, $errors);
                continue;
            }
            foreach ($inputs as $stream) {
                $name = array_search($stream, $inputs);
                if ($name === 'http_server') {
                    $this->treatHttpInput(stream_socket_accept($stream));
                } elseif (($parts = explode('-', $name))[0] === 'client') {
                    if (($client = $this->clientManager->getClientById($parts[1])) === null) {
                        $this->connectionHandler->close($stream);
                        unset($this->ws[$name]);
                    } elseif (!$this->connectionHandler->handle($client)) {
                        unset($this->ws[$name]);
                    }
                } else {
                    $this->treatProcessInput($name);
                }
            }
            $this->prepareStreamsState($inputs, $outputs, $errors);
        }
    }
    
    protected function treatHttpInput($input)
    {
        $client = $request = $response = null;
        try {
            $data = fread($input, 8192);
            if (empty($data)) {
                fclose($input);
                return;
            }
            $request = $this->requestFactory->createRequestFromInput($data);
            $this->container->set('app.request', $request);
            
            if (($client = $this->clientManager->getClient($request)) === null) {
                $client = $this->clientManager->createClient($request);
            }
            if (($wsKey = $request->headers->get('sec-websocket-key')) !== null) {
                $this->connectionHandler->handshake($input, $wsKey);
        
                $this->ws['client-' . $client->getId()] = $input;

                $this->clientManager->assignWsConnection($client, $input);
                
                return;
            }
            $response = $this->router->processRequest($request);
            $this->container->set('app.response', $response);
            $this->responseFactory->processResponse($request, $response, $client);
        } catch (\Exception $ex) {
            $this->container->get('event_dispatcher')->dispatch($event = new ExceptionEvent($request, $ex));
            $response = $event->getResponse();
            $this->responseFactory->processResponse($request, $response, $client);
        } catch (\Error $err) {
            $this->container->get('event_dispatcher')->dispatch($event = new ErrorEvent($request, $err));
            $response = $event->getResponse();
            $this->responseFactory->processResponse($request, $response, $client);
        } finally {
            // @TODO Needs further investigation
            if ($response !== null) {
                fputs($input, $response->send());
                fclose($input);
            }
            $this->nbUncollectedCycles++;
            $this->container->get('session_wrapper')->clearWrapper();
        }
    }
    
    protected function treatProcessInput($name)
    {
        $process = $this->processManager->getByName($name);
        $content = fgets($process->getInput());
        if ($content === false) {
            $this->processManager->removeProcess($name, 'The process failed');
            return;
        }
        if (empty($content)) {
            return;
        }
        $data = json_decode($content, true);
        if (isset($data['technical'])) {
            $this->processManager->updateTechnicalData($process, $data['technical']);
        }
        if (isset($data['command'])) {
            return $this->treatCommand($data);
        }
        if (isset($data['task'])) {
            return $this->taskManager->validateTask($process, $data);
        }
    }
    
    /**
     * @param array $data
     */
    public function treatCommand($data)
    {
        switch ($data['command']) {
            case 'schedule':
                $this->realTimeActionScheduler->scheduleFromProcess(
                    $data['data']['manager'],
                    $data['data']['method'],
                    $data['data']['object_class'],
                    $data['data']['object_id'],
                    $data['data']['date']
                );
                break;
            case 'cancel':
                $this->realTimeActionScheduler->cancelFromProcess(
                    $data['data']['object_class'],
                    $data['data']['object_id'],
                    $data['data']['date']
                );
                break;
            case 'broadcast':
                $this->clientManager->broadcast($data['data']['payload']);
                break;
        }
    }
    
    /**
     * @param array $inputs
     * @param array $outputs
     * @param array $errors
     */
    protected function prepareStreamsState(&$inputs, &$outputs, &$errors)
    {
        $this->container->cleanApplication();
        
        $inputs = array_merge($this->inputs, $this->ws);
        $outputs = $this->outputs;
        $errors = null;
        
        $this->realTimeActionScheduler->execute();
        $this->cyclicActionScheduler->execute();
        
        if ($this->nbUncollectedCycles > $this->collectionCyclesNumber) {
            $this->container->get('database')->refresh();
            $this->clientManager->clear();
            gc_collect_cycles();
            $this->nbUncollectedCycles = 0;
        }
    }
    
    /**
     * @param string $name
     * @param resource $input
     */
    public function addInput($name, $input)
    {
        $this->inputs[$name] = $input;
    }
    
    /**
     * @param string $name
     * @param resource $output
     */
    public function addOutput($name, $output)
    {
        $this->outputs[$name] = $output;
    }
    
    /**
     * @param string $name
     */
    public function removeInput($name)
    {
        unset($this->inputs[$name]);
    }
    
    /**
     * @param string $name
     */
    public function removeOutput($name)
    {
        unset($this->outputs[$name]);
    }
    
    public static function debug($debug)
    {
        ob_start();
        var_dump($debug);
        file_put_contents('/srv/logs/php/test.log', PROCESS_NAME . ': ' . ob_get_clean() . "\n\n", FILE_APPEND);
    }
}
