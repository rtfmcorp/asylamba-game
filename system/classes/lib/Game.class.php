<?php
class Game {

	const COMMERCIAL_TIME_TRAVEL = 0.2;

	public static function convertPlaceType($type) {
		switch ($type) {
			case 1 : return 'planète tellurique'; break;
			case 2 : return 'géante gazeuse'; break;
			case 3 : return 'ruine'; break;
			case 4 : return 'poche de gaz'; break;
			case 5 : return 'ceinture d\'astéroïdes'; break;
			case 6 : return 'zone vide'; break;
			default: return 'rien'; break;
		}
	}

	public static function getSizeOfPlanet($population) {
		if ($population < 100) {
			return 1;
		} elseif ($population < 200) {
			return 2;
		} else {
			return 3;
		}
	}

	public static function formatCoord($xCoord, $yCoord, $planetPosition = 0, $sectorLocation = 0) {
		if ($sectorLocation > 0) {
			return '⟨' . $sectorLocation . '⟩ ' . $xCoord . ':' . $yCoord . ':' . $planetPosition . '';
		} elseif ($planetPosition > 0) {
			return $xCoord . ':' . $yCoord . ':' . $planetPosition;
		} else {
			return $xCoord . ':' . $yCoord;
		}
	}

	public static function resourceProduction($coeffRefinery, $coeffPlanet) {
		return $coeffRefinery * $coeffPlanet;
	}

	public static function getDistance($xa, $xb, $ya, $yb) {
		$distance = floor((sqrt((($xa - $xb) * ($xa - $xb)) + (($ya - $yb) * ($ya - $yb)))));
		return ($distance < 1) ? 1 : $distance;
	}

	public static function getFleetSpeed($bonus) {
		include_once ARES;

		$b = $bonus != NULL
			? Commander::FLEETSPEED * (3 * ($bonus->get(PlayerBonus::SHIP_SPEED) / 100)) : 0;
		return Commander::FLEETSPEED + $b;
	}

	public static function getMaxTravelDistance($bonus) {
		include_once ARES;

		return Commander::DISTANCEMAX;
	}

	public static function getTimeToTravelCommercial($startPlace, $destinationPlace, $bonus = NULL) {
		return round(self::getTimeToTravel($startPlace, $destinationPlace, $bonus) * self::COMMERCIAL_TIME_TRAVEL);
	}

	public static function getTimeToTravel($startPlace, $destinationPlace, $bonus = NULL) {
		# $startPlace and $destinationPlace are instance of Place
		return self::getTimeTravel(
			$startPlace->getRSystem(),
			$startPlace->getPosition(),
			$startPlace->getXSystem(),
			$startPlace->getYSystem(),
			$destinationPlace->getRSystem(),
			$destinationPlace->getPosition(),
			$destinationPlace->getXSystem(),
			$destinationPlace->getYSystem(),
			$bonus
		);
	}

	public static function getTimeTravelCommercial($systemFrom, $positionFrom, $xFrom, $yFrom, $systemTo, $positionTo, $xTo, $yTo, $bonus = NULL) {
		return round(self::getTimeTravel($systemFrom, $positionFrom, $xFrom, $yFrom, $systemTo, $positionTo, $xTo, $yTo, $bonus) * self::COMMERCIAL_TIME_TRAVEL);
	}

	public static function getTimeTravel($systemFrom, $positionFrom, $xFrom, $yFrom, $systemTo, $positionTo, $xTo, $yTo, $bonus = NULL) {
		return $systemFrom == $systemTo
			? Game::getTimeTravelInSystem($positionFrom, $positionTo)
			: Game::getTimeTravelOutOfSystem($bonus, $xFrom, $yFrom, $xTo, $yTo);
	}

	public static function getTimeTravelInSystem($startPosition, $destinationPosition) {
		include_once ARES;

		$distance = abs($startPosition - $destinationPosition);
		return round((Commander::COEFFMOVEINSYSTEM * $distance) * ((40 - $distance) / 50) + 180);
	}

