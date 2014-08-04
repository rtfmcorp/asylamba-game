<?php

/**
 * TutorialResource
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 25.04.14
 */

class TutorialResource {

	const GENERATOR_LEVEL_2 = 1;
	const REFINERY_LEVEL_3 = 2;
	const REFINERY_MODE_PRODUCTION = 3;
	const DOCK1_LEVEL_1 = 4;
	const TECHNOSPHERE_LEVEL_1 = 5;
	const SHIP0_UNBLOCK = 6;
	const BUILD_SHIP0 = 7;
	const CREATE_COMMANDER = 8;
	const MODIFY_SCHOOL_INVEST = 9;

	public static function stepExists($step) {
		if ($step > 0 AND $step <= count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function isLastStep($step) {
		if ($step == count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function getInfo($id, $info) {
		if ($id <= count(self::$steps)) {
			if (in_array($info, array('id', 'title', 'description', 'experienceReward'))) {
				return self::$steps[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	private static $steps = array(
		array(
			'id' => 1,
			'title' => 'Construire le générateur au niveau 2',
			'description' => 'Pour construire le générateur, allez sur votre base orbitale en cliquant sur son nom en-haut à gauche de l\'écran. 
				Vous vous trouvez à présent sur la vue de situation. 
				A gauche de votre écran se trouve une barre de navigation, elle vous permet de vous déplacer dans les différents bâtiments. 
				Cliquez sur l\'icône du générateur pour y accéder.
				<br />
				Le générateur est le bâtiment qui permet de construire les autres bâtiments. Cliquez sur "augmenter vers le niveau 2" sur le générateur, celui-ci sera mis dans la file de construction et mettra un certain temps à se terminer.
				En survolant chaque bâtiment avec votre souris, un petit "+" apparaît. Si vous cliquez dessus, un tableau avec les prix et les temps de construction pour les différents niveaux apparaîtra.
				<br />
				Dans Asylamba, la navigation est horizontale, pour faire glisser le panneau central, utilisez les flèches directionnelles ou alors cliquez sur les flèches qui s\'affichent aux deux extrémités de l\'écran.',
			'experienceReward' => 3,
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 2,
			'title' => 'Construire la raffinerie au niveau 3',
			'description' => 'Rendez-vous à nouveau dans le générateur. Cette fois, vous devrez construire la Raffinerie au niveau 3. Si le bouton de construction est grisé, cela veut dire que vous n\'avez pas tous les prérequis pour exécuter la construction. 
				Il faut toujours que le niveau du générateur soit plus haut que le niveau des autres bâtiments, construisez donc un niveau supplémentaire du générateur.
				<br />
				La raffinerie sert à produire des ressources et à les stocker. Plus le niveau de la raffinerie est élevé, plus elle sera efficiente.
				Les ressources sont produites chaque relève. Une relève correspond à une heure.
				<br />
				Dans chaque bâtiment, il y a un panneau nommé "à propos". Si vous voulez en savoir plus, lisez ce panneau, des informations importantes et intéressantes peuvent s\'y trouver.',
			'experienceReward' => 10
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 3,
			'title' => 'Mettre la raffinerie en mode production',
			'description' => 'Allez dans la raffinerie : sur votre base orbitale, puis cliquez sur l\'icône de la Raffinerie qui se trouve en troisième position de la barre de navigation rapide à gauche de l\'écran.
				Dans ce bâtiment, vous pouvez voir à tout instant combien vous produisez et ou en est votre stock.
				La Raffinerie possède 2 modes : le mode "Stockage" et le mode "Production". Le premier augmente la capacité de stockage et le second augmente la production horaire. 
				Essayer donc de passer en mode "Production" en cliquant sur le bouton prévu à cet effet.',
			'experienceReward' => 15
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 4,
			'title' => 'Construire le Chantier Alpha',
			'description' => 'Toujours dans le générateur, construisez le Chantier Alpha. Une fois que la construction sera achevée, vous aurez accès à ce bâtiment par la barre de navigation rapide.
				<br />
				Ce bâtiment vous permet de construire des vaisseaux de type "Chasseur" et de type "Corvette". 
				Par la suite, vous pourrez construire des vaisseaux plus grands dans le Chantier de Ligne.
				<br />
				Pour pouvoir construire un vaisseau, il faut des prérequis. 
				Ces prérequis sont précisés dans le bouton de construction. 
				Dans le panneau d\'information de chaque vaisseau qui apparaît lors d\'un clic sur le "+" se trouvent les caractéristiques du vaisseau ainsi que divers autres informations.',
			'experienceReward' => 20
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 5,
			'title' => 'Construire la Technosphère',
			'description' => 'La technosphère est un bâtiment qui permet de développer des technologies qui nous permettrons d\'améliorer certains aspects de votre empire et surtout de débloquer des bâtiments ou des vaisseaux.
				<br />
				Rendez-vous dans le générateur pour construire la Technosphère.',
			'experienceReward' => 22
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 6,
			'title' => 'Développer la technologie "Chassis simple léger"',
			'description' => 'Nous voulons construire un Pégase, malheureusement il nous manque un prérequis : la technologie "Chassis simple léger". 
				Rendez-vous donc dans la Technosphère pour développer cette technologie.
				<br />
				Dans la Technosphère, plusieurs types de technologie sont disponibles. 
				Les "Chassis" permettent de débloquer de nouveaux vaisseaux. 
				Les "Nouvelles technologies" permettent de débloquer de nouveaux bâtiments ou de nouvelles actions indispensables comme la "Colonisation" par exemple.
				Les "Améliorations industrielles" permettent de rendre plus efficace divers points du jeu, par exemple augmentation de la production de ressources, augmentation de la vitesse de construction de certaines vaisseaux, augmentation de la force de frappe d\'un vaisseau, etc.
				Les deux premières catégories sont des technologies qu\'on débloque une seule fois, par contre les améliorations industrielles se font par niveau.
				<br />
				Encore une fois, chaque technologie a besoin de prérequis qui sont précisés dans le panneau "+" sur chaque technologie. 
				Ces prérequis comprennent à chaque fois un niveau de la Technosphère et des "Recherches".
				Vos niveaux actuels de recherche sont visibles en cliquant sur l\'onglet "technologie" qui se trouve parmi les icônes tout au sommet de votre écran.
				<br />
				Vous avez tous les prérequis pour développer "Chassis simple léger", faites-le donc.',
			'experienceReward' => 25
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 7,
			'title' => 'Construire un Chasseur Léger',
			'description' => 'Vous pouvez maintenant aller dans le Chantier Alpha et construire un Pégase. Il s\'agit du plus petit vaisseau disponible.
				<br />
				En cliquant sur le "+", vous verrez toutes les informations sur ce vaisseau. 
				Les caractéristiques du vaisseau durant un combat, mais également le temps de construction unitaire, le coût, le nombre de PEV et la soute.
				La soute est le nombre de ressources que ce vaisseau peut ramener d\'un combat. 
				Les PEV sont les Points-Equivalent-Vaisseau, ce qui correspond à la place que va prendre le vaisseau dans une escadrille sachant qu\'une escadrille a un nombre de place limité.
				<br />
				Si vous avez assez de ressources, vous pouvez lancer la construction de plusieurs vaisseaux de même type à la fois. 
				Cela vous facilitera la vie et vous permet de mettre beaucoup de vaisseaux en construction. 
				Attention, la file de construction est limitée en place.',
			'experienceReward' => 28
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 8,
			'title' => 'Commencer à former un commandant',
			'description' => 'Sur chaque base orbitale, vous avez une école de commandement où vous pouvez former des commandants. 
				Pour y accéder, cliquez sur la dernière icône de la barre de navigation rapide.
				<br />
				Dans cette école, vous pouvez créer de nouveaux commandants et les former au sein de l\'école. 
				Quand ils seront prêts, vous pourrez leur attribuer une flotte et les envoyer au combat.
				<br />
				Pour créer un commandant, il suffit de lui donner un nom (ou laisser le nom qu\'on a choisit pour vous) et de cliquer sur le bouton "créer l\'officier".
				200 crédits vous seront débités, mais c\'est pour la bonne cause.',
			'experienceReward' => 32
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 9,
			'title' => 'Modifier l\'investissement dans l\'école de commandement',
			'description' => 'Allez dans l\'école de commandement à nouveau. Vous voyez une case indiquant l\'investissement alloué à l\'école. 
				Ce montant vous est débité chaque relève (chaque heure), il sert à financer l\'école qui forme vos commandants.
				Plus vous investissez, et plus vos commandants gagnent de l\'expérience rapidement.
				<br />
				Nous voulons à présent modifier cet investissement horaire. 
				Pour ce faire, cliquez sur l\'icône "finance" qui se trouve parmi les icônes au sommet de votre écran.
				Ce menu est l\'onglet officiel pour gérer tout ce qui touche aux crédits. Vous avez les recettes et les dépenses pour chaque heure.
				Si vous êtes en perte faites attention, vous allez épuiser votre réserve de crédits assez rapidement.
				<br />
				Déplacez-vous vers la droite pour voir tous les panneaux. Un d\'entre eux s\'appelle "Investissements". 
				En cliquant sur la patite flèche qui pointe vers le bas, vous verrez les investissements votre planète.
				En cliquant sur la flèche à droite du montant, vous pourrez modifier celui-ci.
				<br />
				Changez le montant investi dans l\'école de commandement pour passer à l\'étape suivante.',
			'experienceReward' => 37
			'creditReward' => 0,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0))
	);
}
?>