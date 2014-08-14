<?php
class Game {
	public static function convertPlaceType($type) {
		switch ($type) {
			case 1 : return 'planète tellurique'; break;
			case 2 : return 'géante gazeuse'; break;
			case 3 : return 'ruine'; break;
			case 4 : return 'poche de gaz'; break;
			case 5 : return 'ceinture d\'astéroides'; break;
			case 6 : return 'zone vide'; break;
			default: return 'rien'; break;
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
		return $coeffRefinery * $coeffPlanet * 1;
	}

	public static function getDistance($xa, $xb, $ya, $yb) {
		$distance = floor((sqrt((($xa - $xb) * ($xa - $xb)) + (($ya - $yb) * ($ya - $yb)))));
		return ($distance < 1) ? 1 : $distance;
	}

	public static function getFleetSpeed() {
		include_once ARES;
		return Commander::FLEETSPEED;
	}

	public static function getMaxTravelDistance() {
		include_once ARES;
		return round((Commander::MAXTRAVELTIME * self::getFleetSpeed()) / COEFFMOVEINTERSYSTEM);
	}

	public static function getTimeToTravel($startPlace, $destinationPlace) {
		# $startPlace and $destinationPlace are instance of Place
		return self::getTimeTravel($startPlace->getRSystem(), $startPlace->getPosition(), $startPlace->getXSystem(), $startPlace->getYSystem(),
									$destinationPlace->getRSystem(), $destinationPlace->getPosition(), $destinationPlace->getXSystem(), $destinationPlace->getYSystem());
	}

	public static function getTimeTravel($systemFrom, $positionFrom, $xFrom, $yFrom, $systemTo, $positionTo, $xTo, $yTo) {
		include_once ARES;
		if ($systemFrom == $systemTo) {
			$distance = abs($positionFrom - $positionTo);
			$time = round(Commander::COEFFMOVEINSYSTEM * $distance);
			return $time;
		} else {
			$time = Commander::COEFFMOVEOUTOFSYSTEM;
			$distance = self::getDistance($xFrom, $xTo, $yFrom, $yTo);
			$time += round(Commander::COEFFMOVEINTERSYSTEM * $distance) / self::getFleetSpeed();
			return $time;
		}
	}

	public static function getTimeTravelInSystem($startPosition, $destinationPosition) {
		include_once ARES;
		$distance = abs($startPosition - $destinationPosition);
		$time = round(Commander::COEFFMOVEINSYSTEM * $distance);
		return $time;
	}

	public static function getTimeTravelOutOfSystem($startX, $startY, $destinationX, $destinationY) {
		include_once ARES;
		$time = Commander::COEFFMOVEOUTOFSYSTEM;
		$distance = self::getDistance($startX, $destinationX, $startY, $destinationY);
		$time += round(Commander::COEFFMOVEINTERSYSTEM * $distance) / self::getFleetSpeed;
		return $time;
	}

	public static function getRCPrice($distance, $populationA, $populationB, $coef) {
		return round($distance * ($populationA + $populationB) * $coef);
	}

	public static function getRCIncome($distance, $populationA, $populationB, $coef, $bonusA = 1, $bonusB = 1) {
		return round($distance * ($populationA + $populationB) * $coef * $bonusA * $bonusB);
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

	public static function getTaxFromPopulation($population, $typeOfBase) {
		$tax = ((40 * $population) + 5000) * PAM_COEFTAX;
		$tax *= PlaceResource::get($typeOfBase, 'tax');
		return $tax;
	}

	public static function getAntiSpyRadius($investment, $mode = ANTISPY_DISPLAY_MODE) {
		if ($mode == ANTISPY_DISPLAY_MODE) {
			// en pixels : sert à l'affichage
			return sqrt($investment / 3.14) * 20;
		} else { // ANTISPY_GAME_MODE
			// en position du jeu (250x250)
			return sqrt($investment / 3.14);
		}
	}

	public static function antiSpyArea($startPlace, $destinationPlace, $arrivalDate) {
		if ($startPlace->getRSystem() == $destinationPlace->getRSystem()) {
			return ANTISPY_LITTLE_CIRCLE; // dans le même système
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
				} else if ($distanceRemaining < $antiSpyRadius / 3 * 2) {
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
		if ($startPlace->getRSystem() == $destinationPlace->getRSystem()) {
			return array(TRUE, TRUE, TRUE); // dans le même système
		} else {
			$duration = self::getTimeToTravel($startPlace, $destinationPlace);

			$secRemaining = strtotime($arrivalDate) - strtotime(Utils::now());
			$ratioRemaining = $secRemaining / $duration;

			$distance = self::getDistance($startPlace->getXSystem(), $destinationPlace->getXSystem(), $startPlace->getYSystem(), $destinationPlace->getYSystem());
			$distanceRemaining = $distance * $ratioRemaining;

			$antiSpyRadius = self::getAntiSpyRadius($destinationPlace->getAntiSpyInvest(), 1);

			if ($distanceRemaining < $antiSpyRadius / 3) {
				return array(TRUE, TRUE, TRUE);
			} else if ($distanceRemaining < $antiSpyRadius / 3 * 2) {
				$ratio = ($antiSpyRadius / 3) / $distanceRemaining;
				$sec = $ratio * $secRemaining;
				$newDate = Utils::addSecondsToDate($arrivalDate, -$sec);

				return array(TRUE, TRUE, $newDate);
			} else if ($distanceRemaining < $antiSpyRadius) {
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
				return ($thisRate + (99 * $currentRate)) / 100;
				break;
			case Transaction::TYP_SHIP :
				# 1 resource = x credit
				include_once ATHENA;
				if (ShipResource::isAShip($identifier)) {
					$resourceQuantity = ShipResource::getInfo($identifier, 'resourcePrice') * $quantity;
					$thisRate = $price / $resourceQuantity;
					# dilution of 1%
					return ($thisRate + (99 * $currentRate)) / 100;
				} else {
					return FALSE;
				}
				break;
			case Transaction::TYP_COMMANDER :
				# 1 experience = x credit
				$thisRate = $price / $quantity;
				# dilution of 1%
				return ($thisRate + (99 * $currentRate)) / 100;
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

		$price = $quantity * $minRate;
		return round($price);
	}

	public static function getSpySuccess($antiSpy, $priceInvested) {
		# spy success must be between 0 and 100
		$ratio = $priceInvested / $antiSpy;
		if ($ratio > 2) {
			return 100;
		} else {
			return round($ratio * 50);
		}
	}

	public static function getTypeOfSpy($success) {
		include_once ARTEMIS;
		$percent = rand(0, 100);
		if ($success < 50) {
			if ($percent < 50) {
				return SpyReport::TYP_NOT_CAUGHT;			# 50%
			} else if ($percent < 75) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 25%
			} else {
				return SpyReport::TYP_CAUGHT;				# 25%
			}
		} else {
			if ($percent < 40) {
				return SpyReport::TYP_NOT_CAUGHT;			# 40%
			} else if ($percent < 70) {
				return SpyReport::TYP_ANONYMOUSLY_CAUGHT;	# 30%
			} else {
				return SpyReport::TYP_CAUGHT;				# 30%
			}
		}
	}
}