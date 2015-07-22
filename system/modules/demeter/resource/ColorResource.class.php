<?php
class ColorResource {
	# constants for factions
	const NO_FACTION = 0;
	const EMPIRE = 1;
	const KOVAHK = 2;
	const NEGORA = 3;
	const CARDAN = 4;
	const NERVE = 5;
	const APHERA = 6;
	const SYNELLE = 7;

	const DEFENSELITTLESHIPBONUS = 0;
	const PRICEBIGSHIPBONUS = 1;
	const SPEEDLITTLESHIPBONUS = 2;
	const DEFENSELITTLESHIPMALUS = 3;
	const COMMERCIALROUTEBONUS = 4;
	const COMMERCIALROUTEPRICEBONUS = 5;
	const TAXBONUS = 6;
	const COLOPRICEBONUS = 7;
	const LOOTRESOURCESMALUS = 8;
	const RAFINERYBONUS = 9;
	const STORAGEBONUS = 10;
	const BIGACADEMICBONUS = 11;
	const TECHNOLOGYBONUS = 12;
	const COMMANDERSCHOOLBONUS = 13;
	const LITTLEACADEMICBONUS = 14;

	# constants for the actions bonuses of the factions
	const BONUS_EMPIRE_CRUISER = 5;		# price 5% less for cruiser and heavy cruiser
	const BONUS_NEGORA_ROUTE = 3;		# price 3% less for the price of a commercial route
	const BONUS_CARDAN_COLO = 10;		# price 10% less for colo or conquest
	const BONUS_APHERA_TECHNO = 2;		# 4% less time to build technologies

