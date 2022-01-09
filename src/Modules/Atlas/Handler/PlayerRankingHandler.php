<?php

namespace App\Modules\Atlas\Handler;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Atlas\Manager\PlayerRankingManager;
use App\Modules\Atlas\Manager\RankingManager;
use App\Modules\Atlas\Message\PlayerRankingMessage;
use App\Modules\Atlas\Model\PlayerRanking;
use App\Modules\Atlas\Model\Ranking;
use App\Modules\Atlas\Routine\PlayerRoutine;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
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
