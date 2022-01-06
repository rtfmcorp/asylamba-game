<?php

namespace Asylamba\Modules\Ares\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Ares\Message\CommandersExperienceMessage;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandersExperienceHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlayerBonusManager $playerBonusManager,
		protected PlayerManager $playerManager,
		protected CommanderManager $commanderManager,
	) {

	}

	public function __invoke(CommandersExperienceMessage $message): void
	{
		$now = Utils::now();
		$commanders = $this->entityManager->getRepository(Commander::class)->getAllByStatements([Commander::INSCHOOL]);
		$this->entityManager->beginTransaction();

		foreach ($commanders as $commander) {
			// If the commander was updated recently, we skip him
			if (Utils::interval($commander->uCommander, $now, 'h') === 0) {
				continue;
			}

			$nbrHours = Utils::intervalDates($now, $commander->uCommander);
			$commander->uCommander = $now;
			$orbitalBase = $this->orbitalBaseManager->get($commander->rBase);

			$playerBonus = $this->playerBonusManager->getBonusByPlayer($this->playerManager->get($commander->rPlayer));
			$this->playerBonusManager->load($playerBonus);
			$playerBonus = $playerBonus->bonus;
			foreach ($nbrHours as $hour) {
				$invest  = $orbitalBase->iSchool;
				$invest += $invest * $playerBonus->get(PlayerBonus::COMMANDER_INVEST) / 100;

				# xp gagnée
				$earnedExperience  = $invest / Commander::COEFFSCHOOL;
				$earnedExperience += (rand(0, 1) == 1)
					? rand(0, $earnedExperience / 20)
					: -(rand(0, $earnedExperience / 20));
				$earnedExperience  = round($earnedExperience);
				$earnedExperience  = ($earnedExperience < 0)
					? 0 : $earnedExperience;

				$this->commanderManager->upExperience($commander, $earnedExperience);
			}
		}
		$this->entityManager->commit();
	}
}
