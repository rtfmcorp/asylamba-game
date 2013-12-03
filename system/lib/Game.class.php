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

	public static function getTimeToTravel($startPlace, $destinationPlace) {
		if ($startPlace->getRSystem() == $destinationPlace->getRSystem()) {
			$distance = abs($startPlace->getPosition() - $destinationPlace->getPosition());
			$time = round(COEFFMOVEINSYSTEM * $distance);
			return $time;
		} else {
			$time = COEFFMOVEOUTOFSYSTEM;
			$distance = self::getDistance($startPlace->getXSystem(), $destinationPlace->getXSystem(), $startPlace->getYSystem(), $destinationPlace->getYSystem());
			$time += round(COEFFMOVEINTERSYSTEM * $distance);
			return $time;
		}
	}

	public static function getTimeTravelInSystem($startPosition, $destinationPosition) {
		$distance = abs($startPosition - $destinationPosition);
		$time = round(COEFFMOVEINSYSTEM * $distance);
		return $time;
	}

	public static function getTimeTravelOutOfSystem($startX, $startY, $destinationX, $destinationY) {
		$time = COEFFMOVEOUTOFSYSTEM;
		$distance = self::getDistance($startX, $destinationX, $startY, $destinationY);
		$time += round(COEFFMOVEINTERSYSTEM * $distance);
		return $time;
	}

	# for spy
	public static function getTimeToTravelToSystem($startSystem, $destinationSystem) {
		$distance = self::getDistance($startSystem->xPosition, $destinationSystem->xPosition, $startSystem->yPosition, $destinationSystem->yPosition);
		$time = round(SPY_COEFFTIMEMOVE * $distance);
		return $time;
	}

	public static function getPAToTravel($duration) {
		if ($duration < 3600) {
			return 4;
		} else {
			return Format::numberFormat($duration / 1000);
		}
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

	public static function getTaxFromPopulation($population) {
		return ((40 * $population) + 5000) * PAM_COEFTAX;
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
				if ($quantity > 0) {
					$needed = $quantity;
				} else {
					$needed = FALSE;
				}
				break;
			default :
				$needed = FALSE;
				break;
		}
		return $needed;
	}
}