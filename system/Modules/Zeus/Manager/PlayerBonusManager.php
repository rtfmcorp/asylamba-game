<?php

namespace Asylamba\Modules\Zeus\Manager;

use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Container\StackList;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Demeter\Resource\LawResources;

use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Promethee\Manager\TechnologyManager;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Classes\Library\Session\SessionWrapper;

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Symfony\Contracts\Service\Attribute\Required;

class PlayerBonusManager
{
	protected ColorManager $colorManager;
	protected TechnologyHelper $technologyHelper;
	protected TechnologyManager $technologyManager;

	public function __construct(
		protected LawManager $lawManager,
		protected SessionWrapper $sessionWrapper
	) {
	}

	#[Required]
	public function setColorManager(ColorManager $colorManager): void
	{
		$this->colorManager = $colorManager;
	}

	#[Required]
	public function setTechnologyHelper(TechnologyHelper $technologyHelper): void
	{
		$this->technologyHelper = $technologyHelper;
	}

	#[Required]
	public function setTechnologyManager(TechnologyManager $technologyManager): void
	{
		$this->technologyManager = $technologyManager;
	}
	
	public function getBonusByPlayer(Player $player): PlayerBonus
	{
		$playerBonus = new PlayerBonus();
		$playerBonus->rPlayer = $player->id;
		$playerBonus->synchronized = $player->isSynchronized();
		$playerBonus->playerColor = $player->rColor;
		$playerBonus->bonus = new StackList();
		return $playerBonus;
	}

	public function initialize(PlayerBonus $playerBonus) {
		# remplissage des bonus avec les technologies
		$playerBonus->technology = $this->technologyManager->getPlayerTechnology($playerBonus->rPlayer);
		
		$this->fillFromTechnology($playerBonus);
		$this->addFactionBonus($playerBonus);
		$this->addLawBonus($playerBonus);

		if ($playerBonus->synchronized) {
			for ($i = 0; $i < PlayerBonus::BONUS_QUANTITY; $i++) { 
				$this->sessionWrapper->get('playerBonus')->add($i, ($playerBonus->bonus->exist($i)) ? $playerBonus->bonus->get($i) : 0);
			}
		}
	}

	public function load(PlayerBonus $playerBonus) {
		$playerBonus->technology = new Technology($playerBonus->rPlayer);
		if ($playerBonus->synchronized) {
			// chargement de l'objet avec le contrôleur
			for ($i = 0; $i < PlayerBonus::BONUS_QUANTITY; $i++) { 
				$playerBonus->bonus->add($i, $this->sessionWrapper->get('playerBonus')->get($i));
			}
		} else {				
			// remplissage de l'objet normalement
			// remplissage avec les technologies
			$this->fillFromTechnology($playerBonus);
			$this->addFactionBonus($playerBonus);
			$this->addLawBonus($playerBonus);

			# remplissage avec les cartes
			// ...
		}
	}
	
