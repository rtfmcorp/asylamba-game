<?php

namespace Asylamba\Modules\Promethee\Handler;

use Asylamba\Classes\Daemon\ClientManager;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Modules\Promethee\Manager\TechnologyManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
use Asylamba\Modules\Promethee\Message\TechnologyQueueMessage;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TechnologyQueueHandler implements MessageHandlerInterface
{
	public function __construct(
		protected TechnologyQueueManager $technologyQueueManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlayerManager $playerManager,
		protected TechnologyManager $technologyManager,
		protected TechnologyHelper $technologyHelper,
		protected ClientManager $clientManager,
		protected SessionWrapper $sessionWrapper,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(TechnologyQueueMessage $message)
	{
		$technologyQueue = $this->technologyQueueManager->get($message->getTechnologyQueueId());
		$orbitalBase = $this->orbitalBaseManager->get($technologyQueue->getPlaceId());
		$player = $this->playerManager->get($technologyQueue->getPlayerId());
		# technologie construite
		$technology = $this->technologyManager->getPlayerTechnology($player->getId());
		$this->technologyManager->affectTechnology($technology, $technologyQueue->getTechnology(), $technologyQueue->getTargetLevel(), $player);
		# increase player experience
		$experience = $this->technologyHelper->getInfo($technologyQueue->getTechnology(), 'points', $technologyQueue->getTargetLevel());
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$alt = 'Développement de votre technologie ' . $this->technologyHelper->getInfo($technologyQueue->getTechnology(), 'name');
			if ($technologyQueue->getTargetLevel() > 1) {
				$alt .= ' niveau ' . $technologyQueue->getTargetLevel();
			}
			$alt .= ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
			$session->addFlashbag($alt, Flashbag::TYPE_TECHNOLOGY_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		$this->entityManager->remove($technologyQueue);
		$this->entityManager->flush($technologyQueue);
	}
}
