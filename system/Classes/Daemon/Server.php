<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Router\Router;
use Asylamba\Classes\Library\Http\RequestFactory;
use Asylamba\Classes\Library\Http\ResponseFactory;
use Asylamba\Classes\Daemon\ClientManager;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Exception\ErrorException;

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
        $this->serverCycleTimeout = $container->getParameter('server_cycle_timeout');
        $this->port = $container->getParameter('server_port');
    }
	
    public function shutdown()
    {
        $this->shutdown = true;
        foreach($this->inputs as $input) {
            fclose($input);
        }
        foreach($this->outputs as $output) {
            fclose($output);
        }
    }

    public function createHttpServer()
    {
        $stream = stream_socket_server("tcp://0.0.0.0:{$this->port}", $errno, $errstr);
        if (!$stream) {
            throw new ErrorException("$errstr ($errno)");
        }
        $this->inputs[] = $stream;
    }

    public function listen()
    {
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
		
        while ($this->shutdown === false && ($nbUpgradedStreams = stream_select($inputs, $outputs, $errors, $this->serverCycleTimeout)) !== false) {
            if ($nbUpgradedStreams === 0) {
                $this->prepareStreamsState($inputs, $outputs, $errors);
                continue;
            }
            foreach ($inputs as $stream) {
                $input = stream_socket_accept($stream);
                $data = fread($input, 2048);
				if (empty($data)) {
					fclose($input);
					continue;
				}
                $request = $this->requestFactory->createRequestFromInput($data);
                $this->container->set('app.request', $request);
                if (($client = $this->clientManager->getClient($request)) === null) {
                    $client = $this->clientManager->createClient($request);
                }
                $this->container->set('app.session', $client->getSession());
                $response = $this->router->processRequest($request, $client);
                $this->container->set('app.response', $response);
                $this->responseFactory->processResponse($request, $response, $client);
                fputs ($input, $response->send());
                fclose($input);
            }
            $this->prepareStreamsState($inputs, $outputs, $errors);
        }
    }
    
    protected function prepareStreamsState(&$inputs, &$outputs, &$errors)
    {
		$this->container->cleanApplication();
		
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
    }
    
    public static function debug($debug)
    {
        ob_start();
        var_dump($debug);
        file_put_contents('test.log', ob_get_clean() . "\n\n", FILE_APPEND);
    }
}