	private function fillFromTechnology(PlayerBonus $playerBonus) {
		$this->addTechnoToBonus($playerBonus, Technology::GENERATOR_SPEED, PlayerBonus::GENERATOR_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::REFINERY_REFINING, PlayerBonus::REFINERY_REFINING);
		$this->addTechnoToBonus($playerBonus, Technology::REFINERY_STORAGE, PlayerBonus::REFINERY_STORAGE);
		$this->addTechnoToBonus($playerBonus, Technology::DOCK1_SPEED, PlayerBonus::DOCK1_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::DOCK2_SPEED, PlayerBonus::DOCK2_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::TECHNOSPHERE_SPEED, PlayerBonus::TECHNOSPHERE_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::COMMERCIAL_INCOME, PlayerBonus::COMMERCIAL_INCOME);
		$this->addTechnoToBonus($playerBonus, Technology::GRAVIT_MODULE, PlayerBonus::GRAVIT_MODULE);
		$this->addTechnoToBonus($playerBonus, Technology::DOCK3_SPEED, PlayerBonus::DOCK3_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::POPULATION_TAX, PlayerBonus::POPULATION_TAX);
		$this->addTechnoToBonus($playerBonus, Technology::COMMANDER_INVEST, PlayerBonus::COMMANDER_INVEST);
		$this->addTechnoToBonus($playerBonus, Technology::UNI_INVEST, PlayerBonus::UNI_INVEST);
		$this->addTechnoToBonus($playerBonus, Technology::ANTISPY_INVEST, PlayerBonus::ANTISPY_INVEST);
		$this->addTechnoToBonus($playerBonus, Technology::SPACESHIPS_SPEED, PlayerBonus::SHIP_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::SPACESHIPS_CONTAINER, PlayerBonus::SHIP_CONTAINER);
		$this->addTechnoToBonus($playerBonus, Technology::BASE_QUANTITY, PlayerBonus::BASE_QUANTITY);
		$this->addTechnoToBonus($playerBonus, Technology::FIGHTER_SPEED, PlayerBonus::FIGHTER_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::FIGHTER_ATTACK, PlayerBonus::FIGHTER_ATTACK);
		$this->addTechnoToBonus($playerBonus, Technology::FIGHTER_DEFENSE, PlayerBonus::FIGHTER_DEFENSE);
		$this->addTechnoToBonus($playerBonus, Technology::CORVETTE_SPEED, PlayerBonus::CORVETTE_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::CORVETTE_ATTACK, PlayerBonus::CORVETTE_ATTACK);
		$this->addTechnoToBonus($playerBonus, Technology::CORVETTE_DEFENSE, PlayerBonus::CORVETTE_DEFENSE);
		$this->addTechnoToBonus($playerBonus, Technology::FRIGATE_SPEED, PlayerBonus::FRIGATE_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::FRIGATE_ATTACK, PlayerBonus::FRIGATE_ATTACK);
		$this->addTechnoToBonus($playerBonus, Technology::FRIGATE_DEFENSE, PlayerBonus::FRIGATE_DEFENSE);
		$this->addTechnoToBonus($playerBonus, Technology::DESTROYER_SPEED, PlayerBonus::DESTROYER_SPEED);
		$this->addTechnoToBonus($playerBonus, Technology::DESTROYER_ATTACK, PlayerBonus::DESTROYER_ATTACK);
		$this->addTechnoToBonus($playerBonus, Technology::DESTROYER_DEFENSE, PlayerBonus::DESTROYER_DEFENSE);
	}

	private function addTechnoToBonus(PlayerBonus $playerBonus, $techno, $bonus) {
		$totalBonus = 0;
		for ($i = 0; $i <= $playerBonus->technology->getTechnology($techno); $i++) { 
			$totalBonus += $this->technologyHelper->getImprovementPercentage($techno, $i);
		}
		$playerBonus->bonus->add($bonus, $totalBonus);
	}

	private function addLawBonus(PlayerBonus $playerBonus) {
		$laws = $this->lawManager->getByFactionAndStatements($playerBonus->playerColor, [Law::EFFECTIVE]);
		foreach ($laws as $law) {
			switch ($law->type) {
				case 5:
					$playerBonus->bonus->increase(PlayerBonus::DOCK1_SPEED, LawResources::getInfo($law->type, 'bonus'));
					$playerBonus->bonus->increase(PlayerBonus::DOCK2_SPEED, LawResources::getInfo($law->type, 'bonus'));
					$playerBonus->bonus->increase(PlayerBonus::REFINERY_REFINING, -10);
					$playerBonus->bonus->increase(PlayerBonus::REFINERY_REFINING, -10);
					break;
				case 6:
					#subvention technologique
					$playerBonus->bonus->increase(PlayerBonus::TECHNOSPHERE_SPEED, LawResources::getInfo($law->type, 'bonus'));
					break;	
				default:
					break;
			}
		}
	}
	
