<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Classes\Worker\API;

use Asylamba\Modules\Hephaistos\Routine\DailyRoutine;

class TechnicalManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected API $api,
		protected string $apiMode,
		protected PlayerManager $playerManager,
		protected int $playerInactiveTimeLimit,
		protected int $playerGlobalInactiveTime,
		protected int $notificationsReadTimeout,
		protected int $notificationsUnreadTimeout
	) {
	}
	
	public function processDailyRoutine(): void
	{
		$dailyRoutine = new DailyRoutine();
		$dailyRoutine->execute(
			$this->entityManager,
			$this->api,
			$this->apiMode,
			$this->playerManager,
			$this->playerInactiveTimeLimit,
			$this->playerGlobalInactiveTime,
			$this->notificationsReadTimeout,
			$this->notificationsUnreadTimeout
		);
	}
}
