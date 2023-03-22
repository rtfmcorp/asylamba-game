<?php

namespace Asylamba\Modules\Demeter\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Election\ElectionManager;
use Asylamba\Modules\Demeter\Message\BallotMessage;
use Asylamba\Modules\Demeter\Message\ElectionMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ElectionHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected ElectionManager $electionManager,
		protected MessageBusInterface $messageBus,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(ElectionMessage $message): void
	{
		$faction = $this->colorManager->get($message->getFactionId());
		$faction->electionStatement = Color::ELECTION;
		$election = $this->electionManager->getFactionLastElection($faction->getId());

		$date = new \DateTime($election->dElection);
		$date->modify('+' . Color::ELECTIONTIME . ' second');

		$this->messageBus->dispatch(
			new BallotMessage($faction->getId()),
			[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))]
		);

		$this->entityManager->flush($faction);
	}
}