	private function addFactionBonus(PlayerBonus $playerBonus) {
		$color = $this->colorManager->get($playerBonus->playerColor);

		if (in_array(ColorResource::DEFENSELITTLESHIPBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::FIGHTER_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::CORVETTE_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::FRIGATE_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::DESTROYER_DEFENSE, 5);
		}
		
		if (in_array(ColorResource::SPEEDLITTLESHIPBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::FIGHTER_SPEED, 10);
			$playerBonus->bonus->increase(PlayerBonus::CORVETTE_SPEED, 10);
		}

		if (in_array(ColorResource::DEFENSELITTLESHIPMALUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::FRIGATE_DEFENSE, -5);
			$playerBonus->bonus->increase(PlayerBonus::DESTROYER_DEFENSE, -5);
		}
		
		if (in_array(ColorResource::COMMERCIALROUTEBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::COMMERCIAL_INCOME, 5);
		}
		
		if (in_array(ColorResource::TAXBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::POPULATION_TAX, 3);
		}		

		if (in_array(ColorResource::LOOTRESOURCESMALUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::SHIP_CONTAINER, -5);
		}
		
		if (in_array(ColorResource::RAFINERYBONUS, $color->bonus)) {	
			$playerBonus->bonus->increase(PlayerBonus::REFINERY_REFINING, 4);
		}

		if (in_array(ColorResource::STORAGEBONUS, $color->bonus)) {	
			$playerBonus->bonus->increase(PlayerBonus::REFINERY_STORAGE, 4);
		}
		
		if (in_array(ColorResource::BIGACADEMICBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::UNI_INVEST, 4);
		}
		
		if (in_array(ColorResource::COMMANDERSCHOOLBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::COMMANDER_INVEST, 6);
		}

		if (in_array(ColorResource::LITTLEACADEMICBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::UNI_INVEST, 2);
		}

		if (in_array(ColorResource::TECHNOLOGYBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::TECHNOSPHERE_SPEED, 2);
		}

		if (in_array(ColorResource::DEFENSELITTLESHIPBONUS, $color->bonus)) {
			$playerBonus->bonus->increase(PlayerBonus::FIGHTER_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::CORVETTE_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::FRIGATE_DEFENSE, 5);
			$playerBonus->bonus->increase(PlayerBonus::DESTROYER_DEFENSE, 5);		
		}
	}

	public function increment(PlayerBonus $playerBonus, $bonusId, $increment) {
		if ($bonusId >= 0 && $bonusId < PlayerBonus::BONUS_QUANTITY) {
			if ($increment > 0) {
				$playerBonus->bonus->add($bonusId, $playerBonus->bonus->get($bonusId) + $increment);
				if ($playerBonus->synchronized) {
					$this->sessionWrapper->get('playerBonus')->add($bonusId, $playerBonus->bonus->get($bonusId));
				}
			} else {
				throw new ErrorException('incrémentation de bonus impossible - l\'incrément doit être positif');
			}
		} else {
			throw new ErrorException('incrémentation de bonus impossible - bonus invalide');
		}
	}

	public function decrement(PlayerBonus $playerBonus, $bonusId, $decrement) {
		if ($bonusId >= 0 && $bonusId < PlayerBonus::BONUS_QUANTITY) {
			if ($increment > 0) {
				if ($increment <= $playerBonus->bonus->get($bonusId)) {
					$playerBonus->bonus->add($bonusId, $playerBonus->bonus->get($bonusId) - $decrement);
					if ($playerBonus->synchronized) {
						$this->sessionWrapper->get('playerBonus')->add($bonusId, $playerBonus->bonus->get($bonusId));
					}
				} else {
					throw new ErrorException('décrémentation de bonus impossible - le décrément est plus grand que le bonus');
				}
			} else {
				throw new ErrorException('décrémentation de bonus impossible - le décrément doit être positif');
			}
		} else {
			throw new ErrorException('décrémentation de bonus impossible - bonus invalide');
		}
	}

