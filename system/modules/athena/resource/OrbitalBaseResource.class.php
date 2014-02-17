<?php
class OrbitalBaseResource {
	/**
	 * 0 - generator
	 * 1 - refinery
	 * 2 - dock1
	 * 3 - dock2
	 * 4 - dock3
	 * 5 - technosphere
	 * 6 - commercialPlateforme
	 * 7 - gravitationalModule
	 **/
	private static $orbitalBaseBuildings = array(0, 1, 2, 3, 4, 5, 6, 7);

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

	public static function getBuildingInfo($buildingNumber, $info, $level = 0, $sup = 'default') {
		if(self::isABuilding($buildingNumber)) {
			if ($info == 'name' OR $info == 'frenchName' OR $info == 'imageLink' OR $info == 'description') {
				return self::$building[$buildingNumber][$info];
			} elseif ($info == 'techno') {
				if (in_array($buildingNumber, array(3,4,6,7))) {
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
				} elseif($sup == 'pa') {
					return self::$building[$buildingNumber][$info][$level-1][2];
				} elseif($sup == 'points') {
					return self::$building[$buildingNumber][$info][$level-1][3];
				} else {
					if($sup == 'storageSpace' AND ($buildingNumber == 1 OR $buildingNumber == 2 OR $buildingNumber == 3)) {
						return self::$building[$buildingNumber][$info][$level-1][4];
					} elseif($sup == 'refiningCoefficient' AND $buildingNumber == 1) {
						return self::$building[$buildingNumber][$info][$level-1][5];
					} elseif($sup == 'releasedShip' AND ($buildingNumber == 2 OR $buildingNumber == 3)) {
						return self::$building[$buildingNumber][$info][$level-1][5];
					} elseif($sup == 'releasedShip' AND $buildingNumber == 4) {
						return self::$building[$buildingNumber][$info][$level-1][4];
					} elseif($sup == 'nbRoutesMax' AND $buildingNumber == 6) {
						return self::$building[$buildingNumber][$info][$level-1][4];
					} elseif($sup == 'nbCommercialShip' AND $buildingNumber == 6) {
						return self::$building[$buildingNumber][$info][$level-1][5];
					} elseif($sup == 'protectionRate' AND $buildingNumber == 7) {
						return self::$building[$buildingNumber][$info][$level-1][4];
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
				case 'resource' : return ($sup < self::getBuildingInfo($buildingId, 'level', $level, 'resourcePrice')) ? FALSE : TRUE;
					break;

				// assez de points d'action pour construire ?
				case 'pa' : return ($sup < self::getBuildingInfo($buildingId, 'level', $level, 'pa')) ? FALSE : TRUE;

				// encore de la place dans la queue ?
				// $sup est le nombre de batiments dans la queue
				case 'queue' :
					return ($sup < BQM_MAXQUEUE) ? TRUE : FALSE;
					break;
				// droit de construire le batiment ?
				// $sup est un objet de type OrbitalBase
				case 'buildingTree' :
					$diminution = NULL;
					switch ($buildingId) {
						case 0 : // Générateur
							$diminution = 0;
							break;
						case 1 : // Raffinerie
							$diminution = 0;
							break;
						case 2 : // Dock 1
							$diminution = 0;
							break;
						case 3 : // Dock 2
							$diminution = 5;
							break;
						case 4 : // Dock 3
							$diminution = 10;
							break;
						case 5 : // Technosphère
							$diminution = 0;
							break;
						case 6 : // Plateforme Commerciale
							$diminution = 5;
							break;
						case 7 : // Module Gravitationnel
							$diminution = 10;
							break;
						default :
							CTR::$alert->add('buildingId invalide (entre 0 et 7) dans haveRights de OrbitalBaseResource', ALT_BUG_ERROR);
							break;
					}
					if ($diminution !== NULL) {
						if ($buildingId == 0) {
							if ($level > self::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
								return 'niveau maximum atteint';
							} else {
								return TRUE;
							}
						} else {
							if ($level > self::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
								return 'niveau maximum atteint';
							} elseif ($level > ($sup->realGeneratorLevel - $diminution)) {
								return 'le niveau du générateur n\'es pas assez élevé';
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
			CTR::$alert->add('buildingId invalide (entre 0 et 7) dans haveRights de OrbitalBaseResource', ALT_BUG_ERROR);
			return FALSE;
		}
	}
	
	private static $building = array(
		array(
			'name' => 'generator',
			'frenchName' => 'Générateur',
			'imageLink' => 'generator',
			'level' => array(
				// (time, resourcePrice, pa, points)
				array(100,    100,    3, 	2),
				array(250,   200,    3, 	3),
				array(500,   400,    3, 	4),
				array(1500,   800,    3, 	5),
				array(5000,   1500,   3, 	7),
				array(8000,   3000,   4, 	9),
				array(10000,   5000,   4, 	11),
				array(16000,   8000,   4, 	14),
				array(22000,   20000,   5, 	17),
				array(29000,  50000,  5, 	20),
				array(36000,  90000,  6, 	24),
				array(42000, 120000,  7, 	28),
				array(49000,  180000,  8, 	34),
				array(56000,  250000,  9, 	40),
				array(62000, 320000,  10, 	48),
				array(72000, 380000, 12, 	56),
				array(85000, 490000, 14, 	66),
				array(100000, 580000, 14, 	76),
				array(120000, 680000, 15, 	88),
				array(150000, 800000, 15, 	100)
			),
			'maxLevel' => array(13, 20, 20, 20),
			'description' => 'Vous donnant la possibilité de construire et de faire évoluer vos bâtiments, le <strong>Générateur</strong> vous permet également de savoir à quel niveau sont vos édifices. Cette passerelle est l’institution principale de votre base orbitale. En effet, s’il n’est pas suffisamment évolué, vous remarquerez très vite qu’il devient impossible d’ériger d’autres types de bâtiment.<br /><br />Chaque fois que le Générateur aura passé <strong>cinq niveaux</strong>, une case construction supplémentaire sera à votre disposition. Cependant, dans certains cas, plusieurs bâtiments pourront être construits pour une seule case. Il vous faudra donc faire un choix entre lesdits bâtiments. Attention, la construction de certain type de bâtiment peuvent changer votre manière de jouer.'
		),
		array(
			'name' => 'refinery',
			'frenchName' => 'Raffinerie',
			'imageLink' => 'refinery',
			'level' => array(
				// (time, resourcePrice, pa, points, storageSpace, refiningCoefficient)
				array(72,    100,    	3, 	2, 		3000,     	5),
				array(144,   250,    	3, 	3, 		5000,     	19),
				array(360,   400,    	3, 	4, 		8000,   	33),
				array(1440,   600,    	3, 	5, 		13000,    	47),
				array(2880,   900,    	3, 	7, 		21000,    	61),
				array(4680,	 1150,   	4, 	9, 		34000,    	75),
				array(7200,  2000,   	4, 	11, 	54000,    	89),
				array(10080,  5000,   	4, 	14, 	86000,    	103),
				array(11880,  8000,   	5, 	17, 	138000,   	117),
				array(13680,  10500,   	5, 	20, 	221000,   	131),
				array(15480,  14000,   	6, 	24, 	354000,   	145),
				array(18000,  20000,   	7, 	28, 	566000,  	159),
				array(23400,  30000,  	8, 	34, 	906000,  	173),
				array(28080,  45000,  	9,	40, 	1300000,  	187),
				array(32400, 60000,  	10, 48, 	2080000,  	201), 
				array(39600, 80000,  	12, 56, 	2500000,  	215),
				array(43200, 130000, 	14, 66, 	2800000,  	229),
				array(50400, 180000, 	14, 76,  	3400000,  	243),
				array(57600, 260000, 	15, 88,  	4200000,  	257),
				array(64800, 350000, 	15, 100, 	5000000, 	271)
			),
			'maxLevel' => array(13, 20, 13, 20),
			'description' => 'La <strong>Raffinerie</strong> est le bâtiment où l’on traite vos ressources pour en extraire les fractions utilisables. Ce bâtiment est également le lieu de stockage des ressources amassées durant les relèves. La capacité de stockage ainsi que la capacité d’extraction de votre raffinerie dépend du niveau dans lequel elle se situe.<br /><br />Aucune action directe ne peut être effectuée dans la raffinerie, cependant vous pouvez y voir toutes les informations concernant votre production actuelle et pour tous les niveaux suivants.<br /><br /> Il y a deux modes d’utilisation dans votre raffinerie. Le mode Production améliore le nombre de ressources produites par relève et le mode Stockage augmente la quantité de ressources que vous pouvez stocker.'
		),
		array(
			'name' => 'dock1',
			'frenchName' => 'Chantier Alpha',
			'imageLink' => 'dock1',
			'level' => array(
				// (time, resourcePrice, pa, points, storageSpace[en PEV], releasedShip)
				// en rapport au storagespace beaucoup en début (30-40) puis gentillement vers la fin
				array(60,     100,  	3,  1,		30, 	1),
				array(150,    300,  	3,  2,		40, 	1),
				array(400,    700,  	3,  3,		50, 	1),
				array(800,    1600,  	3,  5,		60, 	1),
				array(1500,	  4200,  	3, 	7,		70, 	2),
				array(2300,   8000,  	4,  10,		80, 	2),
				array(4000,   15000,  	4, 	13,		90, 	3),
				array(5700,   23000,  	4, 	16,		100, 	3),
				array(6400,   40000, 	5, 	20,		110, 	3),
				array(9000,   62000, 	5, 	25,		120, 	4),
				array(12000,  85000, 	6, 	30,		128, 	4),
				array(15000,  130000, 	7, 	40,		136, 	4),
				array(21000,  170000, 	8,	50,		144, 	4),
				array(30000,  205000, 	9, 	60,		152, 	4),
				array(40000,  240000, 	10, 75,	 	159, 	5),
				array(51000,  270000,  	12, 90,	 	167, 	5),
				array(63000,  310000,	14, 105,	176, 	5),
				array(69000,  340000,	14, 125,	184, 	5),
				array(79000,  390000,  	15, 145,	192, 	5),
				array(90000,  430000,  	15,	170,	200, 	6)
			),
			'maxLevel' => array(13, 13, 20, 20),
			'description' => 'Le <strong>Chantier Alpha</strong>, zone de construction et de stockage des vaisseaux, est votre premier chantier d’assemblage de chasseurs et corvettes. Ces vaisseaux sont les plus petits que vous pourrez construire durant le jeu, mais pas forcément les moins puissants. Chaque type d’appareil dispose de qualités comme de défauts, pensez à bien prendre en compte les aptitudes de chacun.<br /><br />Le nombre de vaisseaus en stock dans votre chantier est limité, tout comme votre file de construction. Seule l’augmentation du niveau de votre chantier vous donnera la possibilité de stocker et de construire d’avantage.<br /><br />Le niveau de votre chantier Alpha et votre avancée technologique vous permettront de <strong>débloquer et de découvrir les vaisseaux</strong>.'
		),
		array(
			'name' => 'dock2',
			'frenchName' => 'Chantier de Ligne',
			'imageLink' => 'dock2',
			'level' => array(
				// (time, resourcePrice, pa, points, storageSpace[en PEV], releasedShip)
				// ressources PA (peu) se calquer sur les premiers plus chère que les batiment de 1er gene
				array(1000,  2000, 	    3, 	5, 		100, 1),
				array(3000,  4000,  	3, 	10, 	100, 1),
				array(7000,  6500, 		4, 	20, 	100, 1),
				array(10000,  9000, 	4, 	30, 	100, 2),
				array(15000,  12000, 	5, 	40, 	100, 2),
				array(20000,  25000, 	6, 	60, 	250, 2),
				array(30000, 50000, 	6, 	80, 	250, 3),
				array(40000, 80000, 	7, 	100,	250, 3),
				array(52000, 130000, 	8, 	140, 	250, 3),
				array(65000, 220000, 	10, 180, 	250, 4),
				array(75000, 300000, 	12, 220, 	500, 5),
				array(88000, 450000, 	14, 280, 	500, 5),
				array(100000, 600000, 	16, 380, 	500, 5),
				array(115000, 750000, 	18, 480, 	500, 5),
				array(125000, 1000000,	20, 600, 	500, 6)
			),
			'maxLevel' => array(8, 8, 15, 15),
			'description' => 'Le <strong>Chantier de Ligne</strong> est le deuxième atelier de construction de vaisseaus à votre disposition. Plus grand et plus performant que son cadet le Chantier Alpha, il vous permettra de construire les navettes de type croiseur et destroyer. Ces vaisseaux, plus grands et plus lents que les corvettes et les chasseurs, servent à un autre type de stratégie. Comme pour les petits vaisseaux, les croiseurs et les destroyers disposent d’aptitude propre à certains types de combat, analysez correctement celles-ci pour peaufiner votre stratégie d’attaque ou de défense.<br /><br />Le nombre de vaisseaus que vous pouvez stocker dans votre Chantier de Ligne est limité comme votre fil de construction. Pensez à former des commandants pour vider vos hangars et renforcer vos escadrilles.',
			'techno' => 1
		),
		array(
			'name' => 'dock3',
			'frenchName' => 'Colonne d\'Assemblage',
			'imageLink' => 'dock3',
			'level' => array(
				// (time, resourcePrice, pa, points, releasedShip)
				array(21000,  350000, 70,  100,  1),
				array(42000,  400000, 78,  200,  1),
				array(84000,  600000, 86,  300,  2),
				array(168000, 800000, 92,  600,  2),
				array(336000, 1000000,100, 1000, 3)
			),
			'maxLevel' => array(0, 5, 0, 5),
			'description' => 'La <strong>Colonne d’Assemblage</strong> est le troisième atelier de construction d’appareils. Spécifique aux vaisseaux-mères, ce chantier spatial est indispensable à toute tentative de colonisation. Ce chantier titanesque conçu pour fabriquer des vaisseaux de taille quasi-planétaire, vous donnera la possibilité de construire trois types de vaisseaux-mères. Chacun de ces bâtiments spatiaux dispose de quasiment le même nombre d’aptitudes, excepté sa taille. En effet, lorsque vous créez un vaisseau mère de catégorie trois, il disposera de plus de place de construction que ses deux cadets.<br /><br />La Colonne d’Assemblage est la plus grosse plateforme que vous pouvez construire sur votre base. Elle est également la plus couteuse.',
			'techno' => 2
		),
		// technosphere : NE PAS ENCORE UTILISER, PAS DEFINITIF !!!	
		// bonus : acclère temps de rechercher ?
		array(
			'name' => 'technosphere',
			'frenchName' => 'Technosphère',
			'imageLink' => 'technosphere',
			'level' => array(
				// (time, resourcePrice, pa, points, ?)
				// plus cher vers la fin vite
				array(200,    300, 		3, 	3),
				array(300,    400, 		3, 	4),
				array(480,    2100, 		3, 	5),
				array(1800,   4000, 		3, 	6),
				array(3600,   9000, 		3, 	8),
				array(4680,   14000,		4, 	10),
				array(6840,   20000, 	4, 	12),
				array(8640,   26000, 	4, 	16),
				array(10800,   32000, 	5, 	20),
				array(13320,   40000, 	5, 	24),
				array(17640,   51000, 	6, 	30),
				array(21600,  63000, 	7, 	36),
				array(27000,  74000, 	8, 	42),
				array(32040,  89000, 	9, 	50),
				array(37800,  112000, 	10, 58),
				array(43200,  126000, 	12, 68),
				array(48600,  150000, 	14, 78),
				array(54000,  180000, 	14, 90),
				array(61200,  250000, 	15, 102),
				array(68400,  300000, 	15, 115)
			),
			'maxLevel' => array(13, 20, 20, 20),
			'description' => 'La <strong>Technosphère</strong>, véritable forge de votre base orbitale, vous permettra de donner des bonus à vos bâtiments, vaisseaux et autre.<br /><br />Cette bâtisse de forme arrondie obtiendra au fil du temps et en fonction du nombre de crédits investis dans votre université, un nombre de technologies à développer. Chaque technologie développée vous permettra d’une part de donner des <strong>bonus</strong> a certaines de vos constructions et d’autre part de débloquer vos vaisseaux et bâtiments.<br /><br />Comme dans le chantier Alpha, le Générateur, etc… une liste de développement est en place. Cette liste de développement est, bien évidemment, limitée.'
		),
		array(
			'name' => 'commercialPlateforme',
			'frenchName' => 'Plateforme Commerciale',
			'imageLink' => 'commercialplateforme',
			'level' => array(
				// (time, resourcePrice, pa, points, nbRoutesMax, nbCommercialShip)
				// moin chère que le chantier
				array(1000,  2000,  	3, 	30,		1, 1),
				array(2000,  5000,  	3, 	40,		1, 2),
				array(4000,  8000,  	4, 	50,		1, 5),
				array(6000,  15000, 	4, 	60,		2, 10),
				array(8000,  30000,  	5, 	70,		2, 20),
				array(10000, 55000,  	6, 	80,		2, 50),
				array(12000, 80000,  	6, 	100,	3, 100),
				array(15000, 100000,  	7, 	120,	3, 200),
				array(18000, 220000, 	8, 	140,  	3, 500),
				array(25000, 400000,  	10, 160, 	4, 1000),
				array(36000, 550000,  	12, 200,  	5, 1500),
				array(50000, 750000, 	14, 240, 	6, 2000),
				array(70000, 950000, 	16, 280, 	7, 3000),
				array(90000, 1350000, 	18, 320, 	8, 4000),
				array(105000,1800000, 	20, 380, 	9, 5000)
			),
			'maxLevel' => array(8, 15, 8, 15),
			'description' => 'La <strong>Plateforme Commerciale</strong>, véritable plaque tournante du commerce dans votre domaine, permet, en fonction de sa taille, de créer et de gérer des <strong>routes commerciales</strong> sur le long terme avec vos partenaires. Pour valider une route commerciale vous devez la proposer et l’autre joueur doit l’accepter. <br /><br />Une route commerciale génère des revenus chez les deux parties. Plus la route est longue et plus les planètes sont peuplées, meilleurs sera son rendement. De plus, les routes commerciales entre deux secteurs différents ainsi qu\'avec des joueurs non-alliés ont tendance à générer plus de revenus.',
			'techno' => 0
		),
		array(
			'name' => 'gravitationalModule',
			'frenchName' => 'Module Gravitationnel',
			'imageLink' => 'gravitationalModule',
			'level' => array(
				// (time, resourcePrice, pa, points, protectionRate)
				// +-cher que le chantier 3
				array(21000,  400000,  	70, 	100,  5),
				array(42000,  500000,  	78, 	200,  10),
				array(84000,  720000, 	86, 	400,  15),
				array(168000, 940000, 	92, 	800,  18),
				array(336000, 1000000, 	100, 	1200, 20)
			),
			'maxLevel' => array(0, 0, 5, 5),
			'description' => 'Le <strong>Module Gravitationnel</strong>, seule structure défensive de la base orbitale, rend les attaques contre votre planète plus difficiles pour tous vos ennemis. Créant un champ gravitationnel de <strong>protection</strong> autour de votre planète et de votre base orbitale, cette défense rend tout type de chasseur quasiment inoffensif.<br /><br />Cette défense vous permettra d’attaquer vos ennemis en laissant à votre population une protection contre les petites attaques. Cependant, il est très conseillé de laisser systématiquement quelques vaisseaux de défense dans les places prévues à cet effet.',
			'techno' => 3
		)
	);
}
?>