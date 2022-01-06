<?php

namespace Asylamba\Modules\Athena\Handler\Base;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Athena\Message\Base\BasesUpdateMessage;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BasesUpdateHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected PlayerManager $playerManager,
		protected PlayerBonusManager $playerBonusManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected OrbitalBaseHelper $orbitalBaseHelper,
	) {

	}

	public function __invoke(BasesUpdateMessage $message): void
	{
		$repository = $this->entityManager->getRepository(OrbitalBase::class);
		$bases = $repository->getAll();
		$this->entityManager->beginTransaction();
		$now = Utils::now();

		foreach ($bases as $base) {
			# update time
			$hours = Utils::intervalDates($now, $base->uOrbitalBase);

			if (count($hours) === 0) {
				continue;
			}
			$player = $this->playerManager->get($base->rPlayer);
			$playerBonus = $this->playerBonusManager->getBonusByPlayer($player);
			$this->playerBonusManager->load($playerBonus);
			$base->setUpdatedAt($now);
			$initialResources = $base->resourcesStorage;
			$initialAntiSpyAverage = $base->antiSpyAverage;

			foreach ($hours as $hour) {
				$this->updateResources($base, $playerBonus);
				$this->updateAntiSpy($base);
			}

			$repository->updateBase(
				$base,
				$base->resourcesStorage - $initialResources,
				$base->antiSpyAverage - $initialAntiSpyAverage
			);
		}
		$this->entityManager->commit();
	}

	protected function updateResources(OrbitalBase $orbitalBase, PlayerBonus $playerBonus): void
	{
		$addResources = Game::resourceProduction($this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $orbitalBase->levelRefinery, 'refiningCoefficient'), $orbitalBase->planetResources);
		$addResources += $addResources * $playerBonus->bonus->get(PlayerBonus::REFINERY_REFINING) / 100;

		$this->orbitalBaseManager->increaseResources($orbitalBase, (int) $addResources, false, false);
	}

	protected function updateAntiSpy(OrbitalBase $orbitalBase): void
	{
		$orbitalBase->antiSpyAverage = round((($orbitalBase->antiSpyAverage * (24 - 1)) + ($orbitalBase->iAntiSpy)) / 24);
	}
}
