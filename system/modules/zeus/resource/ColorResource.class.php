<?php
class ColorResource {
	# constants for factions
	const EMPIRE = 1;
	const KOVAHK = 2;
	const NEGORA = 3;
	const CARDAN = 4;
	const NERVE = 5;
	const APHERA = 6;
	const SYNELLE = 7;

	# constants for the actions bonuses of the factions
	const BONUS_EMPIRE_CRUISER = 5;		# price 5% less for cruiser and heavy cruiser
	const BONUS_NEGORA_ROUTE = 3;		# price 3% less for the price of a commercial route
	const BONUS_CARDAN_COLO = 10;		# price 10% less for colo or conquest
	const BONUS_APHERA_TECHNO = 4;		# 4% less time to build technologies

	public static function getInfo($id, $info) {
		if ($id <= count(self::$colors)) {
			if (in_array($info, array(
				'id', 
				'officialName', 
				'popularName', 
				'government', 
				'demonym', 
				'factionPoint', 
				'status', 
				'devise', 
				'desc1', 
				'desc2', 
				'desc3', 
				'desc4', 
				'bonus',
				'mandateDuration',
				'senateDesc',
				'campaignDesc'))) {
				return self::$colors[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function size() {
		return count(self::$colors);
	}

	private static $colors = array(
		array(
			'id' 			=> 1,
			'officialName' 	=> 'Ordre Impérial',
			'popularName' 	=> 'l’Empire',
			'government' 	=> 'Maison Akhénienne',
			'demonym' 		=> 'impériaux',
			'factionPoint' 	=> 'Points de Prestige',
			'status' 		=> array('Noble', 'Dynaste', 'Gardien des Coffres', 'Chef des Armées', 'Intendant', 'Empereur'),
			'devise' 		=> 'Des nefs d’acier,<br />Naquit l’équilibre',
			'desc1' => 'Faction centrale de la Galaxie de l’Œil, l’Ordre Impérial cherche à bâtir un empire puissant, dont les ramifications mèneraient jusqu’aux confins de la Galaxie. Un empire sous l’égide de la Maison Akhénienne, totalitaire et immuable.',
			'desc2' => 'Ordre à la forte puissance militaire, la Maison Akhénienne ne croit qu’en une chose, la suprématie de l’Empereur, seul et unique guide de cette Faction.',
			'desc3' => 'Leur technologie, fiable et robuste, repose sur de longues strates de savoir-faire et sur un base solide d’expérience en matière d’armement et d’aérospatial. Industriellement très développée, l’économie Akhénienne est une machine bien rodée, pouvant allègrement soutenir l’effort de guerre.',
			'desc4' => 'Leurs grandes richesses, principalement acquises sur les plateformes de forage et dans d’énormes concessions minières, leur apportent des ressources de qualité et en grand nombre. Les différents accords, traités et taxes commerciales ainsi que leur situation centrale dans la Galaxie, leur apportent également des revenus stables et constants.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus1-2.png', 'title' => '+ 5% de défense', 'desc' => 'Vos vaisseaux gagnent en défense'),
				array('path' => 'faction/bonus/bonus1-3.png', 'title' => '- 2% prix', 'desc' => 'Les croiseurs et croiseurs-lourds sont moins chers')),
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Akhénien gagne du prestige en construisant des bâtiments et des vaisseaux du chantier de ligne, mais également en conquérant les bases orbitales d\'autres joueurs. Il en perd par contre lorsqu\'il détruit un de ses bâtiments ou qu\'il perd une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' 			=> 2,
			'officialName' 	=> 'Province de Kovahk',
			'popularName' 	=> 'l’Essaim',
			'government' 	=> 'Maison des Kovahkarh',
			'demonym' 		=> 'kovahkarhs',
			'factionPoint' 	=> 'Points de Bataille',
			'status' 		=> array('Guerrier', 'Dynaste', 'Trésorier', 'Conquérant', 'Chambellan', 'Baron'),
			'devise' 		=> 'Eclats de métal dans le ciel',
			'desc1' => 'Fiers sont les soldats Kovahkarh, sans pareil est leur honneur dans la Galaxie de l’Oeil. Kovahk est une faction guerrière aux moeurs parfois brutaux et au caractère bien trempé tel l’acier qu’ils vénèrent et adulent.',
			'desc2' => 'Libérateur des populations oppressées de l’Ancien Empire, Kovahk vénère le pouvoir du métal rarissime et infaillible, un métal qui résisterai à toutes attaques ennemies. Habiles forgerons capable de créer les alliages les plus robustes, l’essaim Kovahkarh étend son influence sur la Galaxie.',
			'desc3' => 'Leur longue tradition de pillage et d’exploration de la Galaxie, leur a amené de très grandes richesses ainsi qu’une habilté hors du commun pour la construction de vaisseaux de combat rapides et puissants.',
			'desc4' => 'Basé sur un système politique très martial et militaire dirigé par un Baron, la société Kovahkarh fait preuve d’une grande discipline et d’une rigueur sans égale. Uni dans un même idéal de victoire et d’expansion, cette faction est une des plus solides de la Galaxie.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus2-2.png', 'title' => '+ 10% de vitesse', 'desc' => 'Vos vaisseaux du Chantier Alpha gagnent en vitesse'),
				array('path' => 'faction/bonus/bonus2-3.png', 'title' => '- 5% de défense', 'desc' => 'Vos vaisseaux du Chantier de Ligne perdent en défense')),
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Kovahkarh gagne du prestige en construisant des vaisseaux, en gagnant des combats et en créant des bases militaires. Il en perd lorsqu\'il se fait tuer au combat ou qu\'il perd une de ces bases militaires.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de Kovahk peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Baron de Kovahk. 
				<br /><br />Le Baron va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la gestion de la Province de Kovahk. Il va devoir déterminer le Financier, le Chef de Guerre et le Ministre de la faction. '),
		array(
			'id' 			=> 3,
			'officialName' 	=> 'Province de Négore',
			'popularName' 	=> 'Négore',
			'government' 	=> 'Maison Négienne',
			'demonym' 		=> 'négiens',
			'factionPoint' 	=> 'Points de Marchandage',
			'status' 		=> array('Commerçant', 'Négociant', 'Financier', 'Stratège', 'Intendant', 'Viziduc'),
			'devise' 		=> 'Toutes les richesses,<br />Passent par Négore',
			'desc1' => 'La maison Négienne, la plus riches de la Galaxie de l’Œil, est composée essentiellement de grands marchands et de financiers expérimentés. Considérée comme la banque de la galaxie depuis très longtemps, son économie est basée sur les échanges et le commerce.',
			'desc2' => 'Parfois peu regardant sur la provenance des marchandises, les Négiens sont d’adroits négociateurs n’hésitant pas à profiter des opportunités qu’offrent la contre-bande, le trafic d’armes ainsi que la vente d’esclaves au détriment des accords et traités commerciaux.',
			'desc3' => 'Dotée de moyens militaires fastueux, la flotte Négienne est rutilante et persuasive, disposant d’une prodigieuse armada de vaisseaux destinés à assurer sa prospérité et son avenir économique dans la Galaxie.',
			'desc4' => 'Vivant dans le luxe et l’opulence, les Vizirs de Négore sont à la tête de fortunes faramineuses permettant les caprices les plus fous; Orgies, casinos, courses et paris sont le quotidien de cette province aux moeurs débridés.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus3-2.png', 'title' => '+ 5% production', 'desc' => 'Vos routes commerciales produisent plus de crédits'),
				array('path' => 'faction/bonus/bonus3-3.png', 'title' => '- 3% prix', 'desc' => 'Vos routes commerciales coûtent moins de crédits')),
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Négien gagne du prestige en créant des routes commerciales, en construisant la Plateforme Commerciale et le Spatioport et en faisant des ventes aux joueurs des autres faction sur le marché. Par contre il en perd lorsqu\'une de ses routes commerciales est détruite.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de Négore peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voies est élu Viziduc de Négore. 
				<br /><br />Le Viziduc va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la gestion de la Province de Négore. Il va devoir déterminer le Financier, le Chef de Guerre et le Ministre de la faction. '),
		array(
			'id' 			=> 4,
			'officialName' 	=> 'Marche de Cardan',
			'popularName' 	=> 'la Marche',
			'government' 	=> 'Eglise Cardanienne',
			'demonym' 		=> 'cardaniens',
			'factionPoint' 	=> 'Points de Foi',
			'status' 		=> array('Fidèle', 'Prêtre', 'Camerlingue', 'Inquisiteur', 'Archiprêtre', 'Guide Suprême'),
			'devise' 		=> 'La lumière vous balaiera',
			'desc1' => 'L’Eglise Cardanienne est la seule faction théocratique de la Galaxie. Elle fût pendant de longues années un mouvement disciminé, peuplé uniquement par des moines et des hommes pieux. Mais des dérives fanatiques ont poussé la Marche de Cardan à devenir une faction belliqueuse et extrémiste, éblouie par un pouvoir suprême et divin qui les mènera à la victoire.',
			'desc2' => 'Les rites prennent une place très importante dans le mode de vie des cardaniens. Nombreux sacrifices et rituels sont faits à chaque Segment en l’honneur des Dieux. Les fidèles se doivent de se plier à la loi cardanienne et respecter les ordres du Guide Suprême.',
			'desc3' => 'Une grande armée de puissants guerriers voués au culte de Cardan, fanatiques experts dans le maniement des armes, les combattants de Cardan n’ont peur que d’une seule chose : ne pas mourir en martyre et décevoir l’Ordre Suprême.',
			'desc4' => 'Ils ne pratiquent que peu de commerce avec les nations étrangères, préférant se suffir à eux-même, même si cela doit aboutir à la famine et à la mort des plus démunis.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus4-2.png', 'title' => '+ 3% de crédits', 'desc' => 'Vos impôts vous amènent plus de crédits'),
				array('path' => 'faction/bonus/bonus4-3.png', 'title' => '- 10% prix', 'desc' => 'Vos colonisations et conquêtes sont moins chères'),
				array('path' => 'faction/bonus/bonus4-4.png', 'title' => '- 5% de ressources', 'desc' => 'Une partie des ressources pillées sont offertes aux Dieux')),
			'mandateDuration' => 1209600,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un membre de Cardan gagne du prestige en colonisant/conquérant une planète se situant en-dehors des territoires de Cardan, en construisant le Chantier Alpha et en faisant un don à sa faction. Il en perd par contre lorsqu\'il perd une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' 			=> 5,
			'officialName' 	=> 'Province de Nerve',
			'popularName' 	=> 'la Nerve',
			'government' 	=> 'République Nervéenne',
			'demonym' 		=> 'nervéens',
			'factionPoint' 	=> 'Points d\'industrie',
			'status' 		=> array('Citoyen', 'Député', 'Ministre des Finances', 'Ministre de la Défense', 'Premier Ministre', 'Président'),
			'devise' 		=> 'Jamais ne tombera,<br />La ville aux Milles Sous-sols',
			'desc1' => 'La république Nervéenne est composée d’une grande communauté préférant vivre à l’écart, cachée dans d’incroyables labyrinthes sous-terrain. Elle est connue principalement pour sa capacité à camoufler la quasi-totalité de ses infrastructures à ses ennemis ainsi que pour ses qualités de bâtisseurs hors-normes.',
			'desc2' => 'Communauté soudée autour de son Président, la Nerve est une faction de grands travailleurs et de bâtisseurs parmi les plus fameux de toute la Galaxie. Ils réalisent des édifices enfouis d’une finesse et d’une complexité incroyable.',
			'desc3' => 'N’ayant pas une grande connaissance de l’art de la guerre, mais contraints de lutter pour la préservation de leur mode de vie, ils se sont adaptés et ont formé de redoutables forteresses défensives.',
			'desc4' => 'Grâce à un dédale de culture hydroponique et une grande connaissance en agro-alchimie mais également en extraction de minerai, l’industriel Nervéen produit une grande quantité de ressources attirant ainsi les marchands les plus riches de la galaxie, leur permettant ainsi un essor prospère.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus5-2.png', 'title' => '+ 4% production', 'desc' => 'Votre Raffinerie produit plus de ressources'),
				array('path' => 'faction/bonus/bonus5-3.png', 'title' => '+ 4% stockage', 'desc' => 'Votre Raffinerie stocke plus de ressources')),
			'mandateDuration' => 950400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Nervéen gagne du prestige en créant des Centres Industriels, en construisant la Raffinerie et le Stockage et en prenant des planètes (proportionnel au coefficient ressource de la planète). Il en perd en détruisant un Centre Industriel et en perdant le contrôle d\'une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' 			=> 6,
			'officialName' 	=> 'Province d’Aphéra',
			'popularName' 	=> 'Aphéra',
			'government' 	=> 'République Aphéréenne',
			'demonym' 		=> 'aphéréens',
			'factionPoint' 	=> 'Points de Technologie',
			'status' 		=> array('Citoyen', 'Technocrate', 'Algorithmicien', 'Tacticien', 'Archiviste', 'Autarque'),
			'devise' 		=> 'Au travers du vide,<br />Nos oiseaux perçents,<br/>Levez les yeux',
			'desc1' => 'La république d’Aphéra, réputée pour son potentiel scientifique, est une faction composée des plus grands chercheurs. Avides de technologie et de progrès, les citées d’Aphéra s’étendent au dessus des nuages, créant de fantastiques villes volantes.',
			'desc2' => 'Consciente des bienfaits de la nature, Aphéra vit en harmonie avec son environnement dont elle tire la majorité de ses ressources énergétiques. Développement durable, énergies renouvelables, cette faction fait preuve d’une conscience écologique très importante, préférant la technologie de pointe à une industrie de masse.',
			'desc3' => 'Entrainés depuis leur plus jeune âge, les pilotes aphéréens sont de fins tacticiens et des as du pilotage, faisant preuve d’une maitrise quasi absolue aux commandes de leurs vaisseaux, le ciel est leur terrain de jeu.',
			'desc4' => 'N’appréciant pas d’être à l’écart des décisions politiques, les aphéréens savent faire preuve de poigne lors que cela est nécessaire, déployant des moyens militaires impressionnants pour faire valoir leur idéologie démocratique au travers de toute la Galaxie.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus6-2.png', 'title' => '+ 4% efficacité', 'desc' => 'Votre Université est plus efficace'),
				array('path' => 'faction/bonus/bonus6-3.png', 'title' => '- 4% temps', 'desc' => 'Vos technologies se développent plus rapidement')),
			'mandateDuration' => 518400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Aphéréen gagne du prestige en menant à terme une Recherche, en développant une Technologie et en construisant la Technosphère.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' 			=> 7,
			'officialName'	=> 'Marche de Synelle',
			'popularName' 	=> 'Synelle',
			'government' 	=> 'Fédération Synélectique',
			'demonym' 		=> 'syns',
			'factionPoint' 	=> 'Points de Sagesse',
			'status' 		=> array('Fédéré', 'Conseiller', 'Consul des Finances', 'Consul de la Défense', 'Premier Consul', 'Chancelier'),
			'devise' 		=> 'Au plus loin des Guerres,<br />La vie prends racine',
			'desc1' => 'La fédération Synélectique, basée sur la connaissance et le savoir, est une faction composée de libres penseurs, de diplomates, d’érudits et de philosophes. Préférant une stratégie plus réfléchie à des attaques éclaires, le système militaire de Synelle est puissant et méthodique.',
			'desc2' => 'Le cursus académique et la formation prennent une place extrêmement importante dans le mode de vie des Syns. Vivant selon les principes du Premier Consul, cette société calme et autarcique renferme une très grande partie du patrimoine historique de la Galaxie dans de gigantesques bibliothèques.',
			'desc3' => 'Fière de son indépendance, Synelle la sage dispose d’un important arsenal militaire prêt à défendre ses richesses contre quiconque viendra à les défier. Synelle aime rester en de bons termes avec les différentes alliances peuplant la galaxie, préfèrant la diplomatie à la guerre.',
			'desc4' => 'Cette province, considérée comme le frigo de la galaxie de l’Œil de par son importante production de denrées alimentaires, n’est pas à la pointe de la technologie, préférant vivre simplement en accord avec ses principes de vie.',
			'bonus' => array(
				array('path' => 'faction/bonus/bonus1-2.png', 'title' => '+ 5% de défense', 'desc' => 'Vos vaisseaux gagnent en défense'),
				array('path' => 'faction/bonus/bonus7-2.png', 'title' => '+ 6% efficacité', 'desc' => 'Votre Ecole de Commandement est plus efficace'),
				array('path' => 'faction/bonus/bonus7-3.png', 'title' => '+ 2% efficacité', 'desc' => 'Votre Université est plus efficace')),
			'mandateDuration' => 1382400,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un membre de Synelle gagne du prestige en étant victorieux lors de la défense d\'une de ses planètes et en construisant le Centre de Recyclage et le Générateur.',
			'campaignDesc' => 'explicatif d\'une campagne')
	);
}
?>