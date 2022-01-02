<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\Task\TaskManager;
use Asylamba\Classes\Process\LoadBalancer;

class CyclicActionScheduler
{
    protected TaskManager $taskManager;
	protected LoadBalancer $loadBalancer;
	protected ?int $lastExecutedDay = null;
	protected ?int $lastExecutedHour = null;
	protected ?int $dailyScriptHour = null;
	/** @var array **/
	protected array $queue = [];
	
	const TYPE_HOURLY = 'hourly';
	const TYPE_DAILY = 'daily';

	public function __construct(TaskManager $taskManager, LoadBalancer $loadBalancer, int $dailyScriptHour)
	{
        $this->taskManager = $taskManager;
        $this->loadBalancer = $loadBalancer;
		$this->dailyScriptHour = $dailyScriptHour;
	}
	
	public function init()
	{
		$this->schedule('ares.commander_manager', 'uExperienceInSchool', self::TYPE_HOURLY);
		$this->schedule('athena.orbital_base_manager', 'updateBases', self::TYPE_HOURLY);
		$this->schedule('gaia.place_manager', 'updatePlayerPlaces', self::TYPE_HOURLY);
		$this->schedule('gaia.place_manager', 'updateNpcPlaces', self::TYPE_HOURLY);
		$this->schedule('zeus.player_manager', 'updatePlayersCredits', self::TYPE_HOURLY);
		$this->schedule('atlas.ranking_manager', 'processPlayersRanking', self::TYPE_DAILY);
		$this->schedule('atlas.ranking_manager', 'processFactionsRanking', self::TYPE_DAILY);
		$this->schedule('hephaistos.technical_manager', 'processDailyRoutine', self::TYPE_DAILY);
		$this->execute();
	}
	
    /**
     * @param string $manager
     * @param string $method
	 * @param string $type
     */
	public function schedule($manager, $method, $type) {
		$this->{"{$type}Queue"}[] = $this->taskManager->createCyclicTask($manager, $method);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		if (($currentHour = date('H')) === $this->lastExecutedHour) {
			return;
		}
		$this->executeHourly();
		$this->executeDaily($currentHour);
		$this->lastExecutedHour = $currentHour;
	}
	
	protected function executeHourly()
	{
		foreach ($this->hourlyQueue as $task) {
            $this->loadBalancer->affectTask($task);
		}
	}
	
	protected function executeDaily($currentHour)
	{
		if (($currentDay = date('d')) === $this->lastExecutedDay || ($currentHour < $this->dailyScriptHour)) {
			return;
		}
		foreach ($this->dailyQueue as $task) {
			$this->loadBalancer->affectTask($task);
		}
		$this->lastExecutedDay = $currentDay;
	}
	
	/**
	 * @return array
	 */
	public function getQueue()
	{
		return $this->queue;
	}
}
