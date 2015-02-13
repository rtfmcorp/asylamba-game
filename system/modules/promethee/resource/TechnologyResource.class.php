<?php
class TechnologyResource {
	# JACKY si tu peux regarder si tout est ok dans les lignes au-dessous !!!
	private static $technologies = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 
		19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46);
	private static $technologiesForUnblocking = array(0, 1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
#	private static $technologiesForUnblocking = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
	private static $technologiesNotDisplayed = array(2, 26, 27);
	
	public static function isATechnology($techno) {
		return (in_array($techno, self::$technologies)) ? TRUE : FALSE;
	}

	public static function isAnUnblockingTechnology($techno) {
		return (in_array($techno, self::$technologiesForUnblocking)) ? TRUE : FALSE;
	}

	public static function isATechnologyNotDisplayed($techno) {
		return (in_array($techno, self::$technologiesNotDisplayed)) ? TRUE : FALSE;
	}

	public static function getInfo($techno, $info, $level = 0) {
		if (self::isATechnology($techno)) {
			if (self::isAnUnblockingTechnology($techno)) {
				if(in_array($info, array('name', 'progName', 'imageLink', 'requiredTechnosphere', 'requiredResearch', 'time', 'resource', 'credit', 'points', 'column', 'shortDescription', 'description'))) {
					return self::$technology[$techno][$info];
				} else {
					CTR::$alert->add('2e argument faux pour getInfo() de TechnologyResource (techno ' . $techno . ')', ALERT_BUG_ERROR);
				}
			} else {
				if(in_array($info, array('name', 'progName', 'imageLink', 'requiredTechnosphere', 'requiredResearch', 'maxLevel', 'category', 'column', 'shortDescription', 'description', 'bonus'))) {
					return self::$technology[$techno][$info];
				} elseif (in_array($info, array('time', 'resource', 'credit', 'points'))) {
					if ($level <= 0) {
						return FALSE;
					}
					if ($info == 'points') {
						return round(self::$technology[$techno][$info] * $level * Technology::COEF_POINTS);
					} elseif ($info == 'time') {
						return round(self::$technology[$techno][$info] * $level * Technology::COEF_TIME);
					} else {

						switch (self::$technology[$techno]['category']) {
							case 1:
								$value = round(self::$technology[$techno][$info] * pow(1.75, $level-1));
								break;
							case 2:
								$value = round(self::$technology[$techno][$info] * pow(1.5, $level-1));
								break;
							case 3:
								$value = round(self::$technology[$techno][$info] * pow(1.3, $level-1));
								break;
							default:
								return FALSE;
						}
						return $value;
					}
					/*
					$value = self::$technology[$techno][$info] * $level;
					//$value = self::$technology[$techno][$info][0] * $level;
					switch ($info) {
						case 'time' : 		$value *= TQM_TECHNOCOEFTIME; 		break;
						case 'resource' : 	$value *= TQM_TECHNOCOEFRESOURCE; 	break;
						case 'credit' : 	$value *= TQM_TECHNOCOEFCREDIT; 	break;
						case 'points' : 	$value *= Technology::COEF_POINTS; 	break;
						default : return FALSE;
					}
					//$value += self::$technology[$techno][$info][1];
					return $value;*/
				} else {
					CTR::$alert->add('2e argument faux pour getInof() de TechnologyResource', ALERT_BUG_ERROR);
				}
			}
		} else {
			CTR::$alert->add('Technologie inexistante dans getInfo() de TechnologyResource', ALERT_BUG_ERROR);
		}
		return FALSE;
	}

	public static function haveRights($techno, $type, $arg1 = 0, $arg2 = 'default') {
		if (self::isATechnology($techno)) {
			switch($type) {
				// assez de ressources pour contruire ?
				// $arg1 est le niveau
				// $arg2 est ce que le joueur possède (ressource ou crédit)
				case 'resource' : return ($arg2 >= self::getInfo($techno, 'resource', $arg1)) ? TRUE : FALSE;
					break;
				// assez de crédits pour construire ?
				case 'credit' : return ($arg2 >= self::getInfo($techno, 'credit', $arg1)) ? TRUE : FALSE;
					break;
				// encore de la place dans la queue ?
				// $arg1 est un objet de type OrbitalBase
				// $arg2 est le nombre de technologies dans la queue
				case 'queue' : 
					$maxQueue = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $arg1->levelTechnosphere, 'nbQueues');
					return ($arg2 < $maxQueue) ? TRUE : FALSE;
					break;
				// a-t-on le droit de construire ce niveau ?
				// $arg1 est le niveau cible
				case 'levelPermit' :
				 	if (self::isAnUnblockingTechnology($techno)) {
				 		return ($arg1 == 1) ? TRUE : FALSE;
				 	} else {
				 		//limitation de niveau ?
				 		if ($arg1 > 0) {
				 			return TRUE;
				 		} else {
				 			return FALSE;
				 		}
				 	}
				// est-ce que le niveau de la technosphère est assez élevé ?
				// arg1 est le niveau de la technosphere
				case 'technosphereLevel' :
					return (self::getInfo($techno, 'requiredTechnosphere') <= $arg1) ? TRUE : FALSE;
					break;
				// est-ce que les recherches de l'université sont acquises ?
				// arg1 est le niveau de la technologie
				// arg2 est une stacklist avec les niveaux de recherche
				case 'research' :
					$neededResearch = self::getInfo($techno, 'requiredResearch');
					$researchList = new StackList();
					for ($i = 0; $i < RSM_RESEARCHQUANTITY; $i++) {
						if ($neededResearch[$i] > 0) {
							if ($arg2->get($i) < ($neededResearch[$i] + $arg1 - 1)) {
								$r = new ArrayList();
								$r->add('techno', ResearchResource::getInfo($i, 'name'));
								$r->add('level', $neededResearch[$i] + $arg1 - 1);
								$researchList->append($r);
							}
						}
					}
					if ($researchList->size() > 0) {
						return $researchList;
					} else {
						return TRUE;
					}
					break;
				// est-ce qu'on peut construire la techno ? Pas dépassé le niveau max
				// arg1 est le niveau de la technologie voulue
				case 'maxLevel' :
					if (self::isAnUnblockingTechnology($techno)) {
						return TRUE;
					} else {
						return ($arg1 <= self::getInfo($techno, 'maxLevel')) ? TRUE : FALSE;
					}
					break;
				default :
					CTR::$alert->add('Erreur dans haveRights() de TechnologyResource', ALERT_BUG_ERROR);
					return FALSE;
			}
		} else {
			CTR::$alert->add('Technologie inexistante dans haveRights() de TechnologyResource', ALERT_BUG_ERROR);
			return FALSE;
		}
	}

	private static $technology = array(
	// UNBLOCK technologies
		// unblock buildings
		array(
			'name' => 'Long courrier',
			'progName' => 'comPlatUnblock',
			'imageLink' => 'complatunblock',
			'requiredTechnosphere' => 8,
			'requiredResearch' => array(0,0,0,0,0,3,0,2,0,0),
			'time' => 14400,
			'resource' => 550,
			'credit' => 6000,
			'points' => 22,
			'column' => 1,
			'shortDescription' => 'Débloque la Plateforme Commerciale.',
			'description' => 'Cette technologie unique en son genre, vous permet de construire une plateforme commerciale. En effet, elle développe les voyages longues distances sur les vaisseaux de marchandises, ce qui vous donne la possibilité de commercer avec tous les marchands de la galaxie.'
		),
		array(
			'name' => 'Grue magnétique',
			'progName' => 'dock2Unblock',
			'imageLink' => 'dock2unblock',
			'requiredTechnosphere' => 16,
			'requiredResearch' => array(3,2,0,0,0,0,0,0,0,0),
			'time' => 15000,
			'resource' => 600,
			'credit' => 7000,
			'points' => 22,
			'column' => 1,
			'shortDescription' => 'Débloque le Chantier de Ligne.',
			'description' => 'La grue magnétique est une infrastructure capable de déplacer d’énormes charges. Elle est utile dans le Chantier de Ligne pour construire des vaisseaux plus lourds et plus grands que ceux du chantier Alpha.'
		),
		array(
			'name' => 'Champ magnétique',
			'progName' => 'dock3Unblock',
			'imageLink' => 'dock3unblock',
			'requiredTechnosphere' => 16,
			'requiredResearch' => array(7,9,0,0,0,0,0,0,0,0),
			'time' => 24600,
			'resource' => 980,
			'credit' => 10000,
			'points' => 44,
			'column' => 1,
			'shortDescription' => 'Débloque la Colonne d\'Assemblage.',
			'description' => 'Formant un champ magnétique puissant autour de votre base orbitale et de votre planète, le champ magnétique est un système de défense qui ralentit les vaisseaux attaquants, donnant de ce fait plus de chance à vos flottes de défenses de faire mouche.'
		),
		array(
			'name' => 'Recycleur autonome',
			'progName' => 'recyclingUnblock',
			'imageLink' => 'recyclingunblock',
			'requiredTechnosphere' => 8,
			'requiredResearch' => array(7,8,8,0,0,0,0,0,0,0),
			'time' => 25800,
			'resource' => 950,
			'credit' => 9800,
			'points' => 44,
			'column' => 1,
			'shortDescription' => 'Débloque le Centre de Recyclage.',
			'description' => 'Cette technologie novatrice implémente un système intelligent sur les contrôleurs des vaisseaux de recyclages. Grâce à ce système, les recycleurs peuvent cibler et collecter les déchets gravitant autour d\'une planète de manière autonome.'
		),
		array(
			'name' => 'Vaisseaux de transport',
			'progName' => 'spatioportUnblock',
			'imageLink' => 'spatioportunblock',
			'requiredTechnosphere' => 16,
			'requiredResearch' => array(7,8,8,0,0,0,0,0,0,0),
			'time' => 25800,
			'resource' => 950,
			'credit' => 9800,
			'points' => 44,
			'column' => 1,
			'shortDescription' => 'Débloque le Spatioport.',
			'description' => 'Grâce à un nouveau système d\'optimisation, des vaisseaux de transports peuvent être développés efficacement. Ces vaisseaux permettent de faire de long trajets et de transporter beaucoup de denrées. Vous pourrez créer des routes commerciales avec différents partenaires commerciaux.'
		),
		// unblock ships
		array(
			'name' => 'Chassis simple léger',
			'progName' => 'ship0Unblock',
			'imageLink' => 'ship0unblock',
			'requiredTechnosphere' => 1,
			'requiredResearch' => array(0,0,0,0,0,0,0,0,0,0),
			'time' => 20,
			'resource' => 150,
			'credit' => 1000,
			'points' => 4,
			'column' => 2,
			'shortDescription' => 'Débloque le Pégase.',
			'description' => 'Le châssis léger simple est une technologie qui vous permet de développer et de produire vos tous premiers vaisseaux, les Pégases.'
		),
		array(
			'name' => 'Chassis simple amélioré',
			'progName' => 'ship1Unblock',
			'imageLink' => 'ship1unblock',
			'requiredTechnosphere' => 6,
			'requiredResearch' => array(1,0,0,0,0,0,0,0,0,0),
			'time' => 9000,
			'resource' => 200,
			'credit' => 1500,
			'points' => 6,
			'column' => 2,
			'shortDescription' => 'Débloque le Satyre.',
			'description' => 'En modifiant quelque peu le châssis léger simple et en transformant la carcasse d’un pégase, vos chercheurs peuvent développer un nouveau type de chasseur, le Satyre.'
		),
		array(
			'name' => 'Chassis simple double',
			'progName' => 'ship2Unblock',
			'imageLink' => 'ship2unblock',
			'requiredTechnosphere' => 10,
			'requiredResearch' => array(2,2,0,0,0,0,0,0,0,0),
			'time' => 29400,
			'resource' => 800,
			'credit' => 7500,
			'points' => 8,
			'column' => 2,
			'shortDescription' => 'Débloque la Chimère.',
			'description' => 'Le châssis simple double est un développement relativement complexe qui vous permet de créer des Chimères, un type de navette multi-tourelle.'
		),
		array(
			'name' => 'Chassis nodal léger',
			'progName' => 'ship3Unblock',
			'imageLink' => 'ship3unblock',
			'requiredTechnosphere' => 16,
			'requiredResearch' => array(4,5,0,0,0,0,0,0,0,0),
			'time' => 10800,
			'resource' => 200,
			'credit' => 1800,
			'points' => 10,
			'column' => 2,
			'shortDescription' => 'Débloque la Sirène.',
			'description' => 'Le châssis nodal, permettant la construction de la corvette « Sirène », améliore et renforce considérablement la puissance de défense des vaisseaux.'
		),
		array(
			'name' => 'Chassis nodal amélioré',
			'progName' => 'ship4Unblock',
			'imageLink' => 'ship4unblock',
			'requiredTechnosphere' => 20,
			'requiredResearch' => array(5,6,0,0,0,0,0,2,3,0),
			'time' => 19200,
			'resource' => 500,
			'credit' => 5500,
			'points' => 12,
			'column' => 2,
			'shortDescription' => 'Débloque la Dryade.',
			'description' => 'Le châssis nodal amélioré, plus grand et plus solide que le châssis nodal, vous donne la possibilité de produire la corvette « Dryade ». Cette corvette gagne, grâce à ce nouveau châssis, en puissance d’attaque et en défense.'
		),
		array(
			'name' => 'Chassis nodal lourd',
			'progName' => 'ship5Unblock',
			'imageLink' => 'ship5unblock',
			'requiredTechnosphere' => 26,
			'requiredResearch' => array(7,8,0,0,0,0,0,0,4,6),
			'time' => 46800,
			'resource' => 1100,
			'credit' => 12500,
			'points' => 14,
			'column' => 2,
			'shortDescription' => 'Débloque la Méduse.',
			'description' => 'Plus compliqué et plus performant que les deux châssis nodaux précédents, le châssis nodal lourd est spécialement adapté pour les vaisseaux de type multi-tourelle. En effet, grâce au renforcement de ce châssis et à l’amélioration de la corvette « Dryade », vous pouvez désormais construire la corvette multi-tourelle « Méduse ».'
		),
		array(
			'name' => 'Chassis modulaire',
			'progName' => 'ship6Unblock',
			'imageLink' => 'ship6unblock',
			'requiredTechnosphere' => 14,
			'requiredResearch' => array(3,3,0,0,0,0,0,0,0,0),
			'time' => 17400,
			'resource' => 450,
			'credit' => 5000,
			'points' => 16,
			'column' => 3,
			'shortDescription' => 'Débloque le Griffon.',
			'description' => 'Le châssis modulaire, nouvelle découverte de votre éminent groupe de recherche, vous permet de construire un nouveau type de vaisseau, les frégates d’attaque de type Griffon.'
		),
		array(
			'name' => 'Chassis modulaire renforcé',
			'progName' => 'ship7Unblock',
			'imageLink' => 'ship7unblock',
			'requiredTechnosphere' => 20,
			'requiredResearch' => array(4,6,0,0,0,0,0,0,0,0),
			'time' => 21600,
			'resource' => 550,
			'credit' => 5000,
			'points' => 18,
			'column' => 3,
			'shortDescription' => 'Débloque le Cyclope.',
			'description' => 'En renforçant et en améliorant légèrement son petit frère, le châssis modulaire, un groupe de chercheur a découvert une nouvelle frégate. En effet, plus puissante et plus grosse, la frégate ionique vient embellir vos rangs.'
		),
		array(
			'name' => 'Chassis polymère',
			'progName' => 'ship8Unblock',
			'imageLink' => 'ship8unblock',
			'requiredTechnosphere' => 24,
			'requiredResearch' => array(6,6,0,0,0,0,0,5,4,0),
			'time' => 30000,
			'resource' => 900,
			'credit' => 7500,
			'points' => 20,
			'column' => 3,
			'shortDescription' => 'Débloque le Minotaure.',
			'description' => 'Le châssis polymère, développé pour la construction de destroyer, vous donnera la possibilité de construire le destroyer Minotaure. Ce châssis est trois fois plus grand que le châssis modulaire, mais également beaucoup plus solide.'
		),
		array(
			'name' => 'Chassis polymère renforcé',
			'progName' => 'ship9Unblock',
			'imageLink' => 'ship9unblock',
			'requiredTechnosphere' => 30,
			'requiredResearch' => array(6,7,0,0,0,0,0,6,4,0),
			'time' => 34800,
			'resource' => 1000,
			'credit' => 9000,
			'points' => 26,
			'column' => 3,
			'shortDescription' => 'Débloque l\'Hydre.',
			'description' => 'Le châssis polymère renforcé a été développé pour améliorer la stabilité du destroyer missile Hydre, dans le but de lui permettre de lancer de plus gros missiles tout en gardant une bonne précision.'
		),
		array(
			'name' => 'Chassis polymère amélioré',
			'progName' => 'ship10Unblock',
			'imageLink' => 'ship10unblock',
			'requiredTechnosphere' => 34,
			'requiredResearch' => array(10,11,0,0,0,0,0,0,7,9),
			'time' => 45600,
			'resource' => 1100,
			'credit' => 12000,
			'points' => 32,
			'column' => 3,
			'shortDescription' => 'Débloque le Cerbère.',
			'description' => 'En modifiant quelque peu le châssis polymère renforcé, une de vos équipes de chercheurs a développé un nouveau type de vaisseau – le Croiseur Cerbère.'
		),
		array(
			'name' => 'Chassis polymère lourd',
			'progName' => 'ship11Unblock',
			'imageLink' => 'ship11unblock',
			'requiredTechnosphere' => 40,
			'requiredResearch' => array(15,13,0,0,0,0,0,0,10,13),
			'time' => 69000,
			'resource' => 1600,
			'credit' => 16500,
			'points' => 40,
			'column' => 3,
			'shortDescription' => 'Débloque le Phénix.',
			'description' => 'Cette amélioration du châssis polymère amélioré vous permet de construire le vaisseau le plus puissant de la galaxie de l’œil – Le Phénix. En renforçant et en améliorant l’ergonomie de ce châssis, vos chercheurs ont pu stabiliser et augmenter la puissance de feu du vaisseau.'
		),
		array(
			'name' => 'Colonisation',
			'progName' => 'colonization',
			'imageLink' => 'colonization',
			'requiredTechnosphere' => 14,
			'requiredResearch' => array(0,0,0,0,0,5,0,3,5,0),
			'time' => 25000,
			'resource' => 15000,
			'credit' => 20000,
			'points' => 60,
			'column' => 1,
			'shortDescription' => 'Vous permet de coloniser de nouvelles planètes.',
			'description' => 'La Colonisation est une technologie relative à vos commandants. En effet, elle offrira à vos chefs d’escadrille la possibilité de prendre possession d’une ou plusieurs planètes vides. Les planètes vides sont des lieux n’appartenant à personne. Cette technologie vous ouvrira les portes de l’agrandissement de votre royaume.'
		),
		array(
			'name' => 'Conquête',
			'progName' => 'conquest',
			'imageLink' => 'conquest',
			'requiredTechnosphere' => 25,
			'requiredResearch' => array(0,0,0,0,0,8,0,8,9,0),
			'time' => 40000,
			'resource' => 30000,
			'credit' => 40000,
			'points' => 100,
			'column' => 1,
			'shortDescription' => 'Vous permet de conquérir les planètes d\'autres joueurs.',
			'description' => 'La technologie « Conquête » va permettre à vos commandants de coloniser des planètes ennemies. Vous pourrez désormais commencer votre guerre de conquête dans la galaxie de l’Œil pour le salut de votre faction.'
		),

	// LEVEL technologies

		array(
			'name' => 'Ingénierie du bâtiment',
			'progName' => 'generatorSpeed',
			'imageLink' => 'generatorspeed',
			'requiredTechnosphere' => 6,
			'requiredResearch' => array(0,0,0,0,0,0,0,4,4,0),
			'time' => 40800,
			'maxLevel' => 25,
			'category' => 2,
			'resource' => 1150,
			'credit' => 10500,
			'points' => 30,
			'column' => 4,
			'shortDescription' => 'Augmente la vitesse de votre Générateur de 3% par niveau.',
			'description' => 'En adoptant de nouvelles mesures dans le milieu de la construction et en associant différents domaines de recherche, vos ingénieures vous permettent de construire vos bâtiments de façon optimale. En effet, grâce à une meilleure coordination au sein de votre générateur, vous pouvez construire plus vite vos infrastructures.',
			'bonus' => 3
		),
		array(
			'name' => 'Craquage catalytique',
			'progName' => 'refineryRefining',
			'imageLink' => 'refineryrefining',
			'requiredTechnosphere' => 10,
			'requiredResearch' => array(0,0,0,0,0,5,6,0,0,0),
			'time' => 37800,
			'maxLevel' => 25,
			'category' => 3,
			'resource' => 110,
			'credit' => 11500,
			'points' => 28,
			'column' => 4,
			'shortDescription' => 'Augmente la production de votre Raffinerie de 2% par niveau.',
			'description' => 'La découverte d’un nouveau procédé dans la chaine de raffinage de vos ressources vous permet d’augmenter votre production de ressources à l’heure.',
			'bonus' => 2
		),
		array(
			'name' => 'Silo-compresseur',
			'progName' => 'refineryStorage',
			'imageLink' => 'refinerystorage',
			'requiredTechnosphere' => 14,
			'requiredResearch' => array(0,0,0,0,0,7,7,0,0,0),
			'time' => 46800,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1300,
			'credit' => 12500,
			'points' => 39,
			'column' => 4,
			'shortDescription' => 'Augmente la taille de votre Stockage de ressources de 2% par niveau.',
			'description' => 'Grâce à l’association de plusieurs domaines de recherche, une de vos équipes vous donne la possibilité d’augmenter vos capacités de stockage. Cette nouvelle amélioration développe une technique de compression de vos ressources augmentant la capacité de stockage de vos silos, sans devoir augmenter leur taille.',
			'bonus' => 2
		),
		array(
			'name' => 'Chaîne d\'assemblage',
			'progName' => 'dock1Speed',
			'imageLink' => 'dock1speed',
			'requiredTechnosphere' => 16,
			'requiredResearch' => array(0,0,0,0,0,0,0,8,9,0),
			'time' => 27000,
			'maxLevel' => 25,
			'category' => 2,
			'resource' => 850,
			'credit' => 10000,
			'points' => 21,
			'column' => 4,
			'shortDescription' => 'Augmente la vitesse d\'assemblage de votre Chantier Alpha de 3% par niveau.',
			'description' => 'La chaine d’assemblage est un procédé optimisant d’avantage votre processus de construction de vaisseaux. En termes plus clairs, ce développement diminue le temps de fabrication des pièces nécessaires à vos vaisseaux dans le Chantier Alpha.',
			'bonus' => 3
		),
		array(
			'name' => 'Chaîne d\'assemblage lourde',
			'progName' => 'dock2Speed',
			'imageLink' => 'dock2speed',
			'requiredTechnosphere' => 17,
			'requiredResearch' => array(0,0,0,0,0,0,0,11,12,0),
			'time' => 69000,
			'maxLevel' => 25,
			'category' => 2,
			'resource' => 1850,
			'credit' => 17000,
			'points' => 48,
			'column' => 4,
			'shortDescription' => 'Augmente la vitesse d\'assemblage de votre Chantier de Ligne de 3% par niveau.',
			'description' => 'En augmentant les capacités de production dans le chantier Alpha, vos chercheurs ont également réussit à l’adapter au Chantier de Ligne, permettant ainsi de diminuer le temps de construction des plus gros vaisseaux.',
			'bonus' => 3
		),
		array(
			'name' => 'Intelligence artificielle',
			'progName' => 'technosphereSpeed',
			'imageLink' => 'technospherespeed',
			'requiredTechnosphere' => 12,
			'requiredResearch' => array(0,0,0,0,0,0,0,0,9,7),
			'time' => 60000,
			'maxLevel' => 25,
			'category' => 2,
			'resource' => 1740,
			'credit' => 16000,
			'points' => 45,
			'column' => 5,
			'shortDescription' => 'Augmente la vitesse de développement de technologies dans la Technosphère de 3% par niveau.',
			'description' => 'La technologie « Intelligence artificielle » vous permet d’augmenter la vitesse de recherche de vos technologies. En effet, après que vos scientifiques aient pu mettre en place un réseau de robotique pour gérer la totalité de votre Technosphère, le complexe a gagné en efficacité.',
			'bonus' => 3
		),
		array(
			'name' => 'Mercatique',
			'progName' => 'commercialIncomeUp',
			'imageLink' => 'commercialincome',
			'requiredTechnosphere' => 15,
			'requiredResearch' => array(0,0,0,0,0,9,8,0,0,0),
			'time' => 51000,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1600,
			'credit' => 15500,
			'points' => 44,
			'column' => 5,
			'shortDescription' => 'Augmente la production de vos routes commerciales de 2% par niveau.',
			'description' => 'Cette nouvelle technologie, basée sur une optimisation des informations et des transferts monétaires entre les différentes planètes, améliore considérablement le revenu de vos routes commerciales.',
			'bonus' => 2
		),
		array(
			'name' => 'Générateur de gravité étendu',
			'progName' => 'gravitModuleUp',
			'imageLink' => 'gravitmodule',
			'requiredTechnosphere' => 25,
			'requiredResearch' => array(0,0,0,0,0,9,10,0,0,0),
			'time' => 58800,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1700,
			'credit' => 17500,
			'points' => 46,
			'column' => 4,
			'shortDescription' => 'Augmente la puissance de votre Module Gravitationnel de 2% par niveau.',
			'description' => 'En améliorant la portée magnétique de votre générateur de gravité, cette technologie vous permet d’augmenter votre système défensif, ce qui augmentera la capacité de vos vaisseaux à toucher les ennemis.',
			'bonus' => 2
		),
		array(
			'name' => 'Chaîne de production lourde',
			'progName' => 'dock3Speed',
			'imageLink' => 'dock3speed',
			'requiredTechnosphere' => 32,
			'requiredResearch' => array(0,0,0,0,0,0,0,14,15,0),
			'time' => 64800,
			'maxLevel' => 25,
			'category' => 2,
			'resource' => 1900,
			'credit' => 17500,
			'points' => 47,
			'column' => 4,
			'shortDescription' => 'Augmente la vitesse de production de de votre Colonne d\'Assemblage de 3% par niveau.',
			'description' => 'Utilisant les mêmes principes économiques que les deux chaines d’assemblage, la chaine de production lourde améliore et optimise le temps de construction de vos vaisseaux-mères dans la colonne d’assemblage.',
			'bonus' => 3
		),
		array(
			'name' => 'Economie sociale de marché',
			'progName' => 'populationTaxUp',
			'imageLink' => 'populationtax',
			'requiredTechnosphere' => 20,
			'requiredResearch' => array(0,0,0,0,0,10,8,0,0,0),
			'time' => 41400,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1000,
			'credit' => 11500,
			'points' => 33,
			'column' => 5,
			'shortDescription' => 'Augmente les impôts que vous percevez à votre population de 2% par niveau.',
			'description' => 'L’économie sociale de marché est un procédé simple de gestion des taxes de votre planète. Cette amélioration vous donne la possibilité de gérer de façon plus précise vos entrés de crédit en terme d’imposition de la population.',
			'bonus' => 2
		),
		array(
			'name' => 'Formation continue',
			'progName' => 'commanderInvestUp',
			'imageLink' => 'commanderinvest',
			'requiredTechnosphere' => 21,
			'requiredResearch' => array(0,0,0,0,0,0,10,0,0,0),
			'time' => 52200,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1680,
			'credit' => 16000,
			'points' => 42,
			'column' => 5,
			'shortDescription' => 'Accélère la formation de vos commandants dans l\'école de 2% par niveau.',
			'description' => 'La formation continue est une technologie appliquée à votre école de commandement. Elle vous permet de former plus précisément les commandants en place dans votre école et ainsi augmenter leurs aptitudes au combat et à la stratégie.',
			'bonus' => 2
		),
		array(
			'name' => 'Lobbying universitaire',
			'progName' => 'uniInvestUp',
			'imageLink' => 'uniinvest',
			'requiredTechnosphere' => 9,
			'requiredResearch' => array(0,0,0,0,0,4,6,0,0,0),
			'time' => 38000,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1100,
			'credit' => 11500,
			'points' => 30,
			'column' => 5,
			'shortDescription' => 'Rend plus efficace votre investissement dans l\'Université de 2% par niveau.',
			'description' => 'Le lobbying universitaire est une technologie mise en place dans le but d’augmenter les échanges entre les différentes facultés de votre université et votre gouvernement. Cette technologie permet à votre université de disposer de plus de fonds pour financer les facultés.',
			'bonus' => 2
		),
		array(
			'name' => 'Surveillance réseau',
			'progName' => 'antiSpyInvestUp',
			'imageLink' => 'antispyinvest',
			'requiredTechnosphere' => 13,
			'requiredResearch' => array(0,0,0,0,0,9,11,0,0,0),
			'time' => 50000,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1630,
			'credit' => 15900,
			'points' => 41,
			'column' => 5,
			'shortDescription' => 'Rend plus efficace votre investissement dans l\'Anti-Espionnage de 2% par niveau.',
			'description' => 'La surveillance réseau est une technologie qui vous permet d’augmenter votre champ de contre-espionnage. Vos services secrets, grâce à d’importantes avancées en matière d’observation et d’espionnage, augmentent considérablement leur champ de vision sans aucune augmentation des coûts.',
			'bonus' => 2
		),
		array(
			'name' => 'Propulsion hyperspatiale',
			'progName' => 'spaceShipsSpeed',
			'imageLink' => 'spaceshipsspeed',
			'requiredTechnosphere' => 25,
			'requiredResearch' => array(0,0,0,0,0,0,0,10,0,8),
			'time' => 60000,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1900,
			'credit' => 17800,
			'points' => 48,
			'column' => 5,
			'shortDescription' => 'Augmente la vitesse de vos vaisseaux de 3% par niveau.',
			'description' => 'La propulsion hyper-spatiale est une innovation importante pour la bonne marche de vos conquêtes. En effet, cette technologie augmente la vitesse de vos flottes, vous permettant donc de conquérir des planètes plus éloignées de votre base principale.',
			'bonus' => 3
		),
		array(
			'name' => 'Silo de transport',
			'progName' => 'spaceShipsContainer',
			'imageLink' => 'spaceshipscontainer',
			'requiredTechnosphere' => 20,
			'requiredResearch' => array(0,0,0,0,0,5,0,0,0,0),
			'time' => 49800,
			'maxLevel' => 30,
			'category' => 2,
			'resource' => 1650,
			'credit' => 16000,
			'points' => 43,
			'column' => 5,
			'shortDescription' => 'Augmente la capactité à transporter des ressources de vos vaisseaux de 2% par niveau.',
			'description' => 'En mariant différents domaines scientifiques et en se basant sur les résultats du silo-compresseur, vos chercheurs ont réussi à adapter ce système à vos vaisseaux. Cette technique permet à vos flottes de ramener plus de ressource dans leur soute de transport.',
			'bonus' => 2
		),
		array(
			'name' => 'Administration étendue',
			'progName' => 'baseQuantity',
			'imageLink' => 'basequantity',
			'requiredTechnosphere' => 15,
			'requiredResearch' => array(5,0,0,0,4,4,0,4,0,0),
			'time' => 47300,
			'maxLevel' => 14,
			'category' => 1,
			'resource' => 4000,
			'credit' => 48000,
			'points' => 43,
			'column' => 1,
			'shortDescription' => 'Chaque niveau supplémentaire de cette technologie vous permet de gérer une base de plus (colonisation ou conquête)',
			'description' => 'La technologie Administration étendue a été initiée par votre ministre des affaires étrangères, de manière à pouvoir contrôler plus facilement vos territoires colonisés. En effet, cette technologie vous permet de mettre en place votre gouvernement et de contrôler la population des planètes sous votre contrôle. Chaque niveau de cette technologie vous permet de coloniser ou de conquérir une planète de plus.',
			'bonus' => 1
		),
		array(
			'name' => 'Turbo-propulsion',
			'progName' => 'fighterSpeed',
			'imageLink' => 'fighterspeed',
			'requiredTechnosphere' => 4,
			'requiredResearch' => array(0,0,0,0,0,0,0,0,5,0),
			'time' => 22800,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 680,
			'credit' => 5500,
			'points' => 19,
			'column' => 6,
			'shortDescription' => 'Améliore la vitesse de vos chasseurs de 3% par niveau.',
			'description' => 'Cette technologie améliore la vitesse de vos chasseurs en augmentant le nombre de réacteurs. Vous pourrez donc attaquer plus rapidement vos ennemis lors des combats.',
			'bonus' => 3
		),
		array(
			'name' => 'Gros calibre',
			'progName' => 'fighterAttack',
			'imageLink' => 'fighterattack',
			'requiredTechnosphere' => 9,
			'requiredResearch' => array(4,6,0,0,0,0,0,0,0,0),
			'time' => 29400,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 880,
			'credit' => 9200,
			'points' => 25,
			'column' => 6,
			'shortDescription' => 'Améliore l\'attaque de vos chasseurs de 3% par niveau.',
			'description' => 'La technologie du calibre 5 améliore directement l’armement de vos chasseurs. Vos Pégases et vos Satyres auront, de ce fait, une plus grande puissance de feu face à vos ennemis.',
			'bonus' => 3
		),
		array(
			'name' => 'Bouclier simple',
			'progName' => 'fighterDefense',
			'imageLink' => 'fighterdefense',
			'requiredTechnosphere' => 12,
			'requiredResearch' => array(0,0,0,0,5,0,0,0,0,0),
			'time' => 36000,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1000,
			'credit' => 8400,
			'points' => 29,
			'column' => 6,
			'shortDescription' => 'Améliore la défense de vos chasseurs de 3% par niveau.',
			'description' => 'Le petit bouclier est un système de défense qui enveloppe vos chasseurs d’un film laser, leur permettant de mieux résister aux impacts de vos ennemis.',
			'bonus' => 3
		),
		array(
			'name' => 'Turbo-propulsion améliorée',
			'progName' => 'corvetteSpeed',
			'imageLink' => 'corvettespeed',
			'requiredTechnosphere' => 15,
			'requiredResearch' => array(0,0,0,0,0,0,0,0,8,0),
			'time' => 27600,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 750,
			'credit' => 6800,
			'points' => 23,
			'column' => 6,
			'shortDescription' => 'Améliore la vitesse de vos corvettes de 3% par niveau.',
			'description' => 'La propulsion améliorée est une technologie qui booste les réacteurs de vos corvettes, améliorant ainsi leur vitesse et leur chance d’éviter les impacts.',
			'bonus' => 3
		),
		array(
			'name' => 'Armement laser',
			'progName' => 'corvetteAttack',
			'imageLink' => 'corvetteattack',
			'requiredTechnosphere' => 18,
			'requiredResearch' => array(8,9,0,0,0,0,0,0,0,0),
			'time' => 33600,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 920,
			'credit' => 9800,
			'points' => 26,
			'column' => 6,
			'shortDescription' => 'Améliore l\'attaque de vos corvettes de 3% par niveau.',
			'description' => 'Cette nouvelle technologie augmente le faisceau des canons laser de vos corvettes. Celles-ci seront donc plus puissantes aux combats spatiaux ou attaques de planètes.',
			'bonus' => 3
		),
		array(
			'name' => 'Bouclier renforcé',
			'progName' => 'corvetteDefense',
			'imageLink' => 'corvettedefense',
			'requiredTechnosphere' => 20,
			'requiredResearch' => array(0,0,0,0,8,0,0,0,0,0),
			'time' => 45000,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1580,
			'credit' => 16500,
			'points' => 38,
			'column' => 6,
			'shortDescription' => 'Améliore la défense de vos corvettes de 3% par niveau.',
			'description' => 'Le bouclier, plus résistant que le petit bouclier, a été élaboré pour augmenter le système de défense de vos corvettes.',
			'bonus' => 3
		),
		array(
			'name' => 'Turbo-propulsion double',
			'progName' => 'frigateSpeed',
			'imageLink' => 'frigatespeed',
			'requiredTechnosphere' => 2,
			'requiredResearch' => array(0,0,0,0,0,0,0,0,10,0),
			'time' => 33000,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 980,
			'credit' => 10500,
			'points' => 24,
			'column' => 7,
			'shortDescription' => 'Améliore la vitesse de vos frégates de 3% par niveau.',
			'description' => 'La super propulsion est un système de propulsion héliothermique. Ce système permet à tous vos vaisseaux de type frégate de gagner en vitesse.',
			'bonus' => 3
		),
		array(
			'name' => 'Tête chercheuse',
			'progName' => 'frigateAttack',
			'imageLink' => 'frigateattack',
			'requiredTechnosphere' => 26,
			'requiredResearch' => array(10,0,11,0,0,0,0,0,0,0),
			'time' => 41400,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1220,
			'credit' => 11500,
			'points' => 34,
			'column' => 7,
			'shortDescription' => 'Améliore l\'attaque de vos frégates de 3% par niveau.',
			'description' => 'La technologie tête chercheuse augmente la capacité des missiles de vos frégates à toucher leurs cibles. Cette technique vous permettra d’imposer plus de dégâts aux navettes ennemies.',
			'bonus' => 3
		),
		array(
			'name' => 'Bouclier lourd',
			'progName' => 'frigateDefense',
			'imageLink' => 'frigatedefense',
			'requiredTechnosphere' => 28,
			'requiredResearch' => array(0,0,0,9,11,0,0,0,0,0),
			'time' => 59400,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1780,
			'credit' => 17200,
			'points' => 45,
			'column' => 7,
			'shortDescription' => 'Améliore la défense de vos frégates de 3% par niveau.',
			'description' => 'Le bouclier renforcé dispose de deux protections laser enveloppent complètement la coque de vos frégates tout en procurant une protection idéal face aux attaques de type missile.',
			'bonus' => 3
		),
		array(
			'name' => 'Turbo-propulsion triple',
			'progName' => 'destroyerSpeed',
			'imageLink' => 'destroyerspeed',
			'requiredTechnosphere' => 30,
			'requiredResearch' => array(0,0,0,0,0,9,0,0,14,0),
			'time' => 34200,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 900,
			'credit' => 8800,
			'points' => 24,
			'column' => 7,
			'shortDescription' => 'Améliore la vitesse de vos destroyers/croiseurs de 3% par niveau.',
			'description' => 'La turbo-propulsion est basée sur un système photonique, lui permettant d’augmenter sa rapidité à proximité d’une étoile. Cette amélioration augmentera la vitesse de tous vos destroyers.',
			'bonus' => 3
		),
		array(
			'name' => 'Canon longue portée',
			'progName' => 'destroyerAttack',
			'imageLink' => 'destroyerattack',
			'requiredTechnosphere' => 38,
			'requiredResearch' => array(15,0,17,0,0,9,0,0,0,0),
			'time' => 45600,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1620,
			'credit' => 15500,
			'points' => 38,
			'column' => 7,
			'shortDescription' => 'Améliore l\'attaque de vos destroyers/croiseurs de 3% par niveau.',
			'description' => 'L’armement lourd est une technologie améliorant la capacité d’attaque de vos destroyers. Ce système transformera quelque peu les canons de vos destroyers, leur offrant la possibilité d’augmenter leur puissance de feu dans les combats spatiaux.',
			'bonus' => 3
		),
		array(
			'name' => 'Bouclier lourd amélioré',
			'progName' => 'destroyerDefense',
			'imageLink' => 'destroyerdefense',
			'requiredTechnosphere' => 40,
			'requiredResearch' => array(0,0,0,15,18,9,0,0,0,0),
			'time' => 72000,
			'maxLevel' => 30,
			'category' => 3,
			'resource' => 1980,
			'credit' => 20000,
			'points' => 49,
			'column' => 7,
			'shortDescription' => 'Améliore la défense de vos destroyers/croiseurs de 3% par niveau.',
			'description' => 'Le bouclier amélioré n’est autre qu’une amélioration du bouclier renforcé. En effet, cette technologie n’apporte pas de modification majeur par rapport au système précédent, elle est simplement adapté aux vaisseaux de type destroyer.',
			'bonus' => 3
		)
	);

}
?>