	public function updateTechnoBonus(PlayerBonus $playerBonus, $techno, $level) {
		switch ($techno) {
			case Technology::GENERATOR_SPEED: $bonusId = PlayerBonus::GENERATOR_SPEED; break;
			case Technology::REFINERY_REFINING: $bonusId = PlayerBonus::REFINERY_REFINING; break;
			case Technology::REFINERY_STORAGE: $bonusId = PlayerBonus::REFINERY_STORAGE; break;
			case Technology::DOCK1_SPEED: $bonusId = PlayerBonus::DOCK1_SPEED; break;
			case Technology::DOCK2_SPEED: $bonusId = PlayerBonus::DOCK2_SPEED; break;
			case Technology::TECHNOSPHERE_SPEED: $bonusId = PlayerBonus::TECHNOSPHERE_SPEED; break;
			case Technology::COMMERCIAL_INCOME: $bonusId = PlayerBonus::COMMERCIAL_INCOME; break;
			case Technology::GRAVIT_MODULE: $bonusId = PlayerBonus::GRAVIT_MODULE; break;
			case Technology::DOCK3_SPEED: $bonusId = PlayerBonus::DOCK3_SPEED; break;
			case Technology::POPULATION_TAX: $bonusId = PlayerBonus::POPULATION_TAX; break;
			case Technology::COMMANDER_INVEST: $bonusId = PlayerBonus::COMMANDER_INVEST; break;
			case Technology::UNI_INVEST: $bonusId = PlayerBonus::UNI_INVEST; break;
			case Technology::ANTISPY_INVEST: $bonusId = PlayerBonus::ANTISPY_INVEST; break;
			case Technology::SPACESHIPS_SPEED: $bonusId = PlayerBonus::SHIP_SPEED; break;
			case Technology::SPACESHIPS_CONTAINER: $bonusId = PlayerBonus::SHIP_CONTAINER; break;
			case Technology::BASE_QUANTITY: $bonusId = PlayerBonus::BASE_QUANTITY; break;
			case Technology::FIGHTER_SPEED: $bonusId = PlayerBonus::FIGHTER_SPEED; break;
			case Technology::FIGHTER_ATTACK: $bonusId = PlayerBonus::FIGHTER_ATTACK; break;
			case Technology::FIGHTER_DEFENSE: $bonusId = PlayerBonus::FIGHTER_DEFENSE; break;
			case Technology::CORVETTE_SPEED: $bonusId = PlayerBonus::CORVETTE_SPEED; break;
			case Technology::CORVETTE_ATTACK: $bonusId = PlayerBonus::CORVETTE_ATTACK; break;
			case Technology::CORVETTE_DEFENSE: $bonusId = PlayerBonus::CORVETTE_DEFENSE; break;
			case Technology::FRIGATE_SPEED: $bonusId = PlayerBonus::FRIGATE_SPEED; break;
			case Technology::FRIGATE_ATTACK: $bonusId = PlayerBonus::FRIGATE_ATTACK; break;
			case Technology::FRIGATE_DEFENSE: $bonusId = PlayerBonus::FRIGATE_DEFENSE; break;
			case Technology::DESTROYER_SPEED: $bonusId = PlayerBonus::DESTROYER_SPEED; break;
			case Technology::DESTROYER_ATTACK: $bonusId = PlayerBonus::DESTROYER_ATTACK; break;
			case Technology::DESTROYER_DEFENSE: $bonusId = PlayerBonus::DESTROYER_DEFENSE; break;
			default:
				throw new ErrorException('mise à jour du bonus de technologie impossible - technologie invalide');
		}
		$this->addTechnoToBonus($playerBonus, $techno, $bonusId);

		if ($playerBonus->synchronized) {
			$totalBonus = 0;
			for ($i = 0; $i <= $playerBonus->technology->getTechnology($techno); $i++) { 
				$totalBonus += $this->technologyHelper->getImprovementPercentage($techno, $i);
			}
			$this->sessionWrapper->get('playerBonus')->add($bonusId, $totalBonus);
		}
	}
}
