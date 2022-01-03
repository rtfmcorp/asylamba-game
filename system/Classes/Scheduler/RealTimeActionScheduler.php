<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Classes\DependencyInjection\Container;

use Asylamba\Classes\Task\TaskManager;
use Asylamba\Classes\Process\LoadBalancer;
use Asylamba\Classes\Process\ProcessGateway;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Athena\Manager\CommercialShippingManager;
use Asylamba\Modules\Athena\Manager\RecyclingMissionManager;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;
use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
use Symfony\Contracts\Service\Attribute\Required;

class RealTimeActionScheduler
{
	protected array $queue = [];
	protected CommanderManager $commanderManager;
	protected BuildingQueueManager $buildingQueueManager;
	protected CommercialShippingManager $commercialShippingManager;
	protected RecyclingMissionManager $recyclingMissionManager;
	protected ShipQueueManager $shipQueueManager;
	protected TechnologyQueueManager $technologyQueueManager;
	protected ColorManager $factionManager;

	public function __construct(
		protected TaskManager $taskManager,
		protected LoadBalancer $loadBalancer,
		protected ProcessGateway $processGateway,
	) {
	}

	#[Required]
	public function setCommanderManager(CommanderManager $commanderManager): void
	{
		$this->commanderManager = $commanderManager;
	}

	#[Required]
	public function setBuildingQueueManager(BuildingQueueManager $buildingQueueManager): void
	{
		$this->buildingQueueManager = $buildingQueueManager;
	}

	#[Required]
	public function setCommercialShippingManager(CommercialShippingManager $commercialShippingManager): void
	{
		$this->commercialShippingManager = $commercialShippingManager;
	}

	#[Required]
	public function setRecyclingMissionManager(RecyclingMissionManager $recyclingMissionManager): void
	{
		$this->recyclingMissionManager = $recyclingMissionManager;
	}

	#[Required]
	public function setShipQueueManager(ShipQueueManager $shipQueueManager): void
	{
		$this->shipQueueManager = $shipQueueManager;
	}

	#[Required]
	public function setTechnologyQueueManager(TechnologyQueueManager $technologyQueueManager): void
	{
		$this->technologyQueueManager = $technologyQueueManager;
	}

	#[Required]
	public function setFactionManager(ColorManager $factionManager): void
	{
		$this->factionManager = $factionManager;
	}
	
	public function init()
	{
		$this->commanderManager->scheduleMovements();
		$this->buildingQueueManager->scheduleActions();
		$this->commercialShippingManager->scheduleShippings();
		$this->recyclingMissionManager->scheduleMissions();
		$this->shipQueueManager->scheduleActions();
		$this->technologyQueueManager->scheduleQueues();
		$this->factionManager->scheduleSenateUpdate();
		$this->factionManager->scheduleCampaigns();
		$this->factionManager->scheduleElections();
		$this->factionManager->scheduleBallot();
		$this->execute();
	}

	public function schedule(string $manager, string $method, object $object, string $date, array $context = null): void
	{
		if (P_TYPE === 'worker') {
			$this->processGateway->writeToMaster([
				'command' => 'schedule',
				'data' => [
					'manager' => $manager,
					'method' => $method,
					'object_class' => get_class($object),
					'object_id' => $object->id,
					'date' => $date,
					'context' => $context 
				]
			]);

			return;
		}
		$this->queue[$date][get_class($object) . '-' . $object->id] = $this->taskManager->createRealTimeTask(
			$manager,
			$method,
			$object->id,
			$date,
			null,
			$context
		);
		// Sort the queue by date
		ksort($this->queue);
	}
	
	/**
	 * @param string $manager
	 * @param string $method
	 * @param string $objectClass
	 * @param int $objectId
	 * @param string $date
	 * @param array $context
	 */
	public function scheduleFromProcess($manager, $method, $objectClass, $objectId, $date, $context = null)
	{
		$this->queue[$date][$objectClass . '-' . $objectId] = $this->taskManager->createRealTimeTask($manager, $method, $objectId, $date, null, $context);
		// Sort the queue by date
		ksort($this->queue);
	}
	
	/**
	 * This method is meant to executed the scheduled data if their date is passed
	 * In case of cyclic actions, the scheduler will check the current hour and compare it to the last executed hour
	 */
	public function execute()
	{
		$now = new \DateTime();
		
		foreach ($this->queue as $date => $actions) {
			// If the action is to be executed later, we break the loop
			// This logic depends on the fact that the queue is key-sorted by date
			if ($now < new \DateTime($date)) {
				break;
			}
			foreach ($actions as $task) {
				$this->loadBalancer->affectTask($task);
			}
			unset($this->queue[$date]);
		}
	}
	
	/**
	 * @param object $object
	 * @param string $date
	 * @param string $oldDate
	 */
	public function reschedule($object, $date, $oldDate) {
		$this->queue[$date][get_class($object) . '-' . $object->id] = $this->queue[$oldDate][get_class($object) . '-' . $object->id];
		
		$this->cancel($object, $oldDate);
	}
	
	/**
	 * @param object $object
	 * @param string $date
	 */
	public function cancel($object, $date): void
	{
		if (P_TYPE === 'worker') {
			$this->processGateway->writeToMaster([
				'command' => 'cancel',
				'data' => [
					'object_class' => get_class($object),
					'object_id' => $object->id,
					'date' => $date
				]
			]);

			return;
		}
		unset($this->queue[$date][get_class($object) . '-' . $object->id]);
		
		if (empty($this->queue[$date])) {
			unset($this->queue[$date]);
		}
	}
    
    public function cancelFromProcess(string $class, int $id, string $date): void
    {
		unset($this->queue[$date][$class . '-' . $id]);
		
		if (empty($this->queue[$date])) {
			unset($this->queue[$date]);
		}
    }

	public function getQueue(): array
	{
		return $this->queue;
	}
}
