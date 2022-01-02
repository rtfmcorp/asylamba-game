<?php

namespace Asylamba\Classes\Daemon;

use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Event\ProcessExceptionEvent;
use Asylamba\Classes\Event\ProcessErrorEvent;

use Asylamba\Classes\Memory\MemoryManager;
use Asylamba\Classes\Process\ProcessManager;
use Asylamba\Classes\Process\ProcessGateway;
use Asylamba\Classes\Task\TaskManager;
use Asylamba\Classes\Worker\Manager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WorkerServer
{
    protected bool $shutdown = false;
    protected array $inputs = [];
    protected array $outputs = [];
    protected int $nbUncollectedCycles = 0;

    public function __construct(
		protected Container $container,
		protected Database $database,
		protected TaskManager $taskManager,
		protected EventDispatcher $eventDispatcher,
		protected MemoryManager $memoryManager,
		protected ProcessManager $processManager,
		protected ProcessGateway $processGateway,
		protected iterable $statefulManagers,
		protected int $workerCycleTimeout,
		protected int $collectionCyclesNumber,
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
		$this->processGateway->writeToMaster([
			'info' => 'process shutdown'
		]);
        $this->shutdown = true;
        foreach($this->inputs as $input) {
            \fclose($input);
        }
        foreach($this->outputs as $output) {
            \fclose($output);
        }
    }

    public function listen(): void
    {
		\stream_set_blocking(STDIN, 0);
        $this->inputs['master'] = STDIN;
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
        \gc_disable();
		$this->memoryManager->refreshNodeMemory();
		
        while ($this->shutdown === false && ($nbUpgradedStreams = \stream_select($inputs, $outputs, $errors, $this->workerCycleTimeout)) !== false) {
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
	
	protected function treatMasterInput($input): void
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
			$this->eventDispatcher->dispatch($event = new ProcessExceptionEvent($ex, $task), ProcessExceptionEvent::NAME);
		} catch (\Error $err) {
			$this->eventDispatcher->dispatch($event = new ProcessErrorEvent($err, $task), ProcessErrorEvent::NAME);
		} finally {
			if (!empty($responseData)) {
				$responseData['time'] = microtime(true) - $startTime;
				$responseData['technical'] = $this->container->get(MemoryManager::class)->getNodeMemory();
				$this->processGateway->writeToMaster($responseData);
			}
		}
	}
	
	protected function treatProcessInput(string $name): void
	{
		//$process = $this->processManager->getByName($name);
		
		//$content = \fgets($process->getInput(), 2048);

		//throw new \ErrorException('bluh');
	}
    
    protected function prepareStreamsState(array &$inputs, array &$outputs, array &$errors): void
    {
		$this->cleanApplication();
		$this->memoryManager->refreshNodeMemory();
		
        $inputs = $this->inputs;
        $outputs = $this->outputs;
        $errors = null;
		
        if ($this->nbUncollectedCycles > $this->collectionCyclesNumber) {
			$this->database->refresh();
            \gc_collect_cycles();
            $this->nbUncollectedCycles = 0;
        }
    }
    
    public function addInput(string $name, $input): void
    {
        $this->inputs[$name] = $input;
    }
    
    public function addOutput(string $name, $output): void
    {
        $this->outputs[$name] = $output;
    }
    
    public function removeInput(string $name): void
    {
        fclose($this->inputs[$name]);
		unset($this->inputs[$name]);
    }
    
    public function removeOutput(string $name): void
    {
        fclose($this->outputs[$name]);
		unset($this->outputs[$name]);
    }
}
