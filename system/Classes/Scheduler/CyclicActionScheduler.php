<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\Task\TaskManager;
use Asylamba\Classes\Process\LoadBalancer;

class CyclicActionScheduler
{
    /** @var TaskManager **/
    protected $taskManager;
	/** @var LoadBalancer **/
	protected $loadBalancer;
	/** @var int **/
	protected $lastExecutedHour;
	/** @var array **/
	protected $queue = [];
	
	/**
	 * @param TaskManager $taskManager
	 * @param LoadBalancer $loadBalancer
	 */
	public function __construct(TaskManager $taskManager, LoadBalancer $loadBalancer)
	{
        $this->taskManager = $taskManager;
        $this->loadBalancer = $loadBalancer;
	}
	
	public function init()
	{
		$this->schedule('ares.commander_manager', 'uExperienceInSchool');
		$this->schedule('gaia.place_manager', 'updatePlayerPlaces');
		$this->schedule('gaia.place_manager', 'updateNpcPlaces');
		$this->schedule('zeus.player_manager', 'updatePlayersCredits');
	}
	
    /**
     * @param string $manager
     * @param string $method
     */
	public function schedule($manager, $method) {
		$this->queue[] = $this->taskManager->createCyclicTask($manager, $method);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		if (($currentHour = date('H')) === $this->lastExecutedHour) {
			return;
		}
		foreach ($this->queue as $task) {
            $this->loadBalancer->affectTask($task);
		}
		$this->lastExecutedHour = $currentHour;
	}
	
	/**
	 * @return array
	 */
	public function getQueue()
	{
		return $this->queue;
	}
}