	public static function getTimeTravelOutOfSystem($bonus, $startX, $startY, $destinationX, $destinationY) {
		include_once ARES;

		$distance = self::getDistance($startX, $destinationX, $startY, $destinationY);
		$time  = Commander::COEFFMOVEOUTOFSYSTEM;
		$time += round((Commander::COEFFMOVEINTERSYSTEM * $distance) / self::getFleetSpeed($bonus));
		return $time;
	}

	public static function getRCPrice($distance) {
		include_once ATHENA;

		return $distance * CommercialRoute::COEF_PRICE;
	}

	public static function getRCIncome($distance, $bonusA = 1, $bonusB = 1) {
		include_once ATHENA;

		$income = CommercialRoute::COEF_INCOME_2 * sqrt($distance * CommercialRoute::COEF_INCOME_1);
		$maxIncome = CommercialRoute::COEF_INCOME_2 * sqrt(100 * CommercialRoute::COEF_INCOME_1);
		if ($income > $maxIncome) {
			$income = $maxIncome;
		}
		return round($income * $bonusA * $bonusB);
	}

 	public static function getTaxFromPopulation($population, $typeOfBase) {
		$tax  = ((180 * $population) + 1500) * PAM_COEFTAX;
		$tax *= PlaceResource::get($typeOfBase, 'tax');
		return $tax;
	}

	public static function getAntiSpyRadius($investment, $mode = ANTISPY_DISPLAY_MODE) {
		return $mode == ANTISPY_DISPLAY_MODE
			# en pixels : sert à l'affichage
			? sqrt($investment / 3.14) * 20
			# en position du jeu (250x250)
			: sqrt($investment / 3.14);
	}

	public static function antiSpyArea($startPlace, $destinationPlace, $arrivalDate) {
		# dans le même système
		if ($startPlace->getRSystem() == $destinationPlace->getRSystem()) {
			return ANTISPY_LITTLE_CIRCLE; 
		} else {
			$duration = self::getTimeToTravel($startPlace, $destinationPlace);

			$secRemaining = strtotime($arrivalDate) - strtotime(Utils::now());
			$ratioRemaining = $secRemaining / $duration;

			$distance = self::getDistance($startPlace->getXSystem(), $destinationPlace->getXSystem(), $startPlace->getYSystem(), $destinationPlace->getYSystem());
			$distanceRemaining = $distance * $ratioRemaining;

			$antiSpyRadius = self::getAntiSpyRadius($destinationPlace->getAntiSpyInvest(), 1);

			if ($antiSpyRadius >= $distanceRemaining) {
				if ($distanceRemaining < $antiSpyRadius / 3) {
					return ANTISPY_LITTLE_CIRCLE; 
				} elseif ($distanceRemaining < $antiSpyRadius / 3 * 2) {
					return ANTISPY_MIDDLE_CIRCLE;
				} else {
					return ANTISPY_BIG_CIRCLE;
				}
			} else {
				return ANTISPY_OUT_OF_CIRCLE;
			}
		}
	}

	public static function getAntiSpyEntryTime($startPlace, $destinationPlace, $arrivalDate) {
		# dans le même système
		if ($startPlace->getRSystem() == $destinationPlace->getRSystem()) {
			return array(TRUE, TRUE, TRUE);
		} else {
			$duration = self::getTimeToTravel($startPlace, $destinationPlace);

			$secRemaining = strtotime($arrivalDate) - strtotime(Utils::now());
			$ratioRemaining = $secRemaining / $duration;

			$distance = self::getDistance($startPlace->getXSystem(), $destinationPlace->getXSystem(), $startPlace->getYSystem(), $destinationPlace->getYSystem());
			$distanceRemaining = $distance * $ratioRemaining;

			$antiSpyRadius = self::getAntiSpyRadius($destinationPlace->getAntiSpyInvest(), 1);

			if ($distanceRemaining < $antiSpyRadius / 3) {
				return array(TRUE, TRUE, TRUE);
			} elseif ($distanceRemaining < $antiSpyRadius / 3 * 2) {
				$ratio = ($antiSpyRadius / 3) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate = Utils::addSecondsToDate($arrivalDate, -$sec);

				return array(TRUE, TRUE, $newDate);
			} elseif ($distanceRemaining < $antiSpyRadius) {
				$ratio = ($antiSpyRadius / 3 * 2) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate1 = Utils::addSecondsToDate($arrivalDate, -$sec);

				$ratio = ($antiSpyRadius / 3) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate2 = Utils::addSecondsToDate($arrivalDate, -$sec);
				
				return array(TRUE, $newDate1, $newDate2);
			} else {
				$ratio = $antiSpyRadius / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate1 = Utils::addSecondsToDate($arrivalDate, -$sec);

				$ratio = ($antiSpyRadius / 3 * 2) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate2 = Utils::addSecondsToDate($arrivalDate, -$sec);

				$ratio = ($antiSpyRadius / 3) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate3 = Utils::addSecondsToDate($arrivalDate, -$sec);
				
				return array($newDate1, $newDate2, $newDate3);
			}
		}
	}

