<?php

namespace App\Classes\Scheduler;

use App\Modules\Ares\Message\CommandersExperienceMessage;
use App\Modules\Athena\Message\Base\BasesUpdateMessage;
use App\Modules\Atlas\Message\FactionRankingMessage;
use App\Modules\Atlas\Message\PlayerRankingMessage;
use App\Modules\Gaia\Message\NpcsPlacesUpdateMessage;
use App\Modules\Gaia\Message\PlayersPlacesUpdateMessage;
use App\Modules\Hephaistos\Message\DailyRoutineMessage;
use App\Modules\Zeus\Message\PlayersCreditsUpdateMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class CyclicActionScheduler
{
	protected ?int $lastExecutedDay = null;
	protected ?int $lastExecutedHour = null;
	/** @var array<string, list<class-string>> **/
	protected array $queues = [
		self::TYPE_DAILY => [
			DailyRoutineMessage::class,
			FactionRankingMessage::class,
			PlayerRankingMessage::class,
		],
		self::TYPE_HOURLY => [
			BasesUpdateMessage::class,
			CommandersExperienceMessage::class,
			NpcsPlacesUpdateMessage::class,
			PlayersCreditsUpdateMessage::class,
			PlayersPlacesUpdateMessage::class,
		],
	];
	
	const TYPE_HOURLY = 'hourly';
	const TYPE_DAILY = 'daily';

	public function __construct(
		protected MessageBusInterface $messageBus,
		protected int $dailyScriptHour
	) {
	}
	
	public function init(): void
	{
		$this->execute();
	}
	
	public function execute()
	{
		if (($currentHour = intval(date('H'))) === $this->lastExecutedHour) {
			return;
		}
		$this->executeHourly();
		$this->executeDaily($currentHour);
		$this->lastExecutedHour = $currentHour;
	}
	
	protected function executeHourly(): void
	{
		$this->processQueue(self::TYPE_HOURLY);
	}
	
	protected function executeDaily(int $currentHour): void
	{
		if (($currentDay = intval(date('d'))) === $this->lastExecutedDay || ($currentHour < $this->dailyScriptHour)) {
			return;
		}
		$this->processQueue(self::TYPE_DAILY);
		$this->lastExecutedDay = $currentDay;
	}

	protected function processQueue(string $queue): void
	{
		foreach ($this->queues[$queue] as $messageClass) {
			$this->messageBus->dispatch(new $messageClass());
		}
	}
}
