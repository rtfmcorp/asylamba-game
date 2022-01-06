<?php

namespace Asylamba\Classes\Scheduler;

use Asylamba\Modules\Ares\Message\CommandersExperienceMessage;
use Asylamba\Modules\Athena\Message\Base\BasesUpdateMessage;
use Asylamba\Modules\Atlas\Message\FactionRankingMessage;
use Asylamba\Modules\Atlas\Message\PlayerRankingMessage;
use Asylamba\Modules\Gaia\Message\NpcsPlacesUpdateMessage;
use Asylamba\Modules\Gaia\Message\PlayersPlacesUpdateMessage;
use Asylamba\Modules\Hephaistos\Message\DailyRoutineMessage;
use Asylamba\Modules\Zeus\Message\PlayersCreditsUpdateMessage;
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
