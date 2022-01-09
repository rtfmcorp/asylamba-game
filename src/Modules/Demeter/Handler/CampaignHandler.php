<?php

namespace App\Modules\Demeter\Handler;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\DateTimeConverter;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Manager\Election\ElectionManager;
use App\Modules\Demeter\Message\BallotMessage;
use App\Modules\Demeter\Message\CampaignMessage;
use App\Modules\Demeter\Message\ElectionMessage;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Model\Election\Election;
use App\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CampaignHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected PlayerManager $playerManager,
		protected ElectionManager $electionManager,
		protected MessageBusInterface $messageBus,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(CampaignMessage $message): void
	{
		$faction = $this->colorManager->get($message->getFactionId());
		$factionPlayers = $this->playerManager->getFactionPlayersByRanking($faction->getId());
		$this->colorManager->updateStatus($faction, $factionPlayers);

		$date = new \DateTime($faction->dLastElection);
		$date->modify('+' . $faction->mandateDuration + Color::CAMPAIGNTIME . ' second');

		$election = new Election();
		$election->rColor = $faction->id;
		$election->dElection = $date->format('Y-m-d H:i:s');

		$this->electionManager->add($election);
		$faction->electionStatement = Color::CAMPAIGN;
		if ($faction->regime === Color::DEMOCRATIC) {
			$this->messageBus->dispatch(
				new ElectionMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($election->dElection)],
			);
		} elseif ($faction->regime === Color::THEOCRATIC) {
			$this->messageBus->dispatch(
				new BallotMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($election->dElection)],
			);
		}
		$this->entityManager->flush($election);
		$this->entityManager->flush($faction);
	}
}
