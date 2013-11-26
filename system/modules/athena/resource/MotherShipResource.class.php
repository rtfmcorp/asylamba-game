<?php
class MotherShipResource {
	/**
	 * 8  - refinery
	 * 9  - dock
	 * 10 - historicalCenter
	 * 11 - gateway
	 **/
	private static $motherShipBuildings = array(8, 9, 10, 11);

	 /**
	 * pegase = 0, satyre = 1, chimere = 2, sirene = 3, dryade = 4 and meduse = 5
	 **/
	private static $dockShips = array(0, 1, 2, 3, 4, 5);

	public static function isABuilding($building) {
		return (in_array($building, self::$motherShipBuildings)) ? TRUE : FALSE;
	}

	public static function isAShipFromDock($ship) {
		return (in_array($ship, self::$dockShips)) ? TRUE : FALSE;
	}

	public static function getBuildingInfo($buildingNumber, $info, $level = 0, $sup = 'default') {
		if(self::isABuilding($buildingNumber)) {
			$buildingNumber -= 8;
			if($info == 'name' OR $info == 'frenchName' OR $info == 'imageLink') {
				return self::$building[$buildingNumber][$info];
			} elseif ($info == 'level') {
				if($level == 0) {
					CTR::$alert->add('Il faut spécifier le 3e arguement dans getBuildingInfo de MotherShipResource', ALT_BUG_ERROR);
					return FALSE;
				}
				if($sup == 'time') {
					return self::$building[$buildingNumber][$info][$level-1][0];
				} elseif($sup == 'resourcePrice') {
					return self::$building[$buildingNumber][$info][$level-1][1];
				} else {
					if($sup == 'storageSpace' AND ($buildingNumber == 0 OR $buildingNumber == 1)) {
						return self::$building[$buildingNumber][$info][$level-1][2];
					} elseif($sup == 'refiningCoefficient' AND $buildingNumber == 0) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} elseif($sup == 'releasedShip' AND $buildingNumber == 1) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} elseif($sup == 'researchRate' AND $buildingNumber == 2) {
						return self::$building[$buildingNumber][$info][$level-1][2];
					} elseif($sup == 'protectionRate' AND $buildingNumber == 3) {
						return self::$building[$buildingNumber][$info][$level-1][2];
					} else {
						CTR::$alert->add('4e argument faux dans getBuildingInfo de MotherShipResource', ALT_BUG_ERROR);
					}
				}
			} else {
				CTR::$alert->add('2e arguement invalide dans getBuildingInfo de MotherShipResource', ALT_BUG_ERROR);
			}
		} else {
			CTR::$alert->add('1er argument invalide (entre 8 et 11) dans getBuildingInfo de MotherShipResource', ALT_BUG_ERROR);
		}
		return FALSE;
	}

	private static $building = array(
		array(
			'name' => 'refinery',
			'frenchName' => 'Raffinerie',
			'imageLink' => '...',
			'level' => array(
				// (time, resourcePrice, pa, storageSpace, refiningCoefficient)
				array(1, 2, 10, 2000, 5),
				array(2, 4, 10, 3000, 10),
				array(5, 6, 10, 4000, 15),
				array(10, 11, 10, 5000, 20),
				array(12, 13, 10, 6000, 25),
				array(14, 15, 10, 7000, 30),
				array(17, 18, 10, 8000, 35),
				array(21, 22, 10, 9000, 40),
				array(24, 25, 10, 10000, 45),
				array(27, 50, 10, 12000, 50),
				array(33, 100, 10, 14000, 55),
				array(35, 200, 10, 16000, 60),
				array(43, 500, 10, 18000, 65),
				array(46, 1000, 10, 20000, 70),
				array(51, 2000, 10, 25000, 75),
				array(60, 5000, 10, 30000, 80),
				array(73, 10000, 10, 40000, 85),
				array(80, 15000, 10, 50000, 90),
				array(88, 20000, 10, 60000, 95),
				array(90, 30000, 10, 100000, 100)
			)
		),
		array(
			'name' => 'dock',
			'frenchName' => 'Chantier',
			'imageLink' => '...',
			'level' => array(
				// (time, resourcePrice, storageSpace [en PEV], releasedShip)
				array(10, 100, 20, 1),
				array(18, 200, 30, 1),
				array(30, 300, 40, 1),
				array(50, 400, 50, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2),
				array(60, 500, 60, 2)
			)
		),
		array(
			'name' => 'historicalCenter',
			'frenchName' => 'Centre Historique',
			'imageLink' => '...',
			'level' => array(
				// (time, resourcePrice, researchRate)
				array(15, 150, 5),
				array(30, 300, 10),
				array(60, 600, 15),
				array(120, 1200, 20),
				array(240, 2400, 25),
				array(480, 4800, 30),
				array(960, 9600, 35),
				array(1920, 19200, 40),
				array(3840, 38400, 45),
				array(7680, 76800, 50)
			)
		),
		array(
			'name' => 'gateway',
			'frenchName' => 'Passerelle',
			'imageLink' => '...',
			'level' => array(
				// (time, resourcePrice, protectionRate)
				array(20, 2000, 5),
				array(40, 5000, 10),
				array(60, 10000, 15),
				array(80, 20000, 18),
				array(100, 50000, 20)
			)
		) 
	);
}
?>