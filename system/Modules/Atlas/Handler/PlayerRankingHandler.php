<?php

namespace Asylamba\Modules\Atlas\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Atlas\Manager\PlayerRankingManager;
use Asylamba\Modules\Atlas\Manager\RankingManager;
use Asylamba\Modules\Atlas\Message\PlayerRankingMessage;
use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Modules\Atlas\Model\Ranking;
use Asylamba\Modules\Atlas\Routine\PlayerRoutine;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\Player;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PlayerRankingHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected PlayerManager $playerManager,
		protected PlayerRankingManager $playerRankingManager,
		protected RankingManager $rankingManager,
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected bool $dataAnalysis,
	) {

	}

	public function __invoke(PlayerRankingMessage $message): void
	{
		if ($this->entityManager->getRepository(Ranking::class)->hasBeenAlreadyProcessed(true, false) === true) {
			return;
		}
		$playerRoutine = new PlayerRoutine($this->dataAnalysis);

		$players = $this->playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);

		$playerRankingRepository = $this->entityManager->getRepository(PlayerRanking::class);

		//$S_PRM1 = $this->playerRankingManager->getCurrentSession();
		//$this->playerRankingManager->newSession();
		//$this->playerRankingManager->loadLastContext();

		$ranking = $this->rankingManager->createRanking(true, false);

		$playerRoutine->execute(
			$players,
			$playerRankingRepository->getPlayersResources(),
			$playerRankingRepository->getPlayersResourcesData(),
			$playerRankingRepository->getPlayersGeneralData(),
			$playerRankingRepository->getPlayersArmiesData(),
			$playerRankingRepository->getPlayersPlanetData(),
			$playerRankingRepository->getPlayersTradeRoutes(),
			$playerRankingRepository->getPlayersLinkedTradeRoutes(),
			$playerRankingRepository->getAttackersButcherRanking(),
			$playerRankingRepository->getDefendersButcherRanking(),
			$this->orbitalBaseHelper
		);

		$playerRoutine->processResults($ranking, $players, $this->playerRankingManager, $playerRankingRepository);

		//$this->playerRankingManager->changeSession($S_PRM1);

		$this->entityManager->flush();
	}
}
