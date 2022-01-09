<?php

namespace App\Modules\Promethee\Manager;

use App\Modules\Promethee\Model\Technology;

use App\Classes\Database\Database;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Promethee\Helper\TechnologyHelper;

use App\Modules\Zeus\Model\Player;

class TechnologyManager
{
	public function __construct(
		protected Database $database,
		protected PlayerBonusManager $playerBonusManager,
		protected TechnologyHelper $technologyHelper
	) {
	}

	public function getPlayerTechnology(int $playerId): ?Technology
	{
		$technology = new Technology();
		$technology->rPlayer = $playerId;
		
		$statement = $this->database->prepare('SELECT * FROM technology WHERE rPlayer = :player_id');
		$statement->execute([
			'player_id' => $playerId
		]);
		while($row = $statement->fetch()) {
			$technology->setTechnology($row['technology'], $row['level'], TRUE);
		}
		return $technology;
	}
	
	/**
	 * ajouter une entrée bdd ou modifier ligne !!!
	 */
	public function affectTechnology(Technology $technology, int $id, string $value, Player $player, bool $load = false): bool
	{
		if ($technology->setTechnology($id, $value) === false) {
			return false;
		}
		if ($load === true) {
			return true;
		} else {
			if ($value < 1) {
				$this->deleteByRPlayer($technology->rPlayer, $id);
			} else {
				if ($value == 1) {
					$this->addTech($technology->rPlayer, $id, $value);
				} else {
					$this->updateTech($technology->rPlayer, $id, $value);
				}
				if (!$this->technologyHelper->isAnUnblockingTechnology($id)) {
					$bonus = $this->playerBonusManager->getBonusByPlayer($player);
					$this->playerBonusManager->load($bonus);
					$this->playerBonusManager->updateTechnoBonus($bonus, $id, $value);
				}
			}
			return true;
		}
	}

	public function addTech(int $playerId, int $technology, int $level): bool
	{
		$statement = $this->database->prepare('INSERT INTO technology(rPlayer, technology, level) VALUES(:player_id, :technology, :level)');
		return $statement->execute([
			'player_id' => $playerId,
			'technology' => $technology,
			'level' => $level
		]);
	}

	public function updateTech(int $playerId, int $technology, int $level): bool
	{
		$statement = $this->database->prepare('UPDATE technology SET level = :level WHERE rPlayer = :player_id AND technology = :technology');
		return $statement->execute([
			'level' => $level,
			'player_id' => $playerId,
			'technology' => $technology
		]);
	}

	public function delete(Technology $technology, int $technologyId): void
	{
		$technology->setTechnology($technologyId, 0);
	}

	public function deleteByRPlayer(int $playerId, int $technology): bool
	{
		$statement = $this->database->prepare('DELETE FROM technology WHERE rPlayer = :player_id and technology = :technology');
		return $statement->execute([
			'player_id' => $playerId,
			'technology' => $technology
		]);
	}
}
