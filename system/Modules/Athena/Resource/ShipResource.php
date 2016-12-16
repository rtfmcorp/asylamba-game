<?php

namespace Asylamba\Modules\Athena\Resource;

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Promethee\Resource\TechnologyResource;

class ShipResource {

	const PEGASE = 0;
	const SATYRE = 1;
	const CHIMERE = 2;
	const SIRENE = 3;
	const DRYADE = 4;
	const MEDUSE = 5;
	const GRIFFON = 6;
	const CYCLOPE = 7;
	const MINOTAURE = 8;
	const HYDRE = 9;
	const CERBERE = 10;
	const PHENIX = 11;

	const SHIP_QUANTITY = 12;

	const COST_REDUCTION = 0.8;

	private static $ships = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14);

	/**
	 * pegase = 0, satyre = 1, sirene = 2, dryade = 3, chimere = 4 and meduse = 5
	 **/
	private static $dock1Ships = array(0, 1, 2, 3, 4, 5);

	/**
	 * griffon = 6, cyclope = 7, minotaure = 8, hydre = 9, cerbere = 10, phenix = 11
	 **/
	private static $dock2Ships = array(6, 7, 8, 9, 10, 11);

	private static $femaleShipNames = array(2, 3, 4, 5, 9);

	public static function isAShip($ship) {
		return in_array($ship, self::$ships);
	}

	public static function isAShipFromDock1($ship) {
		return in_array($ship, self::$dock1Ships);
	}

	public static function isAShipFromDock2($ship) {
		return in_array($ship, self::$dock2Ships);
	}

	public static function isAFemaleShipName($ship) {
		return in_array($ship, self::$femaleShipNames);
	}

	public static function getInfo($shipNumber, $info) {
		if(self::isAShip($shipNumber)) {
			if(in_array($info, array('codeName', 'name', 'class', 'pev', 'life', 'speed', 'defense', 'attack', 'cost', 'time', 'resourcePrice', 'points', 'imageLink', 'techno', 'description'))) {
				return self::$ship[$shipNumber][$info];
			} else {
				throw new ErrorException('info inconnue dans getInfo de ShipResource');
			}
		} else {
			throw new ErrorException('shipId inconnu (entre 0 et 14) dans getInfo de ShipResource');
		}
	}

	private static $ship = array(
		array(
			'codeName' => 'Pégase',
			'name' => 'Chasseur Léger',
			'class' => 0,
			'pev' => 2,
			'life' => 26,
			'speed' => 195,
			'defense' => 1,
			'attack' => array(5),
			'cost' => 7,
			'time' => 500,
			'resourcePrice' => 2800,
			'points' => 3,
			'imageLink' => 'ship0',
			'techno' => 5,
			'description' => 'Un Pégase (aussi appelé chasseur léger) est un petit vaisseau de combat très rapide et très efficace contre les frégates ioniques - Cyclope. Équipé de deux canons mitrailleurs placés sur les côtés de l’appareil, ce vaisseau monoplace est composé d’un alliage carbonique. <br /><br />Utilisé en grand nombre, cette petite navette peut être décisive dans le déroulement d’une bataille. Cette technique de combat fut mise en place par le commandant Hagar Mosine lors de la célèbre mission K-7, sur Nessor.<br /><br />Avec des coûts très faibles et une rapidité de production élevée, le Pégase est l’appareil le plus populaire de la galaxie de l’Œil.'
		),
		array(
			'codeName' => 'Satyre',
			'name' => 'Chasseur lourd',
			'class' => 0,
			'pev' => 3,
			'life' => 30,
			'speed' => 185,
			'defense' => 5,
			'attack' => array(7),
			'cost' => 5,
			'time' => 570,
			'resourcePrice' => 3500, 
			'points' => 4,
			'imageLink' => 'ship1',
			'techno' => 6,
			'description' => 'Grace à sa polyvalence issue d’une construction modulaire, le Satyre est très utile comme vaisseau d’appui. En effet, ce chasseur lourd, mariant vitesse et maniabilité, procure une défense idéale contre les frégates lors de gros combats. Cependant, il est fortement vulnérable contre la puissance de feu des destroyers, nettement plus précise que celle des frégates.<br /><br />Son fuselage est conçu en deux tronçons d’alliage carbonique. Le premier, allant de l’avant de l’appareil jusqu’à l’arrière du cockpit, est renforcé de manière à protéger au mieux le pilote. La deuxième, plus petite et moins épaisse, formant l’arrière de la navette, sert simplement de réservoir de carburant et de munition.'
		),
		array(
			'codeName' => 'Chimère',
			'name' => 'Chasseur multi-tourelle',
			'class' => 0,
			'pev' => 3,
			'life' => 26,
			'speed' => 195,
			'defense' => 3,
			'attack' => array(6, 6),
			'cost' => 25,
			'time' => 850,
			'resourcePrice' => 4420, 
			'points' => 6,
			'imageLink' => 'ship4',
			'techno' => 7,
			'description' => 'La Chimère est un vaisseau rapide disposant de trois types d’attaques. La première est un canon mitrailleur double disposé de chaque côté de l’appareil. La deuxième est un canon laser léger placé sur le sommet du cockpit. Le dernier est une pièce lance missile à tête chercheuse fixée sous la navette. Cet armement, pour le moins impressionnant, a été conçu pour lutter contre les vaisseaux de type frégate. Malheureusement, sa maniabilité très précaire n’en fait pas un appareil très efficace contre les vaisseaux du chantier de ligne.'
		),
		array(
			'codeName' => 'Sirène',
			'name' => 'Corvette légère',
			'class' => 1,
			'pev' => 5,
			'life' => 65,
			'speed' => 190,
			'defense' => 6,
			'attack' => array(20, 2, 2),
			'cost' => 100,
			'time' => 480,
			'resourcePrice' => 4000, 
			'points' => 6,
			'imageLink' => 'ship2',
			'techno' => 8,
			'description' => 'Disposant d’un canon lance missile, la Sirène est très utile pour les attaques rapides. Sa coque de protection avant, composée d’un alliage carbonique, lui offre une forte résistance contre toutes les armes de type canon mitrailleur. En revanche, cet appareil est très vulnérable contre les attaques de type missile.<br /><br />Cette corvette légère, composé d’un superstrato-réacteur disposé au-dessous de l’appareil, est la plus économique en termes de carburant.'
		),
		array(
			'codeName' => 'Dryade',
			'name' => 'Corvette lourde',
			'class' => 1,
			'pev' => 7,
			'life' => 90,
			'speed' => 165,
			'defense' => 23,
			'attack' => array(30),
			'cost' => 58,
			'time' => 1880,
			'resourcePrice' => 8350,
			'points' => 8,
			'imageLink' => 'ship3',
			'techno' => 9,
			'description' => 'La Dryade est un vaisseau de type corvette lourde, armé d’un canon lance missile. Cette appareil puissant et résistant, est particulièrement efficace contre les petits vaisseaux. Composé d’un équipage de trois personnes, la Dryade est également capable de transporter un commando pour toutes missions spéciales ou tentative de colonisations. <br /><br />Cette navette de débarquement fut utilisée par les forces rebelles Kovhakarh durant la bataille pour la prise de la planète Guaména.'
		),
		array(
			'codeName' => 'Méduse',
			'name' => 'Corvette multi-tourelle',
			'class' => 1,
			'pev' => 9,
			'life' => 75,
			'speed' => 145,
			'defense' => 8,
			'attack' => array(20, 12, 12, 12, 12, 7, 7, 7),
			'cost' => 80,
			'time' => 2500,
			'resourcePrice' => 15300, 
			'points' => 15,
			'imageLink' => 'ship5',
			'techno' => 10,
			'description' => 'Disposant de deux canons lance missile, d’un canon laser et d’un canon mitrailleur arrière, la Méduse est l’un des seuls vaisseaux légers à disposer d’autant d’armement. Conçu pour lutter contre les attaques massives de chasseurs légers, la Méduse est également redoutable contre les frégates. <br /><br />La coque protectrice avant de la Méduse est particulièrement résistante, mais cela n’en fait pas un vaisseau invulnérable. Un certain nombre de faiblesses sont facilement distinctes à l’arrière de l’appareil.<br /><br />Cette navette fut créée à la fin de la grande période de trouble, elle ne fut donc jamais utilisée à grande échelle dans une guerre.'
		),
		array(
			'codeName' => 'Griffon',
			'name' => 'Frégate d\'attaque',
			'class' => 2,
			'pev' => 23,
			'life' => 250,
			'speed' => 100,
			'defense' => 40,
			'attack' => array(15, 15, 15, 15),
			'cost' => 68,
			'time' => 3600,
			'resourcePrice' => 40000,
			'points' => 30,
			'imageLink' => 'ship6',
			'techno' => 11,
			'description' => 'La frégate d’attaque Griffon est un vaisseau de guerre de taille moyenne, capable d’assurer plusieurs types de mission : protection de vaisseau mère, surveillance spatiale et défense planétaire. Plus grand et plus puissant que les appareils fabriqués dans le Chantier Alpha, le Griffon dispose de quatre canons laser disposés à chaque extrémité du cockpit.'
		),
		array(
			'codeName' => 'Cyclope',
			'name' => 'Frégate ionique',
			'class' => 2,
			'pev' => 45,
			'life' => 320,
			'speed' => 90,
			'defense' => 40,
			'attack' => array(225),
			'cost' => 500,
			'time' => 7200,
			'resourcePrice' => 80000, 
			'points' => 80,
			'imageLink' => 'ship7',
			'techno' => 12,
			'description' => 'Disposant de quatre canons ioniques convergeant en un seul faisceau dans le but d’amplifier la puissance du tir, le Cyclope est le vaisseau, toute catégorie confondue, le plus dévastateur de la galaxie de l’Œil. <br /><br />Cette frégate légère et rapide fut longtemps utilisée par l’ancien Empire pour imposer et asseoir son pouvoir sur les planètes contrebandières. Elle fut également sujet à controverse pour la population impériale de par sa puissance de feu et le nombre de victimes civiles lors de la plupart de ces nombreuses attaques.'
		),
		array(
			'codeName' => 'Minotaure',
			'name' => 'Destroyer',
			'class' => 3,
			'pev' => 75,
			'life' => 1200,
			'speed' => 88,
			'defense' => 120,
			'attack' => array(35, 35, 35, 35, 25, 10, 10),
			'cost' => 250,
			'time' => 11400,
			'resourcePrice' => 112000, 
			'points' => 70,
			'imageLink' => 'ship8',
			'techno' => 13,
			'description' => 'Le Minotaure, un destroyer de première ligne, est composé d’une coque avant en alliage carbonique capable de résister à la plupart des attaques frontales. Son armement comprend un canon laser de 30 tonnes sur le sommet et de trois tubes lance missile, deux latéraux et un en dessous. Capable de se déplacer à une grande vitesse et disposant d’une coque de protection épaisse, le Minotaure est le vaisseau qui convient le mieux aux attaques de colonisation ou à tous types de défense. <br /><br />Fleuron de la flotte des Kovahkarh lors des périodes de trouble, il fut malheureusement très vite dépassé durant l’essor du nouvel Empire par le Cerbère.'
		),
		array(
			'codeName' => 'Hydre',
			'name' => 'Destroyer missile',
			'class' => 3,
			'pev' => 86,
			'life' => 1050,
			'speed' => 75,
			'defense' => 100,
			'attack' => array(21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21),
			'cost' => 600,
			'time' => 17400,
			'resourcePrice' => 150000, 
			'points' => 80,
			'imageLink' => 'ship9',
			'techno' => 14,
			'description' => 'Plus petit que le Minotaure et moins rapide, l’Hydre fut créée dans le but de défendre les populations prudes Cardanienne contre les frappes massives de Pégase Kovahk. En effet, ses vingt canons lance missile disposés sur sa face avant lui donnent un avantage certain dans les attaques compactes de chasseurs. Doté d’une excellente manœuvrabilité pour ce type de vaisseau, l’Hydre est sans doute le destroyer le mieux conçu de l’histoire de la galaxie de l’Œil.'
		),
		array(
			'codeName' => 'Cerbère',
			'name' => 'Croiseur',
			'class' => 3,
			'pev' => 82,
			'life' => 1220,
			'speed' => 80,
			'defense' => 135,
			'attack' => array(175, 50, 50, 25, 25, 25, 25, 6, 6, 6),
			'cost' => 700,
			'time' => 15960,
			'resourcePrice' => 208000, 
			'points' => 120,
			'imageLink' => 'ship10',
			'techno' => 15,
			'description' => 'Le Cerbère est un grand vaisseau de guerre doté d’un épais blindage, dont l’artillerie principale est composée de deux pièces d’artilleries des plus gros calibres existants. Conçu pour les destructions de masse, les deux principaux canons du Cerbère sont accompagnés de cinq canons lasers longue portée. Ces défenses gargantuesques ont été mises en place dans un premier temps pour assurer une défense planétaire. Cependant, les gouvernements remarquèrent vite que l’utilisation du Cerbère pour les attaques massives était loin d’être inutile.'
		),
		array(
			'codeName' => 'Phénix',
			'name' => 'Croiseur lourd',
			'class' => 3,
			'pev' => 84,
			'life' => 1350,
			'speed' => 75,
			'defense' => 150,
			'attack' => array(200, 50, 50, 50, 50, 25, 25, 25, 25, 6, 6, 6),
			'cost' => 950,
			'time' => 18000,
			'resourcePrice' => 300000, 
			'points' => 200,
			'imageLink' => 'ship11',
			'techno' => 16,
			'description' => 'Le Phénix est le type prédominant de croiseur de la galaxie de l’Œil. Conçu par le vieil Empire pour mettre un terme à la rébellion des périodes de troubles, il est le vaisseau de combat le plus massif du chantier de ligne. En effet, sa construction nécessite énormément de matériel et de main-d’œuvre. De plus, le Phénix est piloté par un équipage de plus de 300 personnes. <br /><br />Cet appareil, très gros, est difficile à manœuvrer dans les combats de mêlée. Cependant il est d’une efficacité redoutable en dernière ligne de vos escadrilles.'
		),
	);
}