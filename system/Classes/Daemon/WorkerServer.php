<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Event\ProcessExceptionEvent;
use Asylamba\Classes\Event\ProcessErrorEvent;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Process\ProcessManager;
use Asylamba\Classes\Process\ProcessGateway;
use Asylamba\Classes\Task\TaskManager;

class WorkerServer
{
    /** @var Container **/
    protected $container;
    /** @var TaskManager **/
    protected $taskManager;
    /** @var ProcessManager **/
    protected $processManager;
    /** @var ProcessGateway **/
    protected $processGateway;
    /** @var int **/
    protected $workerCycleTimeout;
    /** @var boolean **/
    protected $shutdown = false;
    /** @var array **/
    protected $inputs = [];
    /** @var array **/
    protected $outputs = [];
    /** @var int **/
    protected $nbUncollectedCycles = 0;
    /** @var int **/
    protected $collectionCyclesNumber;

    /**
     * @param Container $container
     * @param int $serverCycleTimeout
     * @param int $port
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->taskManager = $container->get('task_manager');
        $this->processGateway = $container->get('process_gateway');
        $this->workerCycleTimeout = $container->getParameter('worker_cycle_timeout');
        $this->collectionCyclesNumber = $container->getParameter('worker_collection_cycles_number');
    }
    
    public function shutdown()
    {
        $this->processGateway->writeToMaster([
            'info' => 'process shutdown'
        ]);
        $this->shutdown = true;
        foreach ($this->inputs as $input) {
            fclose($input);
        }
        foreach ($this->outputs as $output) {
            fclose($output);
        }
    }

    public function listen()
    {
        stream_set_blocking(STDIN, 0);
        $this->inputs['master'] = STDIN;
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
        gc_disable();
        $this->container->get('memory_manager')->refreshNodeMemory();
        
        while ($this->shutdown === false && ($nbUpgradedStreams = stream_select($inputs, $outputs, $errors, $this->workerCycleTimeout)) !== false) {
            $this->nbUncollectedCycles++;
            if ($nbUpgradedStreams === 0) {
                $this->prepareStreamsState($inputs, $outputs, $errors);
                continue;
            }
            foreach ($inputs as $stream) {
                // Get the array index which is the given name of the stream
                $name = array_search($stream, $this->inputs);
                if ($name === 'master') {
                    $this->treatMasterInput($stream);
                } else {
                    $this->treatProcessInput($name);
                }
            }
            $this->prepareStreamsState($inputs, $outputs, $errors);
        }
    }
    
    protected function treatMasterInput($input)
    {
        $responseData = [];
        $task = null;
        $startTime = microtime(true);
        try {
            $content = fgets($input, 2048);
            if (empty($content)) {
                return;
            }
            $task = $this->taskManager->createTaskFromData(json_decode($content, true));
            $responseData = $this->taskManager->perform($task);
        } catch (\Exception $ex) {
            $this->container->get('event_dispatcher')->dispatch($event = new ProcessExceptionEvent($ex, $task));
        } catch (\Error $err) {
            $this->container->get('event_dispatcher')->dispatch($event = new ProcessErrorEvent($err, $task));
        } finally {
            if (!empty($responseData)) {
                $responseData['time'] = microtime(true) - $startTime;
                $responseData['technical'] = $this->container->get('memory_manager')->getNodeMemory();
                $this->processGateway->writeToMaster($responseData);
            }
        }
    }
    
    protected function treatProcessInput($name)
    {
        //$process = $this->processManager->getByName($name);
        
        //$content = fgets($process->getInput(), 2048);
    }
    
    protected function prepareStreamsState(&$inputs, &$outputs, &$errors)
    {
        $this->container->cleanApplication();
        $this->container->get('memory_manager')->refreshNodeMemory();
        
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
        
        if ($this->nbUncollectedCycles > $this->collectionCyclesNumber) {
            $this->container->get('database')->refresh();
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
        fclose($this->inputs[$name]);
        unset($this->inputs[$name]);
    }
    
    /**
     * @param string $name
     */
    public function removeOutput($name)
    {
        fclose($this->outputs[$name]);
        unset($this->outputs[$name]);
    }
}
