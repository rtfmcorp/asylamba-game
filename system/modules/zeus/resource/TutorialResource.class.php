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

	/*--- OLD ON ---
	const GENERATOR_LEVEL_2 = 1;	# a modifier
	const REFINERY_LEVEL_3 = 2;		# a modifier
	const DOCK1_LEVEL_1 = 3;		# a modifier
	const TECHNOSPHERE_LEVEL_1 = 4; # a modifier
	const SHIP0_UNBLOCK = 5;		# a modifier
	const BUILD_SHIP0 = 6;			# a modifier
	const CREATE_COMMANDER = 7;		# a modifier
	const MODIFY_SCHOOL_INVEST = 8; # a killer
	--- OLD OFF ---*/

	const WELCOME = 1;
	const NAVIGATION = 2;
	const GENERATOR_LEVEL_2 = 3;
	const REFINERY_LEVEL_3 = 4;
	const STORAGE_LEVEL_3 = 5;
	const TECHNOSPHERE_LEVEL_1 = 6;
	const MODIFY_UNI_INVEST = 7;
	const CREATE_COMMANDER = 8;
	const DOCK1_LEVEL_1 = 9;
	const SHIP0_UNBLOCK = 10;
	const BUILD_SHIP0 = 11;
	const AFFECT_COMMANDER = 12;
	const FILL_SQUADRON = 13;
	const MOVE_FLEET_LINE = 14;
	const SPY_PLANET = 15;
	const LOOT_PLANET = 16;


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
			if (in_array($info, array('id', 'title', 'description', 'experienceReward', 'creditReward', 'resourceReward', 'shipReward'))) {
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
			'title' => 'Bienvenue',
			'description' => 'Salut à toi,
				<br />
				Bienvenue dans le monde d\'Asylamba! Je me présente : je m\'appelle Jean-Mi, je suis l\'opérateur du jeu et je vais te guider dans tes débuts. 
				<br />
				Nous voici dans ta page de profil. Elle résume l\'ensemble de tes activités actuelles.
				<br />
				Pour suivre le tutoriel, tu devras faire ce qui t\'es indiqué ici. D\'une fois que tu as accompli la tâche, la petite étoile sera colorée avec la couleur de ta faction. Tu peux cliquer dessus et valider l\'étape. Ensuite reviens ici et l\'étape suivante sera disponible.',
			'experienceReward' => 1,
			'creditReward' => 500,
			'resourceReward' => 200,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 2,
			'title' => 'Navigation et temporalité',
			'description' => 'Dans Asylamba, la navigation est horizontale. Pour faire glisser le panneau central, utilise les flèches directionnelles de ton clavier ou clique, à l\'aide de ta souris, sur les flèches qui s\'affichent aux deux extrémités de l\'écran.
				<br />
				La barre de navigation en haut te permet d\'accéder à ta planète (à tes différentes planètes quand tu en auras plusieurs). Tu pourras accéder aussi aux différentes pages de gestion de ton empire comme les finances, l\'université, la carte et l\'amirauté. 
				<br />
				Dernière précision, le temps sur Asylamba évolue de la même façon que dans la réalité. En revanche, les unités changent. Une relève correspond à une heure. Un segment équivaut à 24 heures, ou 24 relèves. Et pour finir une strate correspond à 30 segments. Pour vous faire une idée, une relève pour vous correspond à environ 2 semaines dans le monde d\'Asylamba.',
			'experienceReward' => 2,
			'creditReward' => 0,
			'resourceReward' => 300,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 3,
			'title' => 'Construire le Générateur au niveau 2',
			'description' => 'Le générateur est le bâtiment essentiel pour développer ta colonie. La construction des autres bâtiments dépend du niveau de ton générateur. Actuellement, il est au niveau 1. Suis les instructions ci-dessous pour lancer la construction de ton générateur niveau 2.
				<br />
				Va sur ta base orbitale en cliquant sur son nom en-haut à gauche de l\'écran. 
				<br />
				Tu te trouves à présent sur la vue de situation. 
				<br />
				A gauche de ton écran, se trouve une barre de navigation. Elle te permet de te déplacer dans les différents bâtiments. 
				<br />
				Clique sur l\'icône du générateur pour y accéder et sur "augmenter vers le niveau 2" sur le générateur. Celui-ci sera mis dans la file de construction et mettra un certain temps à se terminer. Plus le niveau augmente, plus le temps de construction et le prix augmentent.
				<br />
				En survolant chaque bâtiment avec ta souris, un petit "+" apparaît. Si tu cliques dessus, un tableau avec les prix et les temps de construction pour les différents niveaux apparaîtra.',
			'experienceReward' => 3,
			'creditReward' => 0,
			'resourceReward' => 400,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 4,
			'title' => 'Construire la Raffinerie au niveau 3',
			'description' => 'Rendez-vous à nouveau dans le générateur. Cette fois, vous devrez construire la Raffinerie au niveau 3. Si le bouton de construction est grisé, cela veut dire que vous n\'avez pas tous les prérequis pour exécuter la construction. 
				Il faut toujours que le niveau du générateur soit plus haut que le niveau des autres bâtiments, construisez donc un niveau supplémentaire du générateur.
				<br />
				La raffinerie sert à produire des ressources. Plus le niveau de la raffinerie est élevé, plus elle sera efficiente.
				Les ressources sont produites chaque relève. Une relève correspond à une heure.
				<br />
				Dans chaque bâtiment, il y a un panneau nommé "à propos". Si vous voulez en savoir plus, lisez ce panneau, des informations importantes et intéressantes peuvent s\'y trouver.',
			'experienceReward' => 3,
			'creditReward' => 0,
			'resourceReward' => 100,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 5,
			'title' => 'Construire le Stockage au niveau 2',
			'description' => 'Maintenant que ta Raffinerie tourne à plein régime, il faut que tu stockes tes ressources pour les utiliser plus tard. Construis le Stockage depuis le Générateur.
				<br />
				A chaque fois que tu construis un nouveau bâtiment, une nouvelle icône s\'ajoute dans la barre de navigation sur la gauche de l\'écran.',
			'experienceReward' => 3,
			'creditReward' => 0,
			'resourceReward' => 100,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 6,
			'title' => 'Construire la Technosphère',
			'description' => 'Pour pouvoir assurer l\'expansion de ton empire, il est essentiel de construire la Technosphère. C\'est grâce à elle que tu vas développer des technologies pour débloquer de nouveaux bâtiments, de nouveaux vaisseaux ainsi que pour améliorer divers aspects du jeu.
				<br />
				Rendez-vous dans le générateur pour construire la Technosphère.',
			'experienceReward' => 3,
			'creditReward' => 1000,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 7,
			'title' => 'Modifier l\'investissements de l\'Université',
			'description' => 'Dans Asylamba, vous devez gérer vos crédits. Pour cela vous avez un résumé de vos finances dans l\'onglet "finance" qui se trouve en-haut de votre écran (quatrième icône du deuxième groupe de menus).
				<br />
				Depuis là, vous pouvez voir si vous gagnez ou perdez des crédits à chaque relève. Vous pouvez être en négatif, mais sachez que ça va utiliser votre réserve de crédits. 
				<br />
				Dans la colonne "dépenses", modifiez vos investissements universitaires en cliquant sur le petit carré avec une flèche dirigée vers le bas puis en modifiant le montant.
				<br />
				Une fois cet investissement modifié, vous pouvez voir comment ce montant est utilisé par vos chercheurs en allant dans l\'Université. Pour y accéder, cliquez sur l\'onglet "université", il se trouve juste à droite de l\'onglet "finance". Vous pouvez changer les pourcentages de chaque faculté en fonction de vos besoins.',
			'experienceReward' => 3,
			'creditReward' => 2500,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 8,
			'title' => 'Former un officier',
			'description' => 'Sur chaque base orbitale, vous avez une école de commandement où vous pouvez former des commandants. 
				Pour y accéder, cliquez sur la dernière icône de la barre de navigation rapide (il faut d\'abord retourner sur votre base orbitale en cliquant sur son nom).
				<br />
				Dans cette école, vous pouvez créer de nouveaux commandants et les former au sein de l\'école. 
				Quand ils seront prêts, vous pourrez leur attribuer une flotte et les envoyer au combat.
				<br />
				Pour créer un commandant, il suffit de lui donner un nom (ou laisser le nom qu\'on a choisit pour vous) et de cliquer sur le bouton "créer l\'officier".
				2500 crédits vous seront débités, mais c\'est pour la bonne cause.',
			'experienceReward' => 5,
			'creditReward' => 100,
			'resourceReward' => 100,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 9,
			'title' => 'Construire le Chantier Alpha',
			'description' => 'Toujours dans le générateur, construisez le Chantier Alpha. Une fois que la construction sera achevée, vous aurez accès à ce bâtiment par la barre de navigation rapide.
				<br />
				Ce bâtiment vous permet de construire des vaisseaux de type "Chasseur" et de type "Corvette". 
				Par la suite, vous pourrez construire des vaisseaux plus grands dans le Chantier de Ligne.
				<br />
				Pour pouvoir construire un vaisseau, il faut des prérequis. Ces prérequis sont précisés dans le bouton de construction. 
				Dans le panneau d\'information de chaque vaisseau qui apparaît lors d\'un clic sur le "+" se trouvent les caractéristiques du vaisseau ainsi que divers autres informations.',
			'experienceReward' => 3,
			'creditReward' => 1000,
			'resourceReward' => 200,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 10,
			'title' => 'Développer la technologie "Chassis simple léger"',
			'description' => 'Nous voulons construire un Pégase, malheureusement il nous manque un prérequis : la technologie "Chassis simple léger". 
				Rends-toi donc dans la Technosphère pour développer cette technologie.
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
			'experienceReward' => 4,
			'creditReward' => 0,
			'resourceReward' => 3000,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 11,
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
			'experienceReward' => 8,
			'creditReward' => 2500,
			'resourceReward' => 0,
			'shipReward' => array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 12,
			'title' => 'Affecter un officier',
			'description' => 'Il faut à présent affecter un officier sur votre base orbitale pour qu\'il la protège. Pour ce faire, vous avez deux solutions. Soit aller sur la vue de situation de votre base orbitale, et cliquer sur "Affecter un officier" sur l\'une des deux lignes. Ceci vous dirigera vers l\'Ecole de commandement. Soit aller directement dans l\'Ecole de commandement.
				<br />
				Depuis là, cliquez simplement sur "affecter" sur l\'officier que vous voulez affecter. Vous serez ensuite redirigés vers l\'amirauté où vous pourrez gérer l\'officier et sa flotte.',
			'experienceReward' => 6,
			'creditReward' => 100,
			'resourceReward' => 0,
			'shipReward' => array(2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 13,
			'title' => 'Constituer une escadrille',
			'description' => 'Va dans l\'amirauté, si tu n\'y es pas déjà. Tu verras le commandant que tu as précédemment affecté. Pour le gérer, clique sur la petite flèche qui se trouve à droite de sa case. Des panneaux s\'ouvriront et toutes les informations du commandant s\'afficheront. Glisse vers la droite pour tout voir.
				<br />
				Un commandant de niveau 1, un Aspirant, peut contrôler une escadrille. Chaque niveau supplémentaire lui offre une escadrille en plus. Pour affecter des vaisseaux à une escadrille il faut cliquer sur celle-ci. Si l\'opération s\'est bien déroulée, l\'esacrille est entourée par un traitillé.
				<br />
				Ensuite dans la colonne à droite se trouve la composition de l\'esacrille et dans la colonne suivante se trouvent les vaisseaux qui sont dans le hangar. Pour affecter un vaisseau, il suffit de cliquer sur un vaisseau du hangar et il sera transféré dans l\'escadrille.
				<br />
				Chaque vaisseau vaut un nombre de PEV (points-équivalant-vaisseau), plus le vaisseau est grand, plus il a de PEV. Attention, une escadrille est constituée au maximum de 100 PEV, choisis donc bien la composition de ton escadrille.
				<br />
				NB: Si des vaisseaux ont été transférés dans l\'escadrille et que l\'étoile du tutoriel ne s\'allume pas, rafraîchissez votre page (F5).',
			'experienceReward' => 2,
			'creditReward' => 100,
			'resourceReward' => 100,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 14,
			'title' => 'Déplacer votre flotte en première ligne',
			'description' => 'Dans la vue de situation, deux lignes de défense sont disponibles autour d\'une planète. La première ligne (ligne extérieure) sert de défense lorsqu\'un ennemi attaque votre planète. La deuxième ligne sert de réserve, elle ne défendra pas lors d\'une attaque, par contre elle entrera en jeu lorsqu\'un ennemi tentera de prendre votre planète.
				<br />
				Lorsque vous avez affecté votre officier, il s\'est positionné en ligne 2. Pour mieux défendre votre planète, positionnez-le en ligne 1. Pour ce faire, allez dans la vue de situation (cliquez sur le nom de votre base en-haut de l\'écran), survolez votre flotte avec la souris. Une flèche apparaîtra, si vous cliquez dessus, votre flotte changera de ligne.',
			'experienceReward' => 2,
			'creditReward' => 40000,
			'resourceReward' => 0,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 15,
			'title' => 'Espionner une planète',
			'description' => 'Maintenant que vous avez une flotte à votre disposition, vous pouvez piller une planète rebelle. Mais avant cela, il est mieux de savoir quelle est la force de la flotte adverse. Pour le savoir, vous pouvez l\'espionner.
				<br />
				En premier lieu, allez sur la carte de la galaxie : troisième icône du menu en-haut de l\'écran. Cliquez sur un système solaire proche de votre planète, un panneau va s\'ouvrir en bas de l\'écran. Là vous voyez toutes les planètes de ce systèmes solaire. Choisissez ensuite une planète en cliquant dessus. 
				<br />
				Sur chaque planète, vous avez cinq actions possibles. La cinquième correspond à l\'espionnage, cliquez dessus (si l\'icône est grise, choisissez une autre planète). En payant une certaine somme en crédits, vous pourrez espionner la planète. Plus la somme est élevée, plus vous verrez d\'informations.
				<br />
				Une fois que vous avez cliqué, vous serez redirigés vers l\'amirauté, sur le rapport d\'espionnage. Si vous voyez des points d\'exclamation, cela signifie que vous avez pas payé assez cher pour voir cette information. Si vous avez payé assez cher, vous verrez la flotte qu\'il y a en défense ainsi que le nombre de PEV de la flotte.',
			'experienceReward' => 5,
			'creditReward' => 10000,
			'resourceReward' => 200,
			'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
		array(
			'id' => 16,
			'title' => 'Attaquer une planète rebelle',
			'description' => 'Pour faire votre premier pillage, il faut trouver une planète qui n\'est pas très défendue. Trouvez une planète qui a moins de PEV que votre flotte. Un conseil, trouvez une planète avec moins de 50 millions d\'habitants, elle aura une petite flotte défensive.
				<br />
				Une fois la planète idéale trouvée, il est temps de l\'attaquer. Vous pouvez arriver directement sur la planète en cliquant sur ses coordonées dans le rapport d\'espionnage ou en allant sur la carte de la galaxie. A l\'endroit où vous avez les cinq actions, la première sert à piller la planète. Sélectionnez votre flotte à gauche de l\'écran, vous verrez apparaître des informations comme le temps que l\'attaque va durer. Un bouton "Lancer l\'attaque" apparaît également, cliquez dessus.
				<br />
				Voilà, vous avez lancé votre première attaque. Dans l\'Amirauté, vous pouvez voir l\'avancement de votre flotte. D\'une fois qu\'elle sera arrivée, vous recevrez un rapport de combat. Si vous avez perdu, vous perdez votre flotte ainsi que votre officier. Si vous avez gagné, votre flotte rentrera sur votre base avec les ressources qu\'elle aura réussi à piller.',
			'experienceReward' => 10,
			'creditReward' => 0,
			'resourceReward' => 500,
			'shipReward' => array(5, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0))
	);
}
?>