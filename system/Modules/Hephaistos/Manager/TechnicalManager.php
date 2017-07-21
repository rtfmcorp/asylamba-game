<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Classes\Worker\API;

use Asylamba\Modules\Hephaistos\Routine\DailyRoutine;

class TechnicalManager
{
	/** @var EntityManager **/
	protected $entityManager;
	/** @var API **/
	protected $api;
	/** @var string **/
	protected $apimode;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var int **/
	protected $playerInactiveTimeLimit;
	/** @var int **/
	protected $playerGlobalInactiveTime;
	/** @var int **/
	protected $readTimeout;
	/** @var int **/
	protected $unreadTimeout;
	
	/**
	 * @param EntityManager $entityManager
	 * @param API $api
	 * @param string $apimode
	 * @param PlayerManager $playerManager
	 * @param int $playerInactiveTimeLimit
	 * @param int $playerGlobalInactiveTime
	 * @param int $readTimeout
	 * @param int $unreadTimeout
	 */
	public function __construct(
		EntityManager $entityManager,
		API $api,
		$apimode,
		PlayerManager $playerManager,
		$playerInactiveTimeLimit,
		$playerGlobalInactiveTime,
		$readTimeout,
		$unreadTimeout
	)
	{
		$this->entityManager = $entityManager;
		$this->api = $api;
		$this->apimode = $apimode;
		$this->playerManager = $playerManager;
		$this->playerInactiveTimeLimit = $playerInactiveTimeLimit;
		$this->playerGlobalInactiveTime = $playerGlobalInactiveTime;
		$this->readTimeout = $readTimeout;
		$this->unreadTimeout = $unreadTimeout;
	}
	
	public function processDailyRoutine()
	{
		$dailyRoutine = new DailyRoutine();
		$dailyRoutine->execute(
			$this->entityManager,
			$this->api,
			$this->apimode,
			$this->playerManager,
			$this->playerInactiveTimeLimit,
			$this->playerGlobalInactiveTime,
			$this->readTimeout,
			$this->unreadTimeout
		);
	}
}