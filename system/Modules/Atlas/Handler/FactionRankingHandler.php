<?php

namespace Asylamba\Modules\Atlas\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Atlas\Manager\FactionRankingManager;
use Asylamba\Modules\Atlas\Manager\RankingManager;
use Asylamba\Modules\Atlas\Message\FactionRankingMessage;
use Asylamba\Modules\Atlas\Model\FactionRanking;
use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Modules\Atlas\Model\Ranking;
use Asylamba\Modules\Atlas\Routine\FactionRoutine;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Gaia\Model\Sector;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FactionRankingHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected FactionRankingManager $factionRankingManager,
		protected RankingManager $rankingManager,
	) {

	}

	public function __invoke(FactionRankingMessage $message): void
	{
		if ($this->entityManager->getRepository(Ranking::class)->hasBeenAlreadyProcessed(false, true) === true) {
			return;
		}
		$factionRoutine = new FactionRoutine();

		$factions = $this->colorManager->getInGameFactions();
		$playerRankingRepository = $this->entityManager->getRepository(PlayerRanking::class);
		$factionRankingRepository = $this->entityManager->getRepository(FactionRanking::class);
		$sectors = $this->entityManager->getRepository(Sector::class)->getAll();

		$S_FRM1 = $this->factionRankingManager->getCurrentSession();
		$this->factionRankingManager->newSession();
		$this->factionRankingManager->loadLastContext();

		$ranking = $this->rankingManager->createRanking(false, true);

		foreach ($factions as $faction) {
			$this->colorManager->updateInfos($faction);

			$routesIncome = $factionRankingRepository->getRoutesIncome($faction);
			$playerRankings = $playerRankingRepository->getFactionPlayerRankings($faction);

			$factionRoutine->execute($faction, $playerRankings, $routesIncome, $sectors);
		}

		$winningFactionId = $factionRoutine->processResults($ranking, $factions, $this->factionRankingManager);

		$this->factionRankingManager->changeSession($S_FRM1);

		if ($winningFactionId !== null) {
			$this->rankingManager->processWinningFaction($winningFactionId);
		}
		$this->entityManager->flush();
	}
}