	public static function getInfo($id, $info) {
		if ($id <= count(self::$colors)) {
			if (in_array($info, [
				'id', 
				'officialName',
				'popularName', 
				'government', 
				'demonym', 
				'factionPoint', 
				'status',
				'regime',
				'devise', 
				'desc1', 
				'desc2', 
				'desc3',
				'desc4',
				'bonus',
				'mandateDuration',
				'senateDesc',
				'campaignDesc'])) {
				return self::$colors[$id][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function getBonus($i) {
		return self::$bonus[$i];
	}

	public static function size() {
		return count(self::$colors);
	}

	private static $bonus = [
		# 0
		['path' => 'faction/bonus/bonus1-2.png', 'title' => '+ 5% de défense', 'desc' => 'Vos vaisseaux gagnent en défense'], # DONE
		['path' => 'faction/bonus/bonus1-3.png', 'title' => '- 5% prix', 'desc' => 'Les croiseurs et croiseurs-lourds sont moins chers'], # DONE
		['path' => 'faction/bonus/bonus2-2.png', 'title' => '+ 10% de maniabilité', 'desc' => 'Vos vaisseaux du Chantier Alpha gagnent en maniabilité'], # DONE
		['path' => 'faction/bonus/bonus2-3.png', 'title' => '- 5% de défense', 'desc' => 'Vos vaisseaux du Chantier de Ligne perdent en défense'], # DONE
		['path' => 'faction/bonus/bonus3-2.png', 'title' => '+ 5% production', 'desc' => 'Vos routes commerciales produisent plus de crédits'], # DONE
		# 5
		['path' => 'faction/bonus/bonus3-3.png', 'title' => '- 3% prix', 'desc' => 'Vos routes commerciales coûtent moins de crédits'], # DONE
		['path' => 'faction/bonus/bonus4-2.png', 'title' => '+ 3% de crédits', 'desc' => 'Vos impôts vous amènent plus de crédits'], # DONE
		['path' => 'faction/bonus/bonus4-3.png', 'title' => '- 10% prix', 'desc' => 'Vos colonisations et conquêtes sont moins chères'], # DONE
		['path' => 'faction/bonus/bonus4-4.png', 'title' => '- 5% de ressources', 'desc' => 'Une partie des ressources pillées sont offertes aux Dieux'], # DONE
		['path' => 'faction/bonus/bonus5-2.png', 'title' => '+ 4% production', 'desc' => 'Votre Raffinerie produit plus de ressources'], # DONDE
		#10
		['path' => 'faction/bonus/bonus5-3.png', 'title' => '+ 4% stockage', 'desc' => 'Votre Raffinerie stocke plus de ressources'], # DONE
		['path' => 'faction/bonus/bonus6-2.png', 'title' => '+ 4% efficacité', 'desc' => 'Votre Université est plus efficace'], # DONE
		['path' => 'faction/bonus/bonus6-3.png', 'title' => '- 2% temps', 'desc' => 'Vos technologies se développent plus rapidement'], # DONE
		['path' => 'faction/bonus/bonus7-2.png', 'title' => '+ 6% efficacité', 'desc' => 'Votre Ecole de Commandement est plus efficace'], # DONE
		['path' => 'faction/bonus/bonus7-3.png', 'title' => '+ 2% efficacité', 'desc' => 'Votre Université est plus efficace'] # DONE
		#15
	];

	private static $colors = [
		[
			'id' 			=> 0,
			'officialName' 	=> 'Sans Faction',
			'popularName' 	=> '',
			'government' 	=> '',
			'demonym' 		=> 'Sans Faction',
			'factionPoint' 	=> '',
			'status' 		=> ['', '', '', '', '', ''],
			'regime'		=> 1,
			'devise' 		=> '',
			'desc1' => '',
			'desc2' => '',
			'desc3' => '',
			'desc4' => '',
			'bonus' => [],
			'mandateDuration' => 0,
			'senateDesc' => '',
			'campaignDesc' => ''
	], [
			'id' 			=> 1,
			'officialName' 	=> 'Ordre Impérial',
			'popularName' 	=> 'l’Empire',
			'government' 	=> 'Maison Akhénienne',
			'demonym' 		=> 'impériaux',
			'factionPoint' 	=> 'Points de Prestige',
			'status' 		=> ['Noble', 'Dynaste', 'Gardien des Coffres', 'Maréchal', 'Intendant', 'Empereur'],
			'regime'		=> 2,
			'devise' 		=> 'Des nefs d’acier,<br />Naquit l’équilibre',
			'desc1' => 'Faction centrale de la Galaxie de l’Œil, l’Ordre Impérial cherche à bâtir un empire puissant, dont les ramifications mèneraient jusqu’aux confins de la Galaxie. Un empire sous l’égide de la Maison Akhénienne, totalitaire et immuable.',
			'desc2' => 'Ordre à la forte puissance militaire, la Maison Akhénienne ne croit qu’en une chose, la suprématie de l’Empereur, seul et unique guide de cette Faction.',
			'desc3' => 'Leur technologie, fiable et robuste, repose sur de longues strates de savoir-faire et sur une base solide d’expérience en matière d’armement et d’aérospatial. Industriellement très développée, l’économie Akhénienne est une machine bien rodée, pouvant allègrement soutenir l’effort de guerre.',
			'desc4' => 'Leurs grandes richesses, principalement acquises sur les plateformes de forage et dans d’énormes concessions minières, leur apportent des ressources de qualité et en grand nombre. Les différents accords, traités et taxes commerciales ainsi que leur situation centrale dans la Galaxie, leur apportent également des revenus stables et constants.',
			'bonus' => [0, 1],
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
	], [
			'id' 			=> 2,
			'officialName' 	=> 'Province de Kovahk',
			'popularName' 	=> 'l’Essaim',
			'government' 	=> 'Maison des Kovahkarh',
			'demonym' 		=> 'kovahkarhs',
			'factionPoint' 	=> 'Points de Bataille',
			'status' 		=> ['Guerrier', 'Dynaste', 'Trésorier', 'Conquérant', 'Chambellan', 'Baron'],
			'regime'		=> 2,
			'devise' 		=> 'Eclats de métal dans le ciel',
			'desc1' => 'Fiers sont les soldats Kovahkarh, sans pareil est leur honneur dans la Galaxie de l’Oeil. Kovahk est une faction guerrière aux moeurs parfois brutales et au caractère bien trempé tel l’acier qu’ils vénèrent et adulent.',
			'desc2' => 'Libérateur des populations oppressées de l’Ancien Empire, Kovahk vénère le pouvoir du métal rarissime et infaillible, un métal qui résisterai à toutes attaques ennemies. Habiles forgerons capables de créer les alliages les plus robustes, l’essaim Kovahkarh étend son influence sur la Galaxie.',
			'desc3' => 'Leur longue tradition de pillage et d’exploration de la Galaxie, leur a amené de très grandes richesses ainsi qu’une habilté hors du commun pour la construction de vaisseaux de combat rapides et puissants.',
			'desc4' => 'Basé sur un système politique très martial et militaire dirigé par un Baron, la société Kovahkarh fait preuve d’une grande discipline et d’une rigueur sans égale. Unie dans un même idéal de victoire et d’expansion, cette faction est une des plus solides de la Galaxie.',
			'bonus' => [2, 3],
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.',
	], [
			'id' 			=> 3,
			'officialName' 	=> 'Province de Négore',
			'popularName' 	=> 'Négore',
			'government' 	=> 'Maison Négienne',
			'demonym' 		=> 'négiens',
			'factionPoint' 	=> 'Points de Marchandage',
			'status' 		=> ['Commerçant', 'Mécène', 'Grand Argentier', 'Condottiere', 'Intendant', 'Doge'],
			'regime'		=> 2,
			'devise' 		=> 'Toutes les richesses,<br />Passent par Négore',
			'desc1' => 'La maison Négienne, la plus riche de la Galaxie de l’Œil, est composée essentiellement de grands marchands et de financiers expérimentés. Considérée comme la banque de la galaxie depuis très longtemps, son économie est basée sur les échanges et le commerce.',
			'desc2' => 'Parfois peu regardant sur la provenance des marchandises, les Négiens sont d’adroits négociateurs n’hésitant pas à profiter des opportunités qu’offrent la contre-bande, le trafic d’armes ainsi que la vente d’esclaves au détriment des accords et traités commerciaux.',
			'desc3' => 'Dotée de moyens militaires fastueux, la flotte Négienne est rutilante et dissuasive, disposant d’une prodigieuse armada de vaisseaux destinés à assurer sa prospérité et son avenir économique dans la Galaxie.',
			'desc4' => 'Vivant dans le luxe et l’opulence, les Vizirs de Négore sont à la tête de fortunes faramineuses permettant les caprices les plus fous; Orgies, casinos, courses et paris sont le quotidien de cette province aux moeurs débridés.',
			'bonus' => [4, 5], 
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
	], [
			'id' 			=> 4,
			'officialName' 	=> 'Marche de Cardan',
			'popularName' 	=> 'la Marche',
			'government' 	=> 'Eglise Cardanienne',
			'demonym' 		=> 'cardaniens',
			'factionPoint' 	=> 'Points de Foi',
			'status' 		=> ['Fidèle', 'Prêtre', 'Camerlingue', 'Inquisiteur', 'Archiprêtre', 'Guide Suprême'],
			'regime'		=> 3,
			'devise' 		=> 'La lumière vous balaiera',
			'desc1' => 'L’Eglise Cardanienne est la seule faction théocratique de la Galaxie. Elle fût pendant de longues années un mouvement discriminé, peuplé uniquement par des moines et des hommes pieux. Mais des dérives fanatiques ont poussé la Marche de Cardan à devenir une faction belliqueuse et extrémiste, éblouie par un pouvoir suprême et divin qui les mènera à la victoire.',
			'desc2' => 'Les rites prennent une place très importante dans le mode de vie des Cardaniens. Nombreux sacrifices et rituels sont faits à chaque Segment en l’honneur des Dieux. Les fidèles se doivent de se plier à la loi cardanienne et respecter les ordres du Guide Suprême.',
			'desc3' => 'Une grande armée de puissants guerriers voués au culte de Cardan, fanatiques experts dans le maniement des armes, les combattants de Cardan n’ont peur que d’une seule chose : ne pas mourir en martyre et décevoir l’Ordre Suprême.',
			'desc4' => 'Ils ne pratiquent que peu de commerce avec les nations étrangères, préférant se suffir à eux-même, même si cela doit aboutir à la famine et à la mort des plus démunis.',
			'bonus' => [6, 7, 8],
			'mandateDuration' => 1209600,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'Les membres du Sénat peuvent se proposer devant les Oracles pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, la Grande Lumière, via les Oracles décide de la personne la plus apte à la représenter dans le monde physique. Cet personne reçoit le titre de Guide Suprême. 
				<br /><br />Le Guide Suprême va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Camerlingue, l\'Inquisiteur et l\'Archiprêtre de la faction. '
		], [
			'id' 			=> 5,
			'officialName' 	=> 'Province de Nerve',
			'popularName' 	=> 'la Nerve',
			'government' 	=> 'République Nervéenne',
			'demonym' 		=> 'nervéens',
			'factionPoint' 	=> 'Points d\'industrie',
			'status' 		=> ['Citoyen', 'Député', 'Ministre des Finances', 'Ministre de la Défense', 'Premier Ministre', 'Président'],
			'regime'		=> 1,
			'devise' 		=> 'Jamais ne tombera,<br />La ville aux Mille Sous-sols',
			'desc1' => 'La république Nervéenne est composée d’une grande communauté préférant vivre à l’écart, cachée dans d’incroyables labyrinthes souterrains. Elle est connue principalement pour sa capacité à camoufler la quasi-totalité de ses infrastructures à ses ennemis ainsi que pour ses qualités de bâtisseurs hors-normes.',
			'desc2' => 'Communauté soudée autour de son Président, la Nerve est une faction de grands travailleurs et de bâtisseurs parmi les plus fameux de toute la Galaxie. Ils réalisent des édifices enfouis d’une finesse et d’une complexité incroyable.',
			'desc3' => 'N’ayant pas une grande connaissance de l’art de la guerre, mais contraints de lutter pour la préservation de leur mode de vie, ils se sont adaptés et ont formé de redoutables forteresses défensives.',
			'desc4' => 'Grâce à un dédale de culture hydroponique et une grande connaissance en agro-alchimie mais également en extraction de minerai, l’industriel Nervéen produit une grande quantité de ressources attirant ainsi les marchands les plus riches de la galaxie, leur permettant ainsi un essor prospère.',
			'bonus' => [9, 10],
			'mandateDuration' => 950400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de la Nerve peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Président. 
				<br /><br />Le Président va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Ministre de la défense, le Ministre des finances et le Premier Ministre de la faction. '
		], [
			'id' 			=> 6,
			'officialName' 	=> 'Province d’Aphéra',
			'popularName' 	=> 'Aphéra',
			'government' 	=> 'République Aphéréenne',
			'demonym' 		=> 'aphéréens',
			'factionPoint' 	=> 'Points de Technologie',
			'status' 		=> ['Citoyen', 'Technocrate', 'Algorithmicien', 'Tacticien', 'Archiviste', 'Autarque'],
			'regime'		=> 1,
			'devise' 		=> 'Au travers du vide,<br />Nos oiseaux percent,<br/>Levez les yeux',
			'desc1' => 'La république d’Aphéra, réputée pour son potentiel scientifique, est une faction composée des plus grands chercheurs. Avides de technologie et de progrès, les cités d’Aphéra s’étendent au-dessus des nuages, créant de fantastiques villes volantes.',
			'desc2' => 'Consciente des bienfaits de la nature, Aphéra vit en harmonie avec son environnement dont elle tire la majorité de ses ressources énergétiques. Développement durable, énergies renouvelables, cette faction fait preuve d’une conscience écologique très importante, préférant la technologie de pointe à une industrie de masse.',
			'desc3' => 'Entrainés depuis leur plus jeune âge, les pilotes aphéréens sont de fins tacticiens et des as du pilotage. Ils font preuve d’une maitrise quasi absolue aux commandes de leurs vaisseaux. Le ciel est leur terrain de jeu.',
			'desc4' => 'N’appréciant pas d’être à l’écart des décisions politiques, les Aphéréens savent faire preuve de poigne lors que cela est nécessaire, déployant des moyens militaires impressionnants pour faire valoir leur idéologie démocratique au travers de toute la Galaxie.',
			'bonus' => [11, 12], 
			'mandateDuration' => 518400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre d\'Aphéra peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Autarque. 
				<br /><br />L\'Autarque va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer l\'Algorithmicien, le Tacticien et l\'Archiviste de la faction. '
		], [
			'id' 			=> 7,
			'officialName'	=> 'Marche de Synelle',
			'popularName' 	=> 'Synelle',
			'government' 	=> 'Fédération Synélectique',
			'demonym' 		=> 'syns',
			'factionPoint' 	=> 'Points de Sagesse',
			'status' 		=> ['Fédéré', 'Conseiller', 'Consul des Finances', 'Consul de la Défense', 'Premier Consul', 'Chancelier'],
			'regime'		=> 1,
			'devise' 		=> 'Au plus loin des Guerres,<br />La vie prend racine',
			'desc1' => 'La fédération Synélectique, basée sur la connaissance et le savoir, est une faction composée de libres penseurs, de diplomates, d’érudits et de philosophes. Préférant une stratégie plus réfléchie à des attaques éclairs, le système militaire de Synelle est puissant et méthodique.',
			'desc2' => 'Le cursus académique et la formation prennent une place extrêmement importante dans le mode de vie des Syns. Vivant selon les principes du Premier Consul, cette société calme et autarcique renferme une très grande partie du patrimoine historique de la Galaxie dans de gigantesques bibliothèques.',
			'desc3' => 'Fière de son indépendance, Synelle la sage dispose d’un important arsenal militaire prêt à défendre ses richesses contre quiconque viendra à les défier. Synelle aime rester en de bons termes avec les différentes alliances peuplant la galaxie, préfèrant la diplomatie à la guerre.',
			'desc4' => 'Cette province, considérée comme le frigo de la galaxie de l’Œil de par son importante production de denrées alimentaires, n’est pas à la pointe de la technologie, préférant vivre simplement en accord avec ses principes de vie.',
			'bonus' => [0, 13, 14],
			'mandateDuration' => 1382400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de Synelle peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Chancelier. 
				<br /><br />Le Chancelier va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Consul de la défense, le Consul des finances et le Premier Consul de la faction. '
		], [
			'id' 			=> 8,
			'officialName'	=> 'Ligue Seldarine',
			'popularName' 	=> 'la Ligue',
			'government' 	=> 'Conseil de la Ligue',
			'demonym' 		=> 'seldarins',
			'factionPoint' 	=> 'Points de Courage',
			'status' 		=> ['Bourgeois', 'Électeur', 'Grand Argentier', 'Champion', 'Licteur', 'Chancelier'],
			'regime'		=> 2,
			'devise' 		=> 'Brisons nos chaînes et marchons libres',
			'desc1' => 'En 260 après Akhéna, l\'Empire a le contrôle de la presque totalité de la galaxie, hormis les quelques territoires occupés par Cardan.<br /><br />Profitant de ce désordre politique et d\'un règne sans force de l\'héritier de l\'impératrice Akhénattia, des industriels et financiers impériaux avec l\'appui de nombreuses maisons mineures, passent à l\'action. Un traité est signé sur la planète Seldare pour affirmer leur indépendance et faire tomber l\'Empereur de son trône : la Ligue Seldarine est créée. Elle n\'aura de cesse de vouloir se substituer au pouvoir en place.',
			'desc2' => 'Esprit très libéraux mais néanmoins attaché à la hiérarchisation de la société, ceux qu\'on appelle communément les Seldarins rêve de devenir les maître d\'un immense Empire où banquiers et entrepreneurs se partageraient le pouvoir, régnant chacun leur tour pour assurer l\'équilibre.',
			'desc3' => 'La ligue peut s\'appuyer sur une économie florissante et d\'énormes moyens de production. Si elle n\'est pas maîtresse dans l\'art de la guerre, elle ne manquera pas de vaisseaux pour s\'entraîner.',
			'desc4' => 'Majoritairement marchands et industriels, leur idéologie repose sur la méritocratie. Les familles noble et bourgeoise cohabitent sans encombre tant que leurs libertés financières et économiques sont garanties.',
			'bonus' => [1, 5],
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'TODO'
		], [
			'id' 			=> 9,
			'officialName'	=> 'Nouvelle Nerve',
			'popularName' 	=> 'Nerve',
			'government' 	=> 'République Nervéenne',
			'demonym' 		=> 'nervéens',
			'factionPoint' 	=> 'Points d\'industrie',
			'status' 		=> ['Citoyen', 'Député', 'Ministre des Finances', 'Ministre de la Défense', 'Premier Ministre', 'Président'],
			'regime'		=> 1,
			'devise' 		=> 'Du fond des gouffres finiront les valses du ciel',
			'desc1' => 'TODO',
			'desc2' => 'TODO',
			'desc3' => 'TODO',
			'desc4' => 'TODO',
			'bonus' => [9, 10],
			'mandateDuration' => 950400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de la Nerve peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Président. 
				<br /><br />Le Président va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Ministre de la défense, le Ministre des finances et le Premier Ministre de la faction. '
		]
	];
}
?>