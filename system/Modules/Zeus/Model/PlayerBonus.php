<?php

/**
 * Player Bonus
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @update 18.07.13
*/

namespace Asylamba\Modules\Zeus\Model;

class PlayerBonus {
	// ATTRIBUTES
	public $rPlayer;
	public $synchronized = FALSE;
	public $technology;
	public $bonus;
	public $playerColor;

	// CONSTANTS 
	const BONUS_QUANTITY = 28;
	// 28 bonus de technos
	const GENERATOR_SPEED = 0;
	const REFINERY_REFINING = 1;
	const REFINERY_STORAGE = 2;
	const DOCK1_SPEED = 3;
	const DOCK2_SPEED = 4;
	const TECHNOSPHERE_SPEED = 5;
	const COMMERCIAL_INCOME = 6;
	const GRAVIT_MODULE = 7;
	const DOCK3_SPEED = 8;
	const POPULATION_TAX = 9;
	const COMMANDER_INVEST = 10;
	const UNI_INVEST = 11;
	const ANTISPY_INVEST = 12;
	const SHIP_SPEED = 13; # vitesse de déplacement
	const SHIP_CONTAINER = 14;
	const BASE_QUANTITY = 15;
	const FIGHTER_SPEED = 16;
	const FIGHTER_ATTACK = 17;
	const FIGHTER_DEFENSE = 18;
	const CORVETTE_SPEED = 19;
	const CORVETTE_ATTACK = 20;
	const CORVETTE_DEFENSE = 21;
	const FRIGATE_SPEED = 22;
	const FRIGATE_ATTACK = 23;
	const FRIGATE_DEFENSE = 24;
	const DESTROYER_SPEED = 25;
	const DESTROYER_ATTACK = 26;
	const DESTROYER_DEFENSE = 27;
}