	public static function getCommercialShipQuantityNeeded($transactionType, $quantity, $identifier = 0) {
		include_once ATHENA;
		switch ($transactionType) {
			case Transaction::TYP_RESOURCE : 
				# 1000 ressources => 1 commercialShip
				$needed = ceil($quantity / 1000);
				break;
			case Transaction::TYP_SHIP :
				# 1 PEV => 1 commercialShip
				if (ShipResource::isAShip($identifier) AND $quantity > 0) {
					$needed = $quantity * ShipResource::getInfo($identifier, 'pev');
				} else {
					$needed = FALSE;
				}
				break;
			case Transaction::TYP_COMMANDER :
				# 1 commander => 1 commercialShip
				$needed = 1;
				break;
			default :
				$needed = FALSE;
				break;
		}
		return $needed;
	}

	public static function calculateCurrentRate($currentRate, $transactionType, $quantity, $identifier, $price) {
		# calculate the new rate (when a transaction is accepted)
		switch ($transactionType) {
			case Transaction::TYP_RESOURCE :
				# 1 resource = x credit
				$thisRate = $price / $quantity;
				# dilution of 1%
				$newRate = (($quantity * $thisRate) + (50000 * (99 * $currentRate)) / 100) / (50000 + $quantity);
				return max($newRate, Transaction::MIN_RATE_RESOURCE);
				break;
			case Transaction::TYP_SHIP :
				# 1 resource = x credit
				include_once ATHENA;
				if (ShipResource::isAShip($identifier)) {
					$resourceQuantity = ShipResource::getInfo($identifier, 'resourcePrice') * $quantity;
					$thisRate = $price / $resourceQuantity;
					# dilution of 1%
					$newRate = (($resourceQuantity * $thisRate) + (50000 * (99 * $currentRate)) / 100) / (50000 + $resourceQuantity);
					return max($newRate, Transaction::MIN_RATE_SHIP);
				} else {
					return FALSE;
				}
				break;
			case Transaction::TYP_COMMANDER :
				# 1 experience = x credit
				$thisRate = $price / $quantity;
				# dilution of 1%
				$newRate = (($quantity * $thisRate) + (50000 * (99 * $currentRate)) / 100) / (50000 + $quantity);
				return max($newRate, Transaction::MIN_RATE_COMMANDER);
				break;
			default :
				return 0;
				break;
		}
	}

	public static function calculateRate($transactionType, $quantity, $identifier, $price) {
		switch ($transactionType) {
			case Transaction::TYP_RESOURCE :
				# 1 resource = x credit
				return $price / $quantity;
				break;
			case Transaction::TYP_SHIP :
				# 1 resource = x credit
				include_once ATHENA;
				if (ShipResource::isAShip($identifier)) {
					$resourceQuantity = ShipResource::getInfo($identifier, 'resourcePrice') * $quantity;
					return $price / $resourceQuantity;
				} else {
					return FALSE;
				}
				break;
			case Transaction::TYP_COMMANDER :
				# 1 experience = x credit
				return $price / $quantity;
				break;
			default :
				return FALSE;
				break;
		}
	}

	public static function getMinPriceRelativeToRate($transactionType, $quantity, $identifier = FALSE) {
		switch ($transactionType) {
			case Transaction::TYP_RESOURCE:
				$minRate = Transaction::MIN_RATE_RESOURCE;
				break;
			case Transaction::TYP_SHIP:
				include_once ATHENA;
				$minRate = Transaction::MIN_RATE_SHIP;
				$quantity = ShipResource::getInfo($identifier, 'resourcePrice') * $quantity;
				break;
			case Transaction::TYP_COMMANDER:
				$minRate = Transaction::MIN_RATE_COMMANDER;
				break;
			default:
				return FALSE;
		}

		$price = round($quantity * $minRate);
		if ($price < 1) {
			$price = 1;
		}
		return $price;
	}

	public static function getMaxPriceRelativeToRate($transactionType, $quantity, $identifier = FALSE) {
		switch ($transactionType) {
			case Transaction::TYP_RESOURCE:
				$minRate = Transaction::MAX_RATE_RESOURCE;
				break;
			case Transaction::TYP_SHIP:
				include_once ATHENA;
				$minRate = Transaction::MAX_RATE_SHIP;
				$quantity = ShipResource::getInfo($identifier, 'resourcePrice') * $quantity;
				break;
			case Transaction::TYP_COMMANDER:
				$minRate = Transaction::MAX_RATE_COMMANDER;
				break;
			default:
				return FALSE;
		}

		$price = $quantity * $minRate;
		return round($price);
	}

	public static function getSpySuccess($antiSpy, $priceInvested) {
		# spy success must be between 0 and 100
		$antiSpy = $antiSpy == 0 ? 1 : $antiSpy;
		$ratio   = $priceInvested / $antiSpy;
		$percent = round($ratio * 33);
		# ça veut dire qu'il payer 3x plus que ce que le gars investi pour tout voir
		if ($percent > 100) {
			$percent = 100;
		}
		return $percent;
	}

	public static function getTypeOfSpy($success, $antiSpy) {
		include_once ARTEMIS;
		if ($antiSpy < 1000) {
			return SpyReport::TYP_NOT_CAUGHT;
		}

		$percent = rand(0, 100);
		if ($success < 40) {
			if ($percent < 5) {
				return SpyReport::TYP_NOT_CAUGHT;			# 5%
			} elseif ($percent < 50) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 45%
			} else {
				return SpyReport::TYP_CAUGHT;				# 50%
			}
		} else if ($success < 80) {
			if ($percent < 30) {
				return SpyReport::TYP_NOT_CAUGHT;			# 30%
			} elseif ($percent < 60) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 30%
			} else {
				return SpyReport::TYP_CAUGHT;				# 40%
			}
		} else if ($success < 100) {
			if ($percent < 50) {
				return SpyReport::TYP_NOT_CAUGHT;			# 50%
			} elseif ($percent < 80) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 30%
			} else {
				return SpyReport::TYP_CAUGHT;				# 20%
			}
		} else { # success == 100
			if ($percent < 70) {
				return SpyReport::TYP_NOT_CAUGHT;			# 70%
			} elseif ($percent < 90) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 20%
			} else {
				return SpyReport::TYP_CAUGHT;				# 10%
			}
		}
	}

	public static function getImprovementFromScientificCoef($coef) {
		# transform scientific coefficient of a place 
		# into improvement coefficient for the technosphere
		if ($coef < 10) {
			return 0;
		} elseif ($coef >= 100) {
			return 40;
		} else {
			return ceil(0.004 * $coef * $coef - 0.01 * $coef + 0.7);
		}
	}

	public static function getFleetCost($ships, $mode='affected') {
		include_once ATHENA;
		$cost = 0;
		for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
			$cost += ShipResource::getInfo($i, 'cost') * $ships[$i];
		}
		if ($mode != 'affected') {
			$cost *= ShipResource::COST_REDUCTION;
		} 
		return ceil($cost);
	}
}