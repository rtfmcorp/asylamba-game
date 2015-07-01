<?php
class OrbitalBaseResource {

	const GENERATOR = 0;
	const REFINERY = 1;
	const DOCK1 = 2;
	const DOCK2 = 3;
	const DOCK3 = 4;
	const TECHNOSPHERE = 5;
	const COMMERCIAL_PLATEFORME = 6;
	const STORAGE = 7;
	const RECYCLING = 8;
	const SPATIOPORT = 9;

	const BUILDING_QUANTITY = 10;

	/**
	 * 0 - generator
	 * 1 - refinery
	 * 2 - dock1
	 * 3 - dock2
	 * 4 - dock3
	 * 5 - technosphere
	 * 6 - commercialPlateforme
	 * 7 - storage
	 * 8 - recycling
	 * 9 - spatioport
	 **/
	private static $orbitalBaseBuildings = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

	/**
	 * pegase = 0, satyre = 1, chimere = 2, sirene = 3, dryade = 4 and meduse = 5
	 **/
	private static $dock1Ships = array(0, 1, 2, 3, 4, 5);

	/**
	 * griffon = 6, cyclope = 7, minotaure = 8, hydre = 9, cerbere = 10, phenix = 11
	 **/
	private static $dock2Ships = array(6, 7, 8, 9, 10, 11);

	/**
	 * motherShip1 = 12, motherShip2 = 13, motherShip3 = 14
	 **/
	private static $dock3Ships = array(12, 13, 14);

	public static function isABuilding($building) {
		return (in_array($building, self::$orbitalBaseBuildings)) ? TRUE : FALSE;
	}

	public static function isAShipFromDock1($ship) {
		return (in_array($ship, self::$dock1Ships)) ? TRUE : FALSE;
	}

	public static function isAShipFromDock2($ship) {
		return (in_array($ship, self::$dock2Ships)) ? TRUE : FALSE;
	}

	public static function isAShipFromDock3($ship) {
		return (in_array($ship, self::$dock3Ships)) ? TRUE : FALSE;
	}

	public static function fleetQuantity($typeOfBase) {
		switch ($typeOfBase) {
			case OrbitalBase::TYP_NEUTRAL:
				return 2; break;
			case OrbitalBase::TYP_COMMERCIAL:
				return 2; break;
			case OrbitalBase::TYP_MILITARY:
				return 5; break;
			case OrbitalBase::TYP_CAPITAL:
				return 5; break;
			default:
				return 0; break;
		}
	}

	public static function getInfo($buildingNumber, $info, $level = 0, $sup = 'default') {
		return self::getBuildingInfo($buildingNumber, $info, $level, $sup);
	}
	
	public static function getBuildingInfo($buildingNumber, $info, $level = 0, $sup = 'default') {
		if(self::isABuilding($buildingNumber)) {
			if ($info == 'name' OR $info == 'frenchName' OR $info == 'imageLink' OR $info == 'description') {
				return self::$building[$buildingNumber][$info];
			} elseif ($info == 'techno') {
				if (in_array($buildingNumber, array(3,4,6,8,9))) {
					return self::$building[$buildingNumber][$info];
				} else {
					return -1;
				}
			} elseif ($info == 'maxLevel') {
				# $level is the type of the base
				return self::$building[$buildingNumber][$info][$level];

			} elseif ($info == 'level') {
				if ($level <= 0 OR $level > count(self::$building[$buildingNumber]['level'])) {
					return FALSE;
				}
				if ($sup == 'time') {
					return self::$building[$buildingNumber][$info][$level-1][0];
				} elseif($sup == 'resourcePrice') {
					return self::$building[$buildingNumber][$info][$level-1][1];
				} elseif($sup == 'points') {
					return self::$building[$buildingNumber][$info][$level-1][2];
				} else {
					if ($sup == 'nbQueues') {
						if ($buildingNumber == 0 OR $buildingNumber == 2 OR $buildingNumber == 3 OR $buildingNumber == 5) {
							return self::$building[$buildingNumber][$info][$level-1][3];
						} 
					} elseif ($sup == 'storageSpace') {
						if ($buildingNumber == 7) {
							return self::$building[$buildingNumber][$info][$level-1][3];
						} elseif ($buildingNumber == 2 OR $buildingNumber == 3) {
							return self::$building[$buildingNumber][$info][$level-1][4];
						}
					} elseif ($sup == 'refiningCoefficient' AND $buildingNumber == 1) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} elseif ($sup == 'releasedShip' AND ($buildingNumber == 2 OR $buildingNumber == 3)) {
						return self::$building[$buildingNumber][$info][$level-1][5];
					} elseif ($sup == 'releasedShip' AND $buildingNumber == 4) {
						return self::$building[$buildingNumber][$info][$level-1][4];
					} elseif ($sup == 'nbCommercialShip' AND $buildingNumber == 6) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} elseif ($sup == 'nbRecyclers' AND $buildingNumber == 8) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} elseif ($sup == 'nbRoutesMax' AND $buildingNumber == 9) {
						return self::$building[$buildingNumber][$info][$level-1][3];
					} else {
						CTR::$alert->add('4e argument invalide dans getBuildingInfo de OrbitalBaseResource', ALT_BUG_ERROR);
					}
				}
			} else {
				CTR::$alert->add('2e argument invalide dans getBuildingInfo de OrbitalBaseResource', ALT_BUG_ERROR);
			}
		} else {
			CTR::$alert->add('1er argument invalide (entre 0 et 7) dans getBuildingInfo de OrbitalBaseResource', ALT_BUG_ERROR);
		}
		return FALSE;
	}

	public static function haveRights($buildingId, $level, $type, $sup) {
		if (self::isABuilding($buildingId)) {
			switch($type) {
				// assez de ressources pour contruire ?
				case 'resource' : 
					return ($sup < self::getBuildingInfo($buildingId, 'level', $level, 'resourcePrice')) ? FALSE : TRUE;
					break;
				// encore de la place dans la queue ?
				// $sup est le nombre de batiments dans la queue
				case 'queue' :
					// $buildingId n'est pas utilisé
					return ($sup < self::getBuildingInfo($buildingId, 'level', $level, 'nbQueues')) ? TRUE : FALSE;
					break;
				// droit de construire le batiment ?
				// $sup est un objet de type OrbitalBase
				case 'buildingTree' :
					$diminution = NULL;
					switch ($buildingId) {
						case self::GENERATOR : 
							$diminution = 0;
							break;
						case self::REFINERY :
							$diminution = 0;
							break;
						case self::DOCK1 :
							$diminution = 0;
							break;
						case self::DOCK2 :
							$diminution = 20;
							break;
						case self::DOCK3 :
							$diminution = 30;
							break;
						case self::TECHNOSPHERE : 
							$diminution = 0;
							break;
						case self::COMMERCIAL_PLATEFORME :
							$diminution = 10;
							break;
						case self::STORAGE : 
							$diminution = 0;
							break;
						case self::RECYCLING : 
							$diminution = 10;
							break;
						case self::SPATIOPORT : 
							$diminution = 20;
							break;
						default :
							CTR::$alert->add('buildingId invalide (entre 0 et 9) dans haveRights de OrbitalBaseResource', ALT_BUG_ERROR);
							break;
					}
					if ($diminution !== NULL) {
						if ($buildingId == self::GENERATOR) {
							if ($level > self::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
								return 'niveau maximum atteint';
							} else {
								return TRUE;
							}
						} else {
							if ($level == 1 AND $sup->typeOfBase == OrbitalBase::TYP_NEUTRAL AND ($buildingId == self::SPATIOPORT OR $buildingId == self::DOCK2)) {
								return 'vous devez évoluer votre colonie pour débloquer ce bâtiment';
							}
							if ($level > self::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
								return 'niveau maximum atteint';
							} elseif ($level > ($sup->realGeneratorLevel - $diminution)) {
								return 'le niveau du générateur n\'est pas assez élevé';
							} else {
								return TRUE;
							}
						}
					}
					break;
				// a la technologie pour construire ce bâtiment ?
				// $sup est un objet de type Technology
				case 'techno' : 
					if (self::getBuildingInfo($buildingId, 'techno') == -1) { return TRUE; }
					if ($sup->getTechnology(self::getBuildingInfo($buildingId, 'techno')) == 1) {
						return TRUE;
					} else { 
						return 'il vous faut développer la technologie ' . TechnologyResource::getInfo(self::getBuildingInfo($buildingId, 'techno'), 'name'); 
					}
					break;
				default :
					CTR::$alert->add('$type invalide (entre 1 et 4) dans haveRights de OrbitalBaseResource', ALT_BUG_ERROR);
					return FALSE;
			}
		} else {
			CTR::$alert->add('buildingId invalide (entre 0 et 9) dans haveRights de OrbitalBaseResource', ALT_BUG_ERROR);
			return FALSE;
		}
	}
	
	private static $building = array(
		array(
			'name' => 'generator',
			'frenchName' => 'Générateur',
			'imageLink' => 'generator',
			'level' => array(
				// (time, resourcePrice, points, queues)
				array(20,		100,	2,		2),
				array(28,		137,	2,		2),
				array(39,		188,	2,		2),
				array(55,		257,	3,		2),
				array(77,		352,	3,		2),
				array(108,		483,	3,		3),
				array(151,		661,	4,		3),
				array(211,		900,	4,		3),
				array(295,		1200,	5,		3),
				array(413,		1600,	5,		3),
				array(578,		2200,	6,		3),
				array(809,		3000,	6,		3),
				array(1133,		4100,	7,		3),
				array(1586,		5600,	8,		3),
				array(2220,		7700,	9,		3),
				array(3108,		10000,	10,		4),
				array(4351,		13000,	11,		4),
				array(6091,		16000,	12,		4),
				array(8527,		20000,	14,		4),
				array(9810,		25000,	15,		4),
				array(11280,	31000,	17,		4),
				array(12970,	39000,	19,		4),
				array(14920,	49000,	21,		4),
				array(17160,	61000,	23,		4),
				array(19730,	76000,	26,		4),
				array(22690,	87000,	28,		5),
				array(26090,	100000,	32,		5),
				array(30000,	115000,	35,		5),
				array(34500,	132000,	39,		5),
				array(39680,	152000,	43,		5),
				array(45630,	175000,	48,		5),
				array(52470,	201000,	54,		5),
				array(60340,	231000,	60,		5),
				array(69390,	266000,	66,		5),
				array(79800,	306000,	74,		5),
				array(91770,	352000,	82,		6),
				array(105540,	405000,	91,		6),
				array(121370,	466000,	102,	6),
				array(139580,	536000,	113,	6),
				array(160520,	616000,	126,	6)
			),
			'maxLevel' => array(30, 40, 40, 40),
			'description' => 'le <strong>Générateur</strong> est le centre névralgique de votre base orbitale. C\'est la super-structure qui permet de construire les autres modules nécessaires à la bonne marche de votre planète. Il vous permet de construire les autres bâtiments ainsi que d\'avoir un aperçu rapide du développement de votre base. De plus, aucun bâtiment ne peut avoir un niveau supérieur à celui de votre générateur.<br /><br />'
		),
		array(
			'name' => 'refinery',
			'frenchName' => 'Raffinerie',
			'imageLink' => 'refinery',
			'level' => array(
				// (time, resourcePrice, points, refiningCoefficient)
				array(11,		50,		1,	8),
				array(15,		70,		1,	9),
				array(21,		100,	1,	10.2),
				array(29,		140,	1,	11.5),
				array(41,		200,	2,	13),
				array(57,		280,	2,	14.7),
				array(80,		390,	2,	16.7),
				array(110,		550,	2,	18.8),
				array(150,		770,	2,	21.3),
				array(210,		1080,	3,	24),
				array(290,		1510,	3,	27.2),
				array(410,		2110,	3,	30.7),
				array(570,		2950,	4,	34.7),
				array(800,		4130,	4,	39.2),
				array(1120,		5780,	5,	44.3),
				array(1570,		7200,	5,	50),
				array(2200,		9000,	6,	56.5),
				array(3080,		11000,	7,	63.9),
				array(4310,		14000,	8,	72.2),
				array(4960,		18000,	9,	81.6),
				array(5700,		23000,	10,	89.7),
				array(6560,		29000,	11,	98.3),
				array(7540,		36000,	12,	107.3),
				array(8670,		45000,	14,	116.9),
				array(9970,		56000,	15,	125.1),
				array(11470,	64000,	17,	134.9),
				array(13190,	74000,	19,	144.3),
				array(15170,	85000,	21,	153.5),
				array(17450,	98000,	24,	164.1),
				array(20070,	113000,	27,	173.5),
				array(23080,	130000,	30,	182.7),
				array(26540,	150000,	34,	291.8),
				array(30520,	173000,	38,	200.9),
				array(35100,	199000,	42,	210.3),
				array(40370,	229000,	47,	219.4),
				array(46430,	252000,	53,	228.6),
				array(53390,	277000,	59,	237.3),
				array(61400,	305000,	66,	246.9),
				array(70610,	336000,	74,	257.4),
				array(81200,	370000,	83,	267.3)
			),
			'maxLevel' => array(30, 40, 30, 40),
			'description' => 'La <strong>Raffinerie</strong> est le bâtiment où l’on traite vos ressources pour en extraire les fractions utilisables. Chaque relève les ressources sont transférées dans le Stockage et sont utilisables. La capacité d’extraction de votre raffinerie dépend du niveau dans lequel elle se situe.<br /><br />Aucune action directe ne peut être effectuée dans la raffinerie, cependant vous pouvez y voir toutes les informations concernant votre production actuelle et pour les niveaux suivants.'
		),
		array(
			'name' => 'dock1',
			'frenchName' => 'Chantier Alpha',
			'imageLink' => 'dock1',
			'level' => array(
				// (time, resourcePrice, points, queues, storageSpace[en PEV], releasedShip)
				array(12,		45,		1,	1,	40,		1),
				array(17,		60,		1,	1,	42,		1),
				array(24,		80,		1,	1,	44,		1),
				array(34,		110,	1,	1,	47,		1),
				array(48,		150,	2,	1,	50,		1),
				array(67,		210,	2,	2,	54,		1),
				array(94,		290,	2,	2,	58,		1),
				array(130,		410,	2,	2,	63,		1),
				array(180,		570,	2,	2,	68,		2),
				array(250,		800,	3,	2,	74,		2),
				array(350,		1120,	3,	3,	82,		2),
				array(490,		1570,	3,	3,	90,		2),
				array(690,		2200,	4,	3,	101,	2),
				array(970,		3080,	4,	3,	116,	2),
				array(1360,		4310,	5,	3,	131,	2),
				array(1900,		5400,	5,	4,	147,	2),
				array(2660,		6750,	6,	4,	164,	3),
				array(3720,		8000,	7,	4,	182,	3),
				array(5210,		10000,	8,	4,	200,	3),
				array(5990,		13000,	9,	4,	220,	3),
				array(6890,		16000,	10,	5,	240,	3),
				array(7920,		20000,	11,	5,	261,	3),
				array(9110,		25000,	12,	5,	283,	3),
				array(10480,	31000,	14,	5,	305,	3),
				array(12050,	39000,	15,	5,	328,	4),
				array(13860,	45000,	17,	6,	352,	4),
				array(15940,	52000,	19,	6,	377,	4),
				array(18330,	60000,	21,	6,	403,	4),
				array(21080,	69000,	24,	6,	429,	4),
				array(24240,	79000,	27,	6,	456,	4),
				array(27880,	91000,	30,	7,	484,	4),
				array(32060,	105000,	34,	7,	512,	4),
				array(36870,	121000,	38,	7,	541,	5),
				array(42400,	139000,	42,	7,	571,	5),
				array(48760,	160000,	47,	7,	602,	5),
				array(56070,	176000,	53,	8,	633,	5),
				array(64480,	194000,	59,	8,	665,	5),
				array(74150,	213000,	66,	8,	698,	5),
				array(85270,	234000,	74,	8,	731,	5),
				array(98060,	257000,	83,	8,	765,	6)
			),
			'maxLevel' => array(30, 30, 40, 40),
			'description' => 'Le <strong>Chantier Alpha</strong>, zone de construction et de stockage des vaisseaux, est votre premier chantier d’assemblage de chasseurs et corvettes. Ces vaisseaux sont les plus petits que vous pourrez construire durant le jeu, mais pas forcément les moins puissants. Chaque type d’appareil dispose de qualités comme de défauts, pensez à bien prendre en compte les aptitudes de chacun.<br /><br />Le nombre de vaisseaux en stock dans votre chantier est limité, tout comme votre file de construction. Seule l’augmentation du niveau de votre chantier vous donnera la possibilité de stocker et de construire d’avantage.<br /><br />Le niveau de votre chantier Alpha et votre avancée technologique vous permettront de <strong>débloquer et de découvrir les vaisseaux</strong>.'
		),
		array(
			'name' => 'dock2',
			'frenchName' => 'Chantier de Ligne',
			'imageLink' => 'dock2',
			'level' => array(
				// (time, resourcePrice, points, queues, storageSpace[en PEV], releasedShip)
				array(1000,		2000,	20,		1,	100,	1),
				array(1300,		2750,	22,		1,	125,	1),
				array(1690,		3781,	25,		1,	150,	1),
				array(2197,		5199,	28,		1,	180,	2),
				array(2856,		7149,	31,		1,	215,	2),
				array(3713,		9830,	34,		2,	250,	2),
				array(4827,		13516,	38,		2,	290,	2),
				array(6275,		18584,	42,		2,	335,	3),
				array(8157,		25554,	47,		2,	385,	3),
				array(10604,	35136,	52,		2,	440,	3),
				array(15907,	48312,	58,		3,	500,	3),
				array(23860,	66429,	64,		3,	565,	4),
				array(35790,	91340,	71,		3,	635,	4),
				array(53685,	125593,	80,		3,	710,	4),
				array(80528,	172690,	88,		3,	790,	4),
				array(120792,	237449,	98,		4,	875,	5),
				array(181188,	326492,	109,	4,	965,	5),
				array(271782,	448927,	122,	4,	1060,	5),
				array(407673,	617275,	135,	4,	1160,	5),
				array(611509,	848753,	150,	4,	1265,	6)
			),
			'maxLevel' => array(0, 10, 20, 20),
			'description' => 'Le <strong>Chantier de Ligne</strong> est le deuxième atelier de construction de vaisseaux à votre disposition. Plus grand et plus performant que son cadet le Chantier Alpha, il vous permettra de construire les navettes de type croiseur et destroyer. Ces vaisseaux, plus grands et plus lents que les corvettes et les chasseurs, servent à un autre type de stratégie. Comme pour les petits vaisseaux, les croiseurs et les destroyers disposent d’aptitude propre à certains types de combat, analysez correctement celles-ci pour peaufiner votre stratégie d’attaque ou de défense.<br /><br />Le nombre de vaisseaux que vous pouvez stocker dans votre Chantier de Ligne est limité comme votre fil de construction. Pensez à former des commandants pour vider vos hangars et renforcer vos escadrilles.',
			'techno' => 1
		),
		array(
			'name' => 'dock3',
			'frenchName' => 'Colonne d\'Assemblage',
			'imageLink' => 'dock3',
			'level' => array(
				// (time, resourcePrice, points, releasedShip)
				array(60000,	200000,	100,	1),
				array(78000,	240000,	120,	1),
				array(101000,	288000,	140,	1),
				array(131000,	346000,	170,	1),
				array(170000,	415000,	200,	2),
				array(221000,	498000,	240,	2),
				array(287000,	598000,	290,	2),
				array(373000,	718000,	350,	2),
				array(485000,	862000,	420,	2),
				array(631000,	1034000,500,	3)
			),
			'maxLevel' => array(0, 0, 0, 10),
			'description' => 'La <strong>Colonne d’Assemblage</strong> est le troisième atelier de construction d’appareils. Spécifique aux vaisseaux-mères, ce chantier spatial est indispensable à toute tentative de colonisation. Ce chantier titanesque conçu pour fabriquer des vaisseaux de taille quasi-planétaire, vous donnera la possibilité de construire trois types de vaisseaux-mères. Chacun de ces bâtiments spatiaux dispose de quasiment le même nombre d’aptitudes, excepté sa taille. En effet, lorsque vous créez un vaisseau mère de catégorie trois, il disposera de plus de place de construction que ses deux cadets.<br /><br />La Colonne d’Assemblage est la plus grosse plateforme que vous pouvez construire sur votre base. Elle est également la plus couteuse.',
			'techno' => 2
		),
		array(
			'name' => 'technosphere',
			'frenchName' => 'Technosphère',
			'imageLink' => 'technosphere',
			'level' => array(
				// (time, resourcePrice, points, queues)
				array(10,	100,	1,	2),
				array(14,	140,	1,	2),
				array(20,	200,	1,	2),
				array(28,	280,	1,	2),
				array(39,	390,	2,	2),
				array(55,	550,	2,	3),
				array(77,	770,	2,	3),
				array(110,	1080,	2,	3),
				array(150,	1510,	2,	3),
				array(210,	2110,	3,	3),
				array(290,	2950,	3,	3),
				array(410,	4130,	3,	3),
				array(570,	5780,	4,	3),
				array(800,	8090,	4,	3),
				array(1120,	11330,	5,	3),
				array(1570,	14200,	5,	4),
				array(2200,	17750,	6,	4),
				array(3080,	22000,	7,	4),
				array(4310,	28000,	8,	4),
				array(4960,	35000,	9,	4),
				array(5700,	44000,	10,	4),
				array(6560,	55000,	11,	4),
				array(7540,	69000,	12,	4),
				array(8670,	86000,	14,	4),
				array(9970,	108000,	15,	4),
				array(11470,124000,	17,	5),
				array(13190,143000,	19,	5),
				array(15170,164000,	21,	5),
				array(17450,189000,	24,	5),
				array(20070,217000,	27,	5),
				array(23080,250000,	30,	5),
				array(26540,288000,	34,	5),
				array(30520,331000,	38,	5),
				array(35100,381000,	42,	5),
				array(40370,438000,	47,	5),
				array(46430,482000,	53,	6),
				array(53390,530000,	59,	6),
				array(61400,583000,	66,	6),
				array(70610,641000,	74,	6),
				array(81200,705000,	83,	6)
			),
			'maxLevel' => array(30, 40, 40, 40),
			'description' => 'La <strong>Technosphère</strong>, véritable forge de votre base orbitale, vous permettra de donner des bonus à vos bâtiments, vaisseaux et autre.<br /><br />Cette bâtisse de forme arrondie obtiendra au fil du temps et en fonction du nombre de crédits investis dans votre université, un nombre de technologies à développer. Chaque technologie développée vous permettra d’une part de donner des <strong>bonus</strong> à certaines de vos constructions et d’autre part de débloquer vos vaisseaux et bâtiments.<br /><br />Comme dans le chantier Alpha, le Générateur, etc… une liste de développement est en place. Cette liste de développement est, bien évidemment, limitée.'
		),
		array(
			'name' => 'commercialPlateforme',
			'frenchName' => 'Plateforme Commerciale',
			'imageLink' => 'commercialplateforme',
			'level' => array(
				// (time, resourcePrice, points, nbCommercialShip)
				array(60,		2000,	10,		5),
				array(84,		2460,	22,		10),
				array(118,		3026,	34,		25),
				array(165,		3722,	46,		45),
				array(231,		4578,	58,		100),
				array(323,		5631,	70,		180),
				array(452,		6926,	82,		250),
				array(630,		8519,	94,		400),
				array(880,		10478,	106,	570),
				array(1230,		12888,	118,	800),
				array(1720,		15852,	130,	1200),
				array(2410,		19498,	142,	1500),
				array(3370,		23982,	154,	1800),
				array(4720,		29498,	166,	2100),
				array(6610,		36283,	178,	2300),
				array(9250,		44628,	190,	2450),
				array(12950,	54892,	202,	2550),
				array(18130,	67518,	214,	2700),
				array(25380,	83047,	226,	2850),
				array(29190,	102147,	238,	3000),
				array(33570,	125641,	250,	3100),
				array(38610,	154539,	262,	3200),
				array(44400,	190083,	274,	3300),
				array(51060,	233802,	286,	3400),
				array(58720,	287576,	298,	3450),
				array(67530,	353719,	310,	3500),
				array(77660,	435074,	322,	3550),
				array(89310,	535141,	334,	3600),
				array(102710,	658223,	346,	3650),
				array(118120,	809614,	358,	3700)
			),
			'maxLevel' => array(20, 30, 20, 30),
			'description' => 'La <strong>Plateforme Commerciale</strong> est véritablement la <strong>place de commerce</strong> entre les joueurs d’Asylamba. En effet, cette plateforme vous permettra de <strong>vendre</strong> ou d’<strong>acheter</strong> des vaisseaux, des commandants ou encore des ressources.<br /><br />Vous devrez fixer vous-même le prix des marchandises que vous souhaitez vendre, il faudra donc faire attention aux tendances du marché, de manière à être sûr de vendre vos produits. De plus, toute vente ou achat est soumis à deux taxes, une d\'achat et une de vente. Prenez donc ces taxes en compte en fixant vos prix. Le montant des taxes revient aux factions concernées.',
			'techno' => 0
		),
		array(
			'name' => 'storage',
			'frenchName' => 'Stockage',
			'imageLink' => 'storage',
			'level' => array(
				// (time, resourcePrice, points, storageSpace)
				array(9,		45,		1,	3700),
				array(13,		60,		1,	3800),
				array(18,		80,		1,	4000),
				array(25,		110,	1,	4400),
				array(35,		150,	2,	5200),
				array(49,		210,	2,	6200),
				array(69,		290,	2,	7400),
				array(100,		410,	2,	8900),
				array(140,		570,	2,	10700),
				array(200,		800,	3,	12800),
				array(280,		1120,	3,	15400),
				array(390,		1570,	3,	18500),
				array(550,		2200,	4,	22200),
				array(770,		3080,	4,	26600),
				array(1080,		4310,	5,	31900),
				array(1510,		5400,	5,	38300),
				array(2110,		6750,	6,	46000),
				array(2950,		8000,	7,	55200),
				array(4130,		10000,	8,	66200),
				array(4750,		13000,	9,	79400),
				array(5460,		16000,	10,	95300),
				array(6280,		20000,	11,	114400),
				array(7220,		25000,	12,	137300),
				array(8300,		31000,	14,	164800),
				array(9550,		39000,	15,	197800),
				array(10980,	45000,	17,	237400),
				array(12630,	52000,	19,	284900),
				array(14520,	60000,	21,	341900),
				array(16700,	69000,	24,	410300),
				array(19210,	79000,	27,	492400),
				array(22090,	91000,	30,	590900),
				array(25400,	105000,	34,	709100),
				array(29210,	121000,	38,	850900),
				array(33590,	139000,	42,	1021100),
				array(38630,	160000,	47,	1225300),
				array(44420,	176000,	53,	1715400),
				array(51080,	194000,	59,	2401600),
				array(58740,	213000,	66,	3362200),
				array(67550,	234000,	74,	4707100),
				array(77680,	257000,	83,	6589900)
			),
			'maxLevel' => array(30, 40, 40, 40),
			'description' => 'Comme son nom l’indique, le <strong>Stockage</strong> est le lieu où vous allez emmagasiner vos <strong>ressources</strong>. Il vous sera utile pour économiser des ressources dans le but de construire certains bâtiments ou vaisseaux.
			<br /><br />Notez qu\'il est possible de dépasser de 25\'000 le stockage maximal dans certains cas (comme la réception de ressources ou l\'annulation d\'une construction).' 
		),
		array(
			'name' => 'recycling',
			'frenchName' => 'Centre de Recyclage',
			'imageLink' => 'recycling',
			'level' => array(
				// (time, resourcePrice, points, nbRecyclers)
				array(55,		1600,	10,	1),
				array(77,		1968,	11,	2),
				array(108,		2421,	12,	4),
				array(151,		2977,	13,	7),
				array(211,		3662,	15,	11),
				array(295,		4504,	16,	16),
				array(413,		5541,	18,	22),
				array(580,		6815,	19,	29),
				array(810,		8382,	21,	37),
				array(1130,		10310,	24,	46),
				array(1580,		12682,	26,	56),
				array(2210,		15598,	29,	67),
				array(3090,		19186,	31,	79),
				array(4330,		23599,	35,	92),
				array(6060,		29026,	38,	106),
				array(8480,		35702,	42,	121),
				array(11870,	43914,	46,	137),
				array(16620,	54014,	51,	154),
				array(23270,	66437,	56,	172),
				array(26760,	81718,	61,	191),
				array(30770,	100513,	67,	211),
				array(35390,	123631,	74,	232),
				array(40700,	152066,	81,	254),
				array(46810,	187041,	90,	277),
				array(53830,	230061,	98,	301),
				array(61900,	282975,	108,326),
				array(71190,	348059,	119,352),
				array(81870,	428113,	131,379),
				array(94150,	526578,	144,407),
				array(108270,	647692,	159,436)
			),
			'maxLevel' => array(20, 20, 30, 30),
			'description' => 'Le <strong>Centre de Recyclage</strong> est un bâtiment très intéressant économiquement parlant. En effet, il vous permettra d\'avoir un revenu supplémentaire en recyclant des lieux autour de vous.<br /><br />Il est possible de lancer des missions de recyclage composées de plusieurs <strong>recycleurs</strong>, ou collecteurs, sur des lieux tels que des champs de ruines, des ceintures d\'astéroïdes, des géantes gazeuses ou des poches de gaz. Le but étant l\'extraction et le forage de matériau pour en retirer des ressources, des crédits et même des vaisseaux.<br /><br />Chaque type de lieu est riche en une ou plusieurs de ces denrées. Attention, les denrées sont épuisables.',
			'techno' => 3

			
		),
		array(
			'name' => 'spatioport',
			'frenchName' => 'Spatioport',
			'imageLink' => 'spatioport',
			'level' => array(
				// (time, resourcePrice, points, commercialRouteQuantity)
				array(100,		2500,	20,	1),
				array(140,		3250,	22,	1),
				array(196,		4225,	25,	1),
				array(274,		5493,	28,	2),
				array(384,		7140,	31,	2),
				array(538,		9282,	34,	2),
				array(753,		12067,	38,	3),
				array(1054,		15687,	42,	3),
				array(1476,		20393,	47,	3),
				array(2066,		26511,	52,	4),
				array(3099,		34465,	58,	5),
				array(4649,		44804,	64,	5),
				array(6973,		58245,	71,	6),
				array(10460,	75719,	80,	6),
				array(15689,	98434,	88,	7),
				array(23534,	127965,	98,	7),
				array(35301,	166354,	109,8),
				array(52952,	216260,	122,8),
				array(79428,	281139,	135,9),
				array(119142,	365480,	150,10),
			),
			'maxLevel' => array(0, 20, 10, 20),
			'description' => 'Le <strong>Spatio-Port</strong>, véritable plaque tournante du commerce dans votre domaine, permet, en fonction de sa taille, de créer et de gérer des <strong>routes commerciales</strong> sur le long terme avec vos partenaires. Pour valider une route commerciale vous devez la proposer et l’autre joueur doit l’accepter.<br /><br />Une route commerciale génère des revenus chez les deux parties. Plus la route est longue, plus le prix pour la mettre en place sera élevé. Le rendement d\'une route est également proportionnel à la distance, mais il est par contre plafonné aux environs de 100 années lumières. Il faudra donc plus de temps pour rentabiliser une très longue route.<br /> Les routes commerciales entre deux secteurs différents ainsi qu’avec des joueurs non-alliés ont tendance à générer plus de revenus.',
			'techno' => 4
		)
	);
}
?>