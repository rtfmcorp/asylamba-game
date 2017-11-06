<?php

namespace Asylamba\Modules\Demeter\Resource;

class ColorResource
{
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
    const BONUS_EMPIRE_CRUISER = 5;        # price 5% less for cruiser and heavy cruiser
    const BONUS_NEGORA_ROUTE = 3;        # price 3% less for the price of a commercial route
    const BONUS_CARDAN_COLO = 10;        # price 10% less for colo or conquest
    const BONUS_APHERA_TECHNO = 2;        # 4% less time to build technologies

    public static function getInfo($id, $info)
    {
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
                'situation',
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
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getBonus($i)
    {
        return self::$bonus[$i];
    }

    public static function size()
    {
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
            'id'            => 0,
            'officialName'    => 'Sans Faction',
            'popularName'    => '',
            'government'    => '',
            'demonym'        => 'Sans Faction',
            'factionPoint'    => '',
            'status'        => ['', '', '', '', '', ''],
            'regime'        => 1,
            'devise'        => '',
            'desc1' => '',
            'desc2' => '',
            'desc3' => '',
            'desc4' => '',
            'bonus' => [],
            'mandateDuration' => 0,
            'senateDesc' => '',
            'campaignDesc' => ''
    ], [
            'id'            => 1,
            'officialName'    => 'Ordre Impérial',
            'popularName'    => 'l’Empire',
            'government'    => 'Maison Akhénienne',
            'demonym'        => 'impériaux',
            'factionPoint'    => 'Points de Prestige',
            'status'        => ['Noble', 'Dynaste', 'Gardien des Coffres', 'Maréchal', 'Intendant', 'Empereur'],
            'regime'        => 2,
            'devise'        => 'Des nefs d’acier,<br />Naquit l’équilibre',
            'situation'     => 'Fondé il y a moins d’une strate, et malgré son expansion, l\'Empire voit planer un nuage noir sur son avenir à cause des morts successives d’Akhéna, le premier empereur, et de son fils, Hakagamon.',
            'desc1' => 'Faction centrale de la Galaxie de l’Œil, l’Ordre Impérial cherche à bâtir un empire puissant, dont les ramifications mèneraient jusqu’aux confins de la Galaxie. Un empire sous l’égide de la Maison Akhénienne, totalitaire et immuable.',
            'desc2' => 'Ordre à la forte puissance militaire, la Maison Akhénienne ne croit qu’en une chose, la suprématie de l’Empereur, seul et unique guide de cette Faction.',
            'desc3' => 'Leur technologie, fiable et robuste, repose sur de longues strates de savoir-faire et sur une base solide d’expérience en matière d’armement et d’aérospatial. Industriellement très développée, l’économie Akhénienne est une machine bien rodée, pouvant allègrement soutenir l’effort de guerre.',
            'desc4' => 'Leurs grandes richesses, principalement acquises sur les plateformes de forage et dans d’énormes concessions minières, leur apportent des ressources de qualité et en grand nombre. Les différents accords, traités et taxes commerciales ainsi que leur situation centrale dans la Galaxie, leur apportent également des revenus stables et constants.',
//            'desc1' => 'Quand Akhena déclarait devant ses sénateurs : “Des nefs d’acier, naquit l’équilibre” avait-il pensé qu’un jour ces même sénateurs ferait de son empire la plus puissante démocratie dans la galaxie ? Avec ses neuf-cents segments d\'existence l’Empire a beaucoup changé. Influencé par ses vassaux démocratiques, le sénat a adopté récemment une réforme profonde du système politique impérial. L’empereur, désormais élu par le peuple, assure protection et justice à ses sujets. Le meilleur partage des richesses de la galaxie a nettement augmenté le niveau de vie de la population.',
//            'desc2' => 'L’empire est un état pétrit de contradiction, héritier d’une tradition de domination des populations sur son territoire, il se proclame désormais héraut de la Liberté et la justice dans la galaxie. Mais il sait aussi que l’ancienne oligarchie, balayée par les dernières réformes du sénat, ne veulent pas de sa parole et qu’elle chercheront par tous les moyens à faire taire ce message d’espoir.',
//            'desc3' => 'Depuis que les provinces de Kovahk et leurs alliés ont fait sécession l’empire se prépare à une attaque imminente et prépare son arsenal en conséquence. L’empire refuse également de voir ses provinces périphérique prendre leur indépendance pour tomber dans des régimes rétrogrades.',
//            'desc4' => 'Ayant conservé les planètes les plus prospère et les plateforme commerciale les plus florissante, l’empire est la plus grande puissance économique de la galaxie. Leurs diverses concessions leur apportent des ressources de qualités et en grand nombre. Les différents accords, traités et taxes commerciales ainsi que leur situation centrale dans la Galaxie, leur apportent également des revenus stables et constants.',
            'bonus' => [0, 1],
            'mandateDuration' => 604800,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
    ], [
            'id'            => 2,
            'officialName'    => 'Empire de Kovahk',
            'popularName'    => 'l’Essaim',
            'government'    => 'Maison des Kovahkarh',
            'demonym'        => 'kovahkarhs',
            'factionPoint'    => 'Points de Bataille',
            'status'        => ['Guerrier', 'Dignitaire', 'Trésorier', 'Maréchal', 'Chambellan', 'Empereur'],
            'regime'        => 2,
            'devise'        => 'Eclats de métal dans le ciel',
            /* 'desc1' => 'Fiers sont les soldats Kovahkarh, sans pareil est leur honneur dans la Galaxie de l’Oeil. Kovahk est une faction guerrière aux moeurs parfois brutales et au caractère bien trempé tel l’acier qu’ils vénèrent et adulent.',
            'desc2' => 'Libérateur des populations oppressées de l’Ancien Empire, Kovahk vénère le pouvoir du métal rarissime et infaillible, un métal qui résisterai à toutes attaques ennemies. Habiles forgerons capables de créer les alliages les plus robustes, l’essaim Kovahkarh étend son influence sur la Galaxie.',
            'desc3' => 'Leur longue tradition de pillage et d’exploration de la Galaxie, leur a amené de très grandes richesses ainsi qu’une habilté hors du commun pour la construction de vaisseaux de combat rapides et puissants.',
            'desc4' => 'Basé sur un système politique très martial et militaire dirigé par un Baron, la société Kovahkarh fait preuve d’une grande discipline et d’une rigueur sans égale. Unie dans un même idéal de victoire et d’expansion, cette faction est une des plus solides de la Galaxie.', */
            'desc1' => 'La Maison des kovahkarhs, fondatrice de l’empire éponyme, a longtemps été à la tête de l’armée Akhénienne. Famille de talentueux amiraux, le pouvoir impérial lui doit ses plus grandes conquêtes. Mais les dernières réformes de l’empire ont profondément outrées les élites kovahkarhes. Elles les jugent contraires aux valeurs historiques de l’Empire. Alors que le sénat impérial tombait entre les mains des démocraties vassales, les anciens hauts dignitaires et la haute noblesse Akhénienne reconnaissaient dans le baron de Kovahk l’empereur légitime de la galaxie.',
            'desc2' => 'Kovahk vénère le pouvoir du métal rarissime et infaillible, un métal qui résisterait à toute attaque ennemie. Composé d\'habiles forgerons capables de créer les alliages les plus robustes et de redoutables militaires, l’Empire kovahkarh étend son influence sur la Galaxie.',
            'desc3' => 'Leur longue tradition de pillage et d’exploration de la Galaxie leur a amené de très grandes richesses ainsi qu’une habileté hors du commun pour la construction de vaisseaux de combat rapides et puissants. Elle a les moyens de produire un grand nombre de forces spatiales. Portée sur un combat en essaim, d\'où son surnom, de milliers de chasseurs et de corvettes, elle peut dévaster un système solaire en quelques jours... puis repartir.',
            'desc4' => 'Basée sur système politique très martial et militaire dirigé les anciens généraux de l’Empire Akhénien, la société kovahkarh fait preuve d’une grande discipline et d’une rigueur sans égal. Unie dans un même idéal de restauration du régime Impérial, cette faction est une des plus solide de la Galaxie.',
            'bonus' => [2, 3],
            'mandateDuration' => 604800,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.',
    ], [
            'id'            => 3,
            'officialName'    => 'Maison de Négore',
            'popularName'    => 'Négore',
            'government'    => 'Maison Négienne',
            'demonym'        => 'négiens',
            'factionPoint'    => 'Points de Marchandage',
            'status'        => ['Commerçant', 'Mécène', 'Grand Argentier', 'Condottiere', 'Intendant', 'Doge'],
            'regime'        => 2,
            'devise'        => 'Toutes les richesses,<br />Passent par Négore',
            'situation'     => 'Grâce au traité de Morgania, et en échange de son rattachement à l\'Empire, Négore a gardé ses privilèges économiques. La situation troublée pourrait lui permettre d\'amasser plus de richesses.',
            // old description
            'desc1' => 'La maison Négienne, la plus riche de la Galaxie de l’Œil, est composée essentiellement de grands marchands et de financiers expérimentés. Considérée comme la banque de la galaxie depuis très longtemps, son économie est basée sur les échanges et le commerce.',
            'desc2' => 'Parfois peu regardant sur la provenance des marchandises, les Négiens sont d’adroits négociateurs n’hésitant pas à profiter des opportunités qu’offrent la contre-bande, le trafic d’armes ainsi que la vente d’esclaves au détriment des accords et traités commerciaux.',
            'desc3' => 'Dotée de moyens militaires fastueux, la flotte Négienne est rutilante et dissuasive, disposant d’une prodigieuse armada de vaisseaux destinés à assurer sa prospérité et son avenir économique dans la Galaxie.',
            'desc4' => 'Vivant dans le luxe et l’opulence, les Vizirs de Négore sont à la tête de fortunes faramineuses permettant les caprices les plus fous; Orgies, casinos, courses et paris sont le quotidien de cette province aux moeurs débridés.',
            // new description
//            'desc1' => 'La Maison Négienne, est depuis sa fondation le plus riche état de la Galaxie de l’Œil. Elle est composée essentiellement de grands marchands et de financiers expérimentés. Considérée comme la banque de la galaxie depuis très longtemps, son économie est basée sur les échanges et le commerce. Durant les guerres d’influences, elle a financé les démocraties dans le but d’affaiblir sa grande rivale la Maison de Kovahk. Néanmoins elle a été prise de court quand le Sénat impérial a adopté la réforme qui a fait de l’empire une république.',
//            'desc2' => 'Parfois peu regardant sur la provenance des marchandises, les Négiens sont d’adroits négociateurs n’hésitant pas à profiter des opportunités qu’offrent la contre-bande, le trafic d’armes ainsi que la vente d’esclaves au détriment des accords et traités commerciaux.',
//            'desc3' => 'Dotée de moyens militaires fastueux, la flotte Négienne est rutilante et dissuasive, disposant d’une prodigieuse armada de vaisseaux destinés à assurer sa prospérité et son avenir économique dans la Galaxie.',
//            'desc4' => 'Vivant dans le luxe et l’opulence, les Vizirs de Négore sont à la tête de fortunes faramineuses permettant les caprices les plus fous; Orgies, casinos, courses et paris sont le quotidien de cette province aux moeurs débridés.',

            'bonus' => [4, 5],
            'mandateDuration' => 604800,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
    ], [
            'id'            => 4,
            'officialName'    => 'Marche de Cardan',
            'popularName'    => 'Cardan',
            'government'    => 'Eglise Cardanienne',
            'demonym'        => 'cardaniens',
            'factionPoint'    => 'Points de Foi',
            'status'        => ['Fidèle', 'Prêtre', 'Camerlingue', 'Inquisiteur', 'Archiprêtre', 'Grand Maître'],
            'regime'        => 3,
            'devise'        => 'La lumière vous balaiera',
            'situation'     => 'Seule région de la galaxie à ne pas avoir été rattaché à l’Empire, Cardan a dû se battre pour préserver son indépendance. Les Dieux sauront-ils guider la Marche vers un destin glorieux ?',
            'desc1' => 'L’Eglise Cardanienne est la seule faction théocratique de la Galaxie. Elle fût pendant de longues années un mouvement discriminé, peuplé uniquement par des moines et des hommes pieux. Mais des dérives fanatiques ont poussé la Marche de Cardan à devenir une faction belliqueuse et extrémiste, éblouie par un pouvoir suprême et divin qui les mènera à la victoire.',
            'desc2' => 'Les rites prennent une place très importante dans le mode de vie des Cardaniens. Nombreux sacrifices et rituels sont faits à chaque Segment en l’honneur des Dieux. Les fidèles se doivent de se plier à la loi cardanienne et respecter les ordres du Guide Suprême.',
            'desc3' => 'Une grande armée de puissants guerriers voués au culte de Cardan, fanatiques experts dans le maniement des armes, les combattants de Cardan n’ont peur que d’une seule chose : ne pas mourir en martyre et décevoir l’Ordre Suprême.',
            'desc4' => 'Ils ne pratiquent que peu de commerce avec les nations étrangères, préférant se suffir à eux-même, même si cela doit aboutir à la famine et à la mort des plus démunis.',
//            'desc1' => 'Jamais les forces armées ne peuvent avoir raison de la culture et des traditions d’un peuple. Bien qu\'occupant les saintes terres, aucun des héritiers d\'Akhéna, le premier empereur, n’a réussi à effacer le souvenir glorieux de la Marche de Cardan. C’est ainsi que lorsque le joug de l’Empire s’est enfin affaibli, les fidèles de Cardan et les disciples de Magoth ont uni leurs forces pour acquérir leur indépendance. Enfin réunis autour d’un objectif commun, les frères d’antan, sous le nom de la Confrérie d’Ahriman, dieu suprême du panthéon cardanien, entendent imposer leurs volonté sur les terres qu’ils ont perdu, puis faire plier la galaxie toute entière.',
//            'desc2' => 'Interdits sous l’occupation impériale, les rites cardaniens sont de nouveau suivis dans les Cardamines. De nombreux sacrifices et rituels sont faits à chaque segment en l’honneur des Dieux. Les fidèles se doivent de se plier à la loi cardanienne et respecter la volonté divine.',
//            'desc3' => 'En quelques segments, la Confrérie a réussi à rassembler une puissante flotte de guerre. Héritiers d’une antique tradition militaire, les guerriers voués au culte de Karn, dieu du fer et des trous noirs, et suivants les enseignement de Magoth sont des fanatiques experts dans le maniement des armes et des vaisseaux de guerre. Ils ne vivent que pour une chose, se sacrifier aux dieux et mourir en martyres.',
//            'desc4' => 'Si elle n’a pas réussi à effacer les traditions cardaniennes, l’occupation impériale a néanmoins provoqué l’ouverture économique de la Marche. Désormais devenue la Confrérie, elle peut s’appuyer sur une industrie remarquable et sur de nombreux fidèles, des marchands, des ouvriers et des militaires, bien formés et unis dans la vénération d’Ahriman.',
            'bonus' => [6, 7, 8],
            'mandateDuration' => 1209600,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se proposer devant les Oracles pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, la Grande Lumière, via les Oracles décide de la personne la plus apte à la représenter dans le monde physique. Cet personne reçoit le titre de Guide Suprême. 
				<br /><br />Le Guide Suprême va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Camerlingue, l\'Inquisiteur et l\'Archiprêtre de la faction. '
        ], [
            'id'            => 5,
            'officialName'    => 'Province de Nerve',
            'popularName'    => 'la Nerve',
            'government'    => 'République Nervéenne',
            'demonym'        => 'nervéens',
            'factionPoint'    => 'Points d\'industrie',
            'status'        => ['Citoyen', 'Député', 'Ministre des Finances', 'Ministre de la Défense', 'Premier Ministre', 'Président'],
            'regime'        => 1,
            'devise'        => 'Jamais ne tombera,<br />La ville aux Mille Sous-sols',
            'situation'     => "Récemment annexée par l’Empire, la Nerve a pu préserver son système démocratique. Néanmoins, l’absence d’un souverain fort à la tête de l’Empire pourrait lui donner des envies de grandeur.",
            'desc1' => 'La république Nervéenne est composée d’une grande communauté préférant vivre à l’écart, cachée dans d’incroyables labyrinthes souterrains. Elle est connue principalement pour sa capacité à camoufler la quasi-totalité de ses infrastructures à ses ennemis ainsi que pour ses qualités de bâtisseurs hors-normes.',
            'desc2' => 'Communauté soudée autour de son Président, la Nerve est une faction de grands travailleurs et de bâtisseurs parmi les plus fameux de toute la Galaxie. Ils réalisent des édifices enfouis d’une finesse et d’une complexité incroyable.',
            'desc3' => 'N’ayant pas une grande connaissance de l’art de la guerre, mais contraints de lutter pour la préservation de leur mode de vie, ils se sont adaptés et ont formé de redoutables forteresses défensives.',
            'desc4' => 'Grâce à un dédale de culture hydroponique et une grande connaissance en agro-alchimie mais également en extraction de minerai, l’industriel Nervéen produit une grande quantité de ressources attirant ainsi les marchands les plus riches de la galaxie, leur permettant ainsi un essor prospère.',
            'bonus' => [9, 10],
            'mandateDuration' => 950400,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de la Nerve peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voix est élue Président. 
				<br /><br />Le Président va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Ministre de la défense, le Ministre des finances et le Premier Ministre de la faction. '
        ], [
            'id'            => 6,
            'officialName'    => 'Province d’Aphéra',
            'popularName'    => 'Aphéra',
            'government'    => 'République Aphéréenne',
            'demonym'        => 'aphéréens',
            'factionPoint'    => 'Points de Technologie',
            'status'        => ['Citoyen', 'Technocrate', 'Algorithmicien', 'Tacticien', 'Archiviste', 'Autarque'],
            'regime'        => 1,
            'devise'        => 'Au travers du vide,<br />Nos oiseaux percent,<br/>Levez les yeux',
            'desc1' => 'La république d’Aphéra, réputée pour son potentiel scientifique, est une faction composée des plus grands chercheurs. Avides de technologie et de progrès, les cités d’Aphéra s’étendent au-dessus des nuages, créant de fantastiques villes volantes.',
            'desc2' => 'Consciente des bienfaits de la nature, Aphéra vit en harmonie avec son environnement dont elle tire la majorité de ses ressources énergétiques. Développement durable, énergies renouvelables, cette faction fait preuve d’une conscience écologique très importante, préférant la technologie de pointe à une industrie de masse.',
            'desc3' => 'Entrainés depuis leur plus jeune âge, les pilotes aphéréens sont de fins tacticiens et des as du pilotage. Ils font preuve d’une maitrise quasi absolue aux commandes de leurs vaisseaux. Le ciel est leur terrain de jeu.',
            'desc4' => 'N’appréciant pas d’être à l’écart des décisions politiques, les Aphéréens savent faire preuve de poigne lors que cela est nécessaire, déployant des moyens militaires impressionnants pour faire valoir leur idéologie démocratique au travers de toute la Galaxie.',
            'bonus' => [11, 12],
            'mandateDuration' => 518400,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre d\'Aphéra peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voix est élue Autarque. 
				<br /><br />L\'Autarque va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer l\'Algorithmicien, le Tacticien et l\'Archiviste de la faction. '
        ], [
            'id'            => 7,
            'officialName'    => 'Marche de Synelle',
            'popularName'    => 'Synelle',
            'government'    => 'Fédération Synélectique',
            'demonym'        => 'syns',
            'factionPoint'    => 'Points de Sagesse',
            'status'        => ['Fédéré', 'Conseiller', 'Consul des Finances', 'Consul de la Défense', 'Premier Consul', 'Chancelier'],
            'regime'        => 1,
            'devise'        => 'Au plus loin des Guerres,<br />La vie prend racine',
            /*
            'desc1' => 'La fédération Synélectique, basée sur la connaissance et le savoir, est une faction composée de libres penseurs, de diplomates, d’érudits et de philosophes. Préférant une stratégie plus réfléchie à des attaques éclairs, le système militaire de Synelle est puissant et méthodique.',
            'desc2' => 'Le cursus académique et la formation prennent une place extrêmement importante dans le mode de vie des Syns. Vivant selon les principes du Premier Consul, cette société calme et autarcique renferme une très grande partie du patrimoine historique de la Galaxie dans de gigantesques bibliothèques.',
            'desc3' => 'Fière de son indépendance, Synelle la sage dispose d’un important arsenal militaire prêt à défendre ses richesses contre quiconque viendra à les défier. Synelle aime rester en de bons termes avec les différentes alliances peuplant la galaxie, préfèrant la diplomatie à la guerre.',
            'desc4' => 'Cette province, considérée comme le frigo de la galaxie de l’Œil de par son importante production de denrées alimentaires, n’est pas à la pointe de la technologie, préférant vivre simplement en accord avec ses principes de vie.', */
            'desc1' => 'La fédération Synélectique, basée sur la connaissance et le savoir, est une faction composée de libres penseurs, de diplomates, d’érudits et de philosophes. Préférant une stratégie plus réfléchie à des attaques éclairs, le système militaire de Synelle est puissant et méthodique. Grâce à l’influence que son statut de république vassale lui procurait, elle est à la base des récentes réformes qui ont fait de l’Empire une démocratie.',
            'desc2' => 'La société Syn a connu de nombreuses mutation depuis sa fondation en SEG. Elle qui fut une société calme et autarcique, Synelle est désormais le cœur cosmopolite de la galaxie.',
            'desc3' => 'En tant que principal allié de l’empire Akhénien, Synelle la Sage ne peut se passer d’un important arsenal militaire, construit dans l’éventualité d’une confrontation avec les ennemis de la démocratie. Bien qu’elle partage un passé commun avec Négore, Synelle constate que les dernières réformes qu’elle a mené dans l’empire déplaisent au Doge. Elle devine, désormais, qu’elle ne peut plus compter sur le soutien indéfectible de Négore.',
            'desc4' => 'Consciente des guerres qu’a causé les élans nationalistes de ses élites lors des guerres d’influence, Synelle s’est depuis repentie. Elle souhaite redevenir aux yeux de tous la faction incarnant la paix galactique préférant la diplomatie et le commerce à a l’art militaire.',
            'bonus' => [0, 13, 14],
            'mandateDuration' => 1382400,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de Synelle peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voix est élue Chancelier. 
				<br /><br />Le Chancelier va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Consul de la défense, le Consul des finances et le Premier Consul de la faction. '
        ], [
            'id'            => 8,
            'officialName'    => 'Ligue Seldarine',
            'popularName'    => 'la Ligue',
            'government'    => 'Conseil de la Ligue',
            'demonym'        => 'seldarins',
            'factionPoint'    => 'Points de Courage',
            'status'        => ['Bourgeois', 'Électeur', 'Grand Argentier', 'Champion', 'Licteur', 'Chancelier'],
            'regime'        => 2,
            'devise'        => 'Brisons nos chaînes et marchons libres',
            'desc1' => 'En 260 après Akhéna, l\'Empire a le contrôle de la presque totalité de la galaxie, hormis les quelques territoires occupés par Cardan.<br /><br />Profitant de ce désordre politique et d\'un règne sans force de l\'héritier de l\'impératrice Akhénattia, des industriels et financiers impériaux avec l\'appui de nombreuses maisons mineures, passent à l\'action. Un traité est signé sur la planète Seldare pour affirmer leur indépendance et faire tomber l\'Empereur de son trône : la Ligue Seldarine est créée. Elle n\'aura de cesse de vouloir se substituer au pouvoir en place.',
            'desc2' => 'Esprit très libéraux mais néanmoins attaché à la hiérarchisation de la société, ceux qu\'on appelle communément les Seldarins rêve de devenir les maître d\'un immense Empire où banquiers et entrepreneurs se partageraient le pouvoir, régnant chacun leur tour pour assurer l\'équilibre.',
            'desc3' => 'La ligue peut s\'appuyer sur une économie florissante et d\'énormes moyens de production. Si elle n\'est pas maîtresse dans l\'art de la guerre, elle ne manquera pas de vaisseaux pour s\'entraîner.',
            'desc4' => 'Majoritairement marchands et industriels, leur idéologie repose sur la méritocratie. Les familles noble et bourgeoise cohabitent sans encombre tant que leurs libertés financières et économiques sont garanties.',
            'bonus' => [1, 5],
            'mandateDuration' => 604800,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
        ], [
            'id'            => 9,
            'officialName'    => 'Nouvelle-Nerve',
            'popularName'    => 'la Nouvelle-Nerve',
            'government'    => 'République Nervéenne',
            'demonym'        => 'néo-nervéens',
            'factionPoint'    => 'Points d\'industrie',
            'status'        => ['Citoyen', 'Député', 'Ministre des Finances', 'Ministre de la Défense', 'Premier Ministre', 'Président'],
            'regime'        => 1,
            'devise'        => 'Au fond des gouffres,<br />finiront les valses du ciel',
            'desc1' => 'Décimés puis déportés en 27 par la Grande Amirale Alba Valseciel, les Nervéens ont toujours été nourri d’un sentiment patriote très fort. Grâce à leurs efforts conjoints, ils achevèrent la restauration de la citadelle aux mille sous-sols en 75. Militant avec vigueur pour plus d’indépendance, Nerve obtient, enfin, en 89, un statut d’autonomie. Elle jouit alors d’une grande liberté sous Valerio et prospéra. Mais le règne sévère d’Akhénattia vint lui retirer de nombreux privilèges ce qui ternit à nouveau les relations avec l’Empire duquel les Nervéens s’étaient pourtant rapprochés au fil des ans. Lorsqu’Eris monta sur le trône et mena son règne sans autorité, les Nervéens firent sécession et déclarèrent leur indépendance en temps que Nouvelle Nerve.',
            'desc2' => 'Les Néo-Nervéens sont très fiers. Attachés à leur liberté et à la démocratie comme nul autre, ils méprisent la grande majorité des peuples de la galaxie. Qui plus est, ils vouent une haine dévorante à la Maison Valseciel, responsable de la chute de la Première Nerve.',
            'desc3' => 'Les Néo-Nervéens sont emprunt d’un tel amour pour leur Mère Patrie, qu’ils sont considérés comme des soldats sans peur et téméraires. Contrairement à leurs ancêtres, leur stratégie n’est plus uniquement basée sur la défense de forteresses. En effet, ils ne rechignent pas à prendre les devants et à combattre en orbite. Cependant, la force de la Nouvelle Nerve repose sur un savoir faire ancestrale et sur des matériaux de qualité. Les sous-sols des mondes nervéens sont extrêmement riches et consistent en leur plus précieux atout.',
            'desc4' => 'Tous frères dans la République, les Néo-Nervéens sont comme leurs prédécesseurs, amoureux des arts, de l’ingénierie et des matières nobles. Les monuments les plus fantastiques de la galaxie sont souvent l’ouvrage d’architectes et d’artistes de Nerve. Il faut dire que la citadelle est un chef d’oeuvre en soi, bien que les stigmates des Valseciel soient encore par endroit visibles.',
            'bonus' => [9, 10],
            'mandateDuration' => 950400,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de la Nouvelle-Nerve peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voix est élue Président. 
				<br /><br />Le Président va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Ministre de la défense, le Ministre des finances et le Premier Ministre de la faction. '
        ], [
            'id'            => 10,
            'officialName'    => 'Culte Magothique',
            'popularName'    => 'le Culte',
            'government'    => 'Assemblée de Magoth',
            'demonym'        => 'magoths',
            'factionPoint'    => 'Points d\'industrie',
            'status'        => ['Disciple', 'Oracle', 'Gardien des coffres', 'Chef inquisiteur', 'Patriarche', 'Grand dignitaire'],
            'regime'        => 3,
            'devise'        => 'Point de vraie foi en dehors du sang et des larmes',
            'desc1' => 'Les Magothistes, persuadés de posséder la vraie foi se fondent sur la parole de Magoth uniquement. Celle-ci demande un maximum de sacrifices et de souffrance. Ils sont prêts à tout pour que la Galaxie voue un culte aux vrais Dieux. Seuls les élus et les personnes baptisées exclusivement par le triumvirat religieux peuvent prétendre à la vie. Leur extrémisme et leur vision bornée de la foi ont fait qu’ils ont été banni du coeur de Cardan.',
            'desc2' => 'Les Magothistes possèdent la seule et unique vérité, la parole de Magoth. Leur existence est tourné vers ces préceptes qui excluent évidemment la tolérance envers les non-croyants. Les hérétiques aiment dire que la vie d’un Magothiste est ponctuée de prières, de purification, de châtiments corporels et d’expiation de pêchés. A cela s’ajoutent la méditation, la recherche du geste parfait, la conscience aiguë du partage entre initiés, le don de soi et l’amour inconditionnel de son prochain. C’est pour cette dernière raison qu’il est primordial que les Magothistes fassent comprendre à la galaxie l’erreur dans laquelle elle s’embourbe. Pour le bien de l’humanité, il est de leur devoir divin de délivrer les populations des hérétiques afin de les ramener dans la lumière et sauver leurs âmes.',
            'desc3' => 'Il serait une erreur de penser que les Magothistes se contentent de foncer sur leurs ennemis sans réfléchir. Leur stratégie inclut le sacrifice des troupes mais seulement en cas de nécessité. Leur principal objectif est de couper les têtes des gouverneurs des planètes mais d’épargner les populations afin de les sauver en les convertissant à la parole de Magoth. Ainsi, leur armée est faite des guerriers les plus courageux et sans peur qu’on puisse trouver dans la galaxie, mais est aussi composée d’une logistique à toute épreuve pour s’occuper des civils.',
            'desc4' => 'Que ferait un magoth sans sa cinquantaine d’icônes ? Il est certain que leur dévotion nécessite le support de ces oeuvres sublimes. Le titre de peintre d’icône est réservé à quelques élus seulement qui, de ce fait, sont admirés et bénis par le reste des Magothistes. Nul autre personne n’a le droit d’en peindre sans la bénédiction du clergé. L’écriture des textes sacrés est aussi relayée au rang d’art. Les enluminures sont aussi précises que finement décorées. Enfin, la musique est au centre des méditations. Chaque magoth connaît plusieurs chants religieux qui l\'amèneront dans un état de transe nécessaire pour entendre la voix des Dieux. ',
            'bonus' => [8, 2, 6],
            'mandateDuration' => 1209600,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se proposer devant les Oracles pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, la Grande Lumière, via les Oracles décide de la personne la plus apte à la représenter dans le monde physique. Cet personne reçoit le titre de Guide Suprême. 
				<br /><br />Le Guide Suprême va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Camerlingue, l\'Inquisiteur et l\'Archiprêtre de la faction. '
        ], [
            'id'            => 11,
            'officialName'    => 'Néo-Humaniste',
            'popularName'    => 'Néo-Humaniste',
            'government'    => 'Nouvelle Eglise Cardanienne',
            'demonym'        => 'néo-humanistes',
            'factionPoint'    => 'Points d\'industrie',
            'status'        => ['Éveillé', 'Philosophe', 'Drapier', 'Grand Général', 'Sénéchal', 'Primat'],
            'regime'        => 1,
            'devise'        => 'Le vrai chemin est celui de la mesure et de la subtilité',
            'desc1' => 'Les Néo-Humanistes, majoritairement issus des descendants des Néo-Nervéens vivant sur les territoires conquis en SEG280. Ils prônent une foi plus ouverte et plus progressiste que la doctrine orthodoxe. Ils cherchent avant tout à sortir Cardan de son autarcie séculaire et défendent ainsi une politique plus ouverte au commerce avec le reste de la galaxie tout en restant indépendants faces aux autres grandes puissances.',
            'desc2' => 'Si l’on devait choisir un adjectif qui décrirait les néo-humanistes, celui qui conviendrait le mieux serait probablement “mesuré”. Partant du pragmatisme quasiment matérialiste des néo-nervéens, les néo-humanistes y ont ajouté une touche de religion qui désormais éclaire leur morale. Très vite, ils se sont intéressés aux nouvelles sciences qui avaient été interdites par les décrets magothistes. Ils disposent désormais d’universités parmi les plus performantes de la galaxie.',
            'desc3' => 'Préférant la diplomatie à la guerre, les néo-humanistes ne disposaient il y a peu que d’une force armée très limitée. Mais lors des premiers troubles qui secouèrent Cardan, un nouveau groupe politique beaucoup moins pacifique fit son apparition. Les généraux Cardaniens s’étant prononcés pour une réforme politique de la faction et donc contre le clergé Magothiste, ont massivement rejoint la Nouvelle Église Cardanienne. Désormais il y a nombre de bons stratèges néo-humanistes qui ont commencé à militariser certaines bases orbitales stratégiques dans la possibilité d’une attaque magothe sur les terres que contrôlent la Nouvelle Église Cardanienne.',
            'desc4' => 'Les néo-humanistes, tout comme chez leurs prédécesseurs, ont un certain goût pour l’esthétisme, l’ingénierie et les matières nobles. Aimant la simplicité que l’on retrouve dans les arts immémoriaux telles que la peinture ou la sculpture, ils trouvent parfois surfaits les héritages des anciens monuments de la Nerve. Ils préfèrent bâtir des cathédrales exposées à la lumière des astres plutôt que dans de longues galeries souterraines.',
            'bonus' => [6, 12],
            'mandateDuration' => 950400,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction. 
				<br /><br />Une fois que toutes les candidatures ont été déposées, chaque membre de la Nerve peut voter pour le candidat de son choix. À la fin de la période de vote, la personne ayant reçu le plus de voix est élue Président. 
				<br /><br />Le Président va ensuite choisir trois personnes parmi les membres du Sénat pour le seconder dans la Faction. Il va devoir déterminer le Ministre de la défense, le Ministre des finances et le Premier Ministre de la faction. '
        ], [
            'id'            => 12,
            'officialName'    => 'Impérialiste',
            'popularName'    => 'Impérialiste',
            'government'    => 'Maison de Dhase',
            'demonym'        => 'impérialistes',
            'factionPoint'    => 'Points d\'industrie',
            'status'        => ['Noble', 'Prince', 'Vidôme', 'Maréchal', 'Préfet', 'Gouverneur Général'],
            'regime'        => 2,
            'devise'        => 'Pour l’honneur, la vertu et la foi !',
            'desc1' => 'Les Impérialistes, regroupés derrière la maison de Dhase, ancienne famille de souches impériales et néo-nervéennes gouvernant la province de Cardan avant son indépendance, ont aussi pris les armes contre les autres factions. Ils recherchent à rattacher leurs territoires à l’Empire et rejettent désormais la foi cardanienne orthodoxe au profit d’un culte lié à l’empereur.',
            'desc2' => 'Depuis l’indépendance de Cardan, les anciens nobles qui travaillaient pour l’administration de la marche ont été forcés d’abandonner leur privilèges. Si beaucoup d’entre eux ont quitté Cardan pour la Ligue ou l’Empire, la minorité qui est restée dans les Cardamines ont développé des coutumes hybrides combinant celles de la haute noblesse impériale et la ferveur cardanienne. Simple dans leur quotidien mais aimant le faste des grandes réceptions, les impérialistes sont un curieux mélange de deux sociétés opposées que peu auraient imaginé possible.',
            'desc3' => 'Inspirés par les conquêtes d’Akhéna, les impérialistes sont de grands stratèges dans l’âme. Aimant les combats et la guerre, les soldats impérialistes ne jurent que par et pour l’honneur. Le culte des anciens empereurs glorifie l’héroïsme et le sacrifice, ce qui rend les guerriers de cette faction redoutables. Ils sont convaincus que leur courage combiné à leur technologie récemment achetée à l’Empire les feront maître des Cardamines.',
            'desc4' => 'Ils aiment les arts raffinés, comme la peinture, la sculpture et les holoreportages. Ils sont friands de rassemblements où des pièces antiques en théâtre ou en musique sont rejouées. La littérature ainsi que le sport, car comme tout le monde le sait, le sport est la base d’une âme pure, est au coeur des loisirs des impérialistes.',
            'bonus' => [0, 1],
            'mandateDuration' => 604800,
            'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de points. Ces points sont consultables dans le classement général.',
            'campaignDesc' => 'À tout moment, un membre du sénat ou du gouvernement peut tenter un coup d\'état. Il aura alors 7 relèves pour attirer des partisans. Il aura réussi son putsch s\'il arrive à recruter suffisemment de partisans.'
        ]
    ];
}
