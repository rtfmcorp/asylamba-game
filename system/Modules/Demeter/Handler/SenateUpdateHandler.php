<?php

namespace Asylamba\Modules\Demeter\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Message\SenateUpdateMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SenateUpdateHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected PlayerManager $playerManager,
		protected MessageBusInterface $messageBus,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(SenateUpdateMessage $message): void
	{
		$faction = $this->colorManager->get($message->getFactionId());
		$this->colorManager->updateStatus($faction, $this->playerManager->getFactionPlayersByRanking($faction->getId()));

		if ($faction->regime === Color::ROYALISTIC && $faction->electionStatement === Color::MANDATE) {
			$date = date('Y-m-d H:i:s', time() + $faction->mandateDuration);
			$faction->dLastElection = $date;
			$this->messageBus->dispatch(
				new SenateUpdateMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($date)],
			);
			$this->entityManager->flush($faction);
		}
	}
}
