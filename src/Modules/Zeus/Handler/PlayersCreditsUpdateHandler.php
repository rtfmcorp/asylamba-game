<?php

namespace App\Modules\Zeus\Handler;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\TransactionManager;
use App\Modules\Athena\Model\Transaction;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Model\Color;
use App\Modules\Promethee\Manager\ResearchManager;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Message\PlayersCreditsUpdateMessage;
use App\Modules\Zeus\Model\Player;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PlayersCreditsUpdateHandler implements MessageHandlerInterface
{
	public function __construct(
		protected PlayerManager $playerManager,
		protected ColorManager $colorManager,
		protected ResearchManager $researchManager,
		protected EntityManager $entityManager,
		protected PlayerBonusManager $playerBonusManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected CommanderManager $commanderManager,
		protected TransactionManager $transactionManager,
	) {
	}

	public function __invoke(PlayersCreditsUpdateMessage $message): void
	{
		$players = $this->playerManager->getActivePlayers();
		$factions = $this->colorManager->getAll();
		$S_RSM1 = $this->researchManager->getCurrentSession();
		$now = Utils::now();
		$repository = $this->entityManager->getRepository(Player::class);
		$this->entityManager->beginTransaction();

		foreach ($players as $player) {
			# update time
			$hours = Utils::intervalDates($now, $player->uPlayer);
			$nbHours = count($hours);
			if ($nbHours === 0) {
				continue;
			}
			$player->uPlayer = $now;
			# load the bonus
			$playerBonus = $this->playerBonusManager->getBonusByPlayer($player);
			$this->playerBonusManager->load($playerBonus);

			# load the researches
			$S_RSM1 = $this->researchManager->getCurrentSession();
			$this->researchManager->newSession();
			$this->researchManager->load(array('rPlayer' => $player->id));

			$bases = $this->orbitalBaseManager->getPlayerBases($player->id);
			$commanders = $this->commanderManager->getPlayerCommanders(
				$player->id,
				[Commander::AFFECTED, Commander::MOVING],
				['c.experience' => 'DESC', 'c.statement' => 'ASC']
			);
			$researchSession = $this->researchManager->getCurrentSession();
			$transactions = $this->transactionManager->getPlayerPropositions($player->id, Transaction::TYP_SHIP);

			$initialCredits = $player->credit;

			for ($i = 0; $i < $nbHours; $i++) {
				$this->playerManager->uCredit(
					$player,
					$bases,
					$playerBonus,
					$commanders,
					$researchSession,
					$factions,
					$transactions
				);
			}
			$repository->updatePlayerCredits(
				$player,
				abs($initialCredits - $player->credit),
				($initialCredits > $player->credit) ? '-' : '+'
			);
			$this->entityManager->clear($player);
		}
		$this->researchManager->changeSession($S_RSM1);
		$this->entityManager->flush(Color::class);
		$this->entityManager->commit();
	}
}
