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
namespace Asylamba\Modules\Zeus\Resource;

class TutorialResource
{
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
    
    const FACTION_FORUM = 17;
    const SHARE_ASYLAMBA = 18;
    const REFINERY_LEVEL_10 = 19;
    const STORAGE_LEVEL_8 = 20;
    const DOCK1_LEVEL_6 = 21;
    const REFINERY_LEVEL_16 = 22;
    const STORAGE_LEVEL_12 = 23;
    const TECHNOSPHERE_LEVEL_6 = 24;
    const SHIP1_UNBLOCK = 25;
    const DOCK1_LEVEL_15 = 26;
    const BUILD_SHIP1 = 27;
    const REFINERY_LEVEL_20 = 28;

    const SPONSORSHIP = 29;


    public static function stepExists($step)
    {
        if ($step > 0 and $step <= count(self::$steps)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isLastStep($step)
    {
        if ($step == count(self::$steps)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getInfo($id, $info)
    {
        if ($id <= count(self::$steps)) {
            if (in_array($info, array('id', 'title', 'description', 'experienceReward', 'creditReward', 'resourceReward', 'shipReward'))) {
                return self::$steps[$id - 1][$info];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private static $steps = array(
        array(
            'id' => 1,
            'title' => 'Bienvenue',
            'description' => 'Salut à toi,
				<br /><br />
				Bienvenue dans le monde d\'Asylamba! Je me présente : je m\'appelle Jean-Mi, je suis l\'opérateur du jeu et je vais te guider dans tes débuts. 
				<br /><br />
				Nous voici dans ta page de profil. Elle résume l\'ensemble de tes activités actuelles.
				<br /><br />
				Pour suivre le tutoriel, tu devras faire ce qui t\'es indiqué ici. D\'une fois que tu as accompli la tâche, la petite étoile sera colorée avec la couleur de ta faction. Tu peux cliquer dessus et valider l\'étape. Ensuite reviens ici et l\'étape suivante sera disponible.',
            'experienceReward' => 1,
            'creditReward' => 0,
            'resourceReward' => 50,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 2,
            'title' => 'Navigation et temporalité',
            'description' => 'Dans Asylamba, la navigation est horizontale. Pour faire glisser le panneau central, utilise les flèches directionnelles de ton clavier ou clique, à l\'aide de ta souris, sur les flèches qui s\'affichent aux deux extrémités de l\'écran.
				<br /><br />
				La barre de navigation en haut te permet d\'accéder à ta planète (à tes différentes planètes quand tu en auras plusieurs). Tu pourras accéder aussi aux différentes pages de gestion de ton empire comme les finances, l\'université, la carte et l\'amirauté. 
				<br /><br />
				Dernière précision, le temps sur Asylamba évolue de la même façon que dans la réalité. En revanche, les unités changent. Une relève correspond à une heure. Un segment équivaut à 24 heures, ou 24 relèves. Et pour finir une strate correspond à 30 segments. Pour te faire une idée, une relève pour toi correspond à environ 2 semaines dans le monde d\'Asylamba.',
            'experienceReward' => 2,
            'creditReward' => 0,
            'resourceReward' => 200,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 3,
            'title' => 'Construire le Générateur au niveau 2',
            'description' => 'Le générateur est le bâtiment essentiel pour développer ta colonie. La construction des autres bâtiments dépend du niveau de ton générateur. Actuellement, il est au niveau 1. Suis les instructions ci-dessous pour lancer la construction de ton générateur niveau 2.
				<br /><br />
				Va sur ta base orbitale en cliquant sur son nom en-haut à gauche de l\'écran. 
				<br /><br />
				Tu te trouves à présent sur la vue de situation. 
				<br /><br />
				A gauche de ton écran, se trouve une barre de navigation. Elle te permet de te déplacer dans les différents bâtiments. 
				<br /><br />
				Clique sur l\'icône du générateur pour y accéder et sur "augmenter vers le niveau 2" sur le générateur. Celui-ci sera mis dans la file de construction et mettra un certain temps à se terminer. Plus le niveau augmente, plus le temps de construction et le prix augmentent.
				<br /><br />
				En survolant chaque bâtiment avec ta souris, un petit "+" apparaît. Si tu cliques dessus, un tableau avec les prix et les temps de construction pour les différents niveaux apparaîtra.',
            'experienceReward' => 3,
            'creditReward' => 0,
            'resourceReward' => 400,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 4,
            'title' => 'Construire la Raffinerie au niveau 3',
            'description' => 'Rends-toi à nouveau dans le générateur. Cette fois, tu devra construire la Raffinerie au niveau 3. Si le bouton de construction est grisé, cela veut dire que tu n\'as pas tous les prérequis pour exécuter la construction. 
				Il faut toujours que le niveau du générateur soit plus haut que le niveau des autres bâtiments, construis donc un niveau supplémentaire du générateur.
				<br /><br />
				La raffinerie sert à produire des ressources. Plus le niveau de la raffinerie est élevé, plus elle sera efficiente.
				Les ressources sont produites chaque relève.
				<br /><br />
				Dans chaque bâtiment, il y a un panneau nommé "à propos". Si tu veux en savoir plus, lis ce panneau, des informations importantes et intéressantes peuvent s\'y trouver.',
            'experienceReward' => 3,
            'creditReward' => 0,
            'resourceReward' => 200,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 5,
            'title' => 'Construire le Stockage au niveau 3',
            'description' => 'Maintenant que ta Raffinerie tourne à plein régime, il faut que tu stockes tes ressources pour les utiliser plus tard. Construis le Stockage depuis le Générateur.',
            'experienceReward' => 3,
            'creditReward' => 0,
            'resourceReward' => 100,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 6,
            'title' => 'Construire la Technosphère',
            'description' => 'Pour pouvoir assurer l\'expansion de ton empire, il est essentiel de construire la Technosphère. C\'est grâce à elle que tu vas développer des technologies pour débloquer de nouveaux bâtiments, de nouveaux vaisseaux. La Technosphère améliorera ainsi divers aspects du jeu.
				<br /><br />
				Rends-toi dans le générateur pour construire la Technosphère.
				<br /><br />
				A chaque fois que tu construis un nouveau bâtiment, une nouvelle icône s\'ajoute dans la barre de navigation sur la gauche de l\'écran.',
            'experienceReward' => 3,
            'creditReward' => 0,
            'resourceReward' => 100,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 7,
            'title' => 'Modifier l\'investissement universitaire dans les finances',
            'description' => 'Dans Asylamba, tu dois gérer tes crédits. Pour cela tu as un résumé de tes finances dans l\'onglet "finance" qui se trouve en-haut de l\'écran (quatrième icône du deuxième groupe de menus).
				<br /><br />
				Depuis là, tu peux voir si tu gagnes ou perds des crédits à chaque relève. Tu peux être en négatif, mais sache que ça va puiser dans ta réserve de crédits. 
				<br /><br />
				Dans la colonne "dépenses", modifie tes investissements universitaires en cliquant sur le petit carré avec une flèche dirigée vers le bas puis en modifiant le montant.
				<br /><br />
				Une fois cet investissement modifié, tu peux voir comment ce montant est utilisé par tes chercheurs en allant dans l\'Université. Pour y accéder, clique sur l\'onglet "université", il se trouve juste à droite de l\'onglet "finance". Tu peux changer les pourcentages de chaque faculté en fonction de tes besoins.',
            'experienceReward' => 3,
            'creditReward' => 2500,
            'resourceReward' => 0,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 8,
            'title' => 'Former un officier',
            'description' => 'Sur chaque base orbitale, tu as une école de commandement où tu peux former des commandants. 
				Pour y accéder, clique sur la dernière icône de la barre de navigation rapide (il faut d\'abord retourner sur ta base orbitale en cliquant sur son nom).
				<br /><br />
				Dans cette école, tu peux créer de nouveaux commandants et les former au sein de l\'école. 
				Quand ils seront prêts, tu pourras leur attribuer une flotte et les envoyer au combat.
				<br /><br />
				Pour créer un commandant, il suffit de lui donner un nom (ou de laisser le nom que j\'ai généré pour toi) et de cliquer sur le bouton "créer l\'officier".
				2500 crédits te seront débités, mais c\'est pour la bonne cause.',
            'experienceReward' => 5,
            'creditReward' => 0,
            'resourceReward' => 100,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 9,
            'title' => 'Construire le Chantier Alpha',
            'description' => 'Toujours dans le générateur, construis le Chantier Alpha. Une fois que la construction sera achevée, tu auras accès à ce bâtiment par la barre de navigation rapide.
				<br /><br />
				Ce bâtiment te permet de construire des vaisseaux de type "Chasseur" et de type "Corvette". 
				Par la suite, tu pourras construire des vaisseaux plus grands dans le Chantier de Ligne.
				<br /><br />
				Pour pouvoir construire un vaisseau, il faut des prérequis. Ces prérequis sont précisés dans le bouton de construction. 
				Dans le panneau d\'information de chaque vaisseau qui apparaît lors d\'un clic sur le "+" se trouvent les caractéristiques du vaisseau ainsi que divers autres informations.',
            'experienceReward' => 3,
            'creditReward' => 1000,
            'resourceReward' => 150,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 10,
            'title' => 'Développer la technologie "Châssis simple léger"',
            'description' => 'Nous voulons construire un Pégase, malheureusement il nous manque un prérequis : la technologie "Châssis simple léger". 
				Rends-toi donc dans la Technosphère pour développer cette technologie.
				<br /><br />
				Dans la Technosphère, plusieurs types de technologie sont disponibles. 
				Les "Châssis" permettent de débloquer de nouveaux vaisseaux. 
				Les "Nouvelles technologies" permettent de débloquer de nouveaux bâtiments ou de nouvelles actions indispensables comme la "Colonisation" par exemple.
				Les "Améliorations industrielles" permettent de rendre plus efficace divers points du jeu, par exemple augmentation de la production de ressources, augmentation de la vitesse de construction de certaines vaisseaux, augmentation de la force de frappe d\'un vaisseau, etc.
				Les deux premières catégories sont des technologies qu\'on débloque une seule fois, par contre les améliorations industrielles se font par niveau.
				<br /><br />
				Encore une fois, chaque technologie a besoin de prérequis qui sont précisés dans le panneau "+" sur chaque technologie. 
				Ces prérequis comprennent à chaque fois un niveau de la Technosphère et des "Recherches".
				Tes niveaux actuels de recherche sont visibles en cliquant sur l\'onglet "technologie" qui se trouve parmi les icônes tout au sommet de ton écran.
				<br /><br />
				Tu as tous les prérequis pour développer "Châssis simple léger", fais-le donc.',
            'experienceReward' => 4,
            'creditReward' => 0,
            'resourceReward' => 3000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 11,
            'title' => 'Construire un Chasseur Léger',
            'description' => 'Tu peux maintenant aller dans le Chantier Alpha et construire un Pégase. Il s\'agit du plus petit vaisseau disponible.
				<br /><br />
				En cliquant sur le "+", tu verras toutes les informations sur ce vaisseau, 
				les caractéristiques du vaisseau durant un combat, mais également le temps de construction unitaire, le coût, le nombre de PEV et la soute.
				La soute est le nombre de ressources que ce vaisseau peut ramener d\'un combat. 
				Les PEV sont les Points-Equivalent-Vaisseau, ce qui correspond à la place que va prendre le vaisseau dans une escadrille sachant qu\'une escadrille a un nombre de place limité.
				<br /><br />
				Si tu as assez de ressources, tu peux lancer la construction de plusieurs vaisseaux de même type à la fois. 
				Cela te facilitera la vie et te permet de mettre beaucoup de vaisseaux en construction. 
				Attention, la file de construction est limitée en place.
				<br /><br />
				Les vaisseaux, une fois produits, ont un coût d\'entretien qui est différent pour chaque vaisseau. Un vaisseau affecté coûte plus cher qu\'un vaisseau stocké dans les hangars. 
				Pensez-donc à vérifier l\'état de vos finances de temps à autre, il serait bête d\'envoyer vos vaisseaux à la casse par manque de crédits.',
            'experienceReward' => 8,
            'creditReward' => 0,
            'resourceReward' => 0,
            'shipReward' => array(2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 12,
            'title' => 'Affecter un officier',
            'description' => 'Il faut à présent affecter un officier sur ta base orbitale pour qu\'il la protège. Pour ce faire, tu as deux solutions. Soit aller sur la vue de situation de ta base orbitale et cliquer sur "Affecter un officier" sur l\'une des deux lignes. Ceci te dirigera vers l\'Ecole de commandement. Soit aller directement dans l\'Ecole de commandement.
				<br /><br />
				Depuis là, clique simplement sur "affecter" sur l\'officier que tu veux affecter. Tu seras ensuite redirigé vers l\'amirauté où tu pourras gérer l\'officier et sa flotte.',
            'experienceReward' => 6,
            'creditReward' => 0,
            'resourceReward' => 0,
            'shipReward' => array(2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 13,
            'title' => 'Constituer une escadrille',
            'description' => 'Va dans l\'amirauté, si tu n\'y es pas déjà. Tu verras le commandant que tu as précédemment affecté. Pour le gérer, clique sur la petite flèche qui se trouve à droite de sa case. Des panneaux s\'ouvriront et toutes les informations du commandant s\'afficheront. Glisse vers la droite pour tout voir.
				<br /><br />
				Un commandant de niveau 1, un Aspirant, peut contrôler une escadrille. Chaque niveau supplémentaire lui offre une escadrille en plus. Pour affecter des vaisseaux à une escadrille il faut cliquer sur celle-ci. Si l\'opération s\'est bien déroulée, l\'escadrille est entourée par un traitillé.
				<br /><br />
				Ensuite dans la colonne à droite se trouve la composition de l\'escadrille et dans la colonne suivante se trouvent les vaisseaux qui sont dans le hangar. Pour affecter un vaisseau, il suffit de cliquer sur un vaisseau du hangar et il sera transféré dans l\'escadrille.
				<br /><br />
				Chaque vaisseau vaut un nombre de PEV (points-équivalant-vaisseau), plus le vaisseau est grand, plus il a de PEV. Attention, une escadrille est constituée au maximum de 100 PEV, choisis donc bien la composition de ton escadrille.
				<br /><br />
				NB: Si des vaisseaux ont été transférés dans l\'escadrille et que l\'étoile du tutoriel ne s\'allume pas, rafraîchis ta page (F5).',
            'experienceReward' => 2,
            'creditReward' => 200,
            'resourceReward' => 0,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 14,
            'title' => 'Déplacer une flotte en première ligne',
            'description' => 'Dans la vue de situation, deux lignes de défense sont disponibles autour d\'une planète. La première ligne (ligne extérieure) sert de défense lorsqu\'un ennemi attaque ta planète. La deuxième ligne sert de réserve, elle ne défendra pas lors d\'une attaque, par contre elle entrera en jeu lorsqu\'un ennemi tentera de prendre ta planète.
				<br /><br />
				Lorsque tu as affecté ton officier, il s\'est positionné en ligne 2. Pour mieux défendre ta planète, positionne-le en ligne 1. Pour ce faire, va dans la vue de situation (clique sur le nom de ta base en-haut de l\'écran), survole ta flotte avec la souris, une flèche apparaîtra. Si tu cliques dessus, ta flotte changera de ligne.',
            'experienceReward' => 2,
            'creditReward' => 40000,
            'resourceReward' => 0,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 15,
            'title' => 'Espionner une planète',
            'description' => 'Maintenant que tu as une flotte à ta disposition, tu peux piller une planète rebelle. Mais avant cela, il est mieux de savoir quelle est la force de la flotte adverse. Pour le savoir, tu peux l\'espionner.
				<br /><br />
				En premier lieu, va sur la carte de la galaxie : troisième icône du menu en-haut de l\'écran. Clique sur un système solaire proche de ta planète, un panneau va s\'ouvrir en bas de l\'écran. Là tu vois toutes les planètes de ce système solaire. Choisis ensuite une planète en cliquant dessus. 
				<br /><br />
				Sur chaque planète, tu as cinq actions possibles. La cinquième correspond à l\'espionnage, clique dessus (si l\'icône est grise, choisis une autre planète). En payant une certaine somme en crédits, tu pourras espionner la planète. Plus la somme est élevée, plus tu verras d\'informations.
				<br /><br />
				Une fois que tu as cliqué, tu seras redirigé vers l\'amirauté, sur le rapport d\'espionnage. Si tu vois des points d\'interrogation, cela signifie que tu n\'as pas payé assez cher pour voir cette information. Si tu as payé assez cher, tu verras la flotte qu\'il y a en défense ainsi que le nombre de PEV de la flotte.',
            'experienceReward' => 5,
            'creditReward' => 10000,
            'resourceReward' => 200,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 16,
            'title' => 'Attaquer une planète rebelle',
            'description' => 'Pour faire ton premier pillage, il faut trouver une planète qui n\'est pas très défendue. Trouve une planète qui a moins de PEV que ta flotte. Un conseil, espionne une planète avec moins de 50 millions d\'habitants, elle aura une petite flotte défensive.
				<br /><br />
				Une fois la planète idéale trouvée, il est temps de l\'attaquer. Tu pourras arriver directement sur la planète en cliquant sur ses coordonées dans le rapport d\'espionnage ou en allant sur la carte de la galaxie. A l\'endroit où tu as les cinq actions, la première sert à piller la planète. Sélectionne ta flotte à gauche de l\'écran, tu verras apparaître des informations comme le temps que l\'attaque va durer. Un bouton "Lancer l\'attaque" apparaît également, clique dessus.
				<br /><br />
				Voilà, tu as lancé ta première attaque. Dans l\'Amirauté, tu peux voir l\'avancement de ta flotte. D\'une fois qu\'elle sera arrivée, tu recevras un rapport de combat. Si tu as perdu, tu perds ta flotte ainsi que ton officier. Si tu as gagné, ta flotte rentrera sur ta base avec les ressources qu\'elle aura réussi à piller.',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 0,
            'shipReward' => array(3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 17,
            'title' => 'Poster un message sur le forum de faction',
            'description' => 'Le forum de faction est un lieu essentiel dans le développement au sein du jeu. C\'est là que tu peux discuter avec les membres de ta faction sur divers points, que ça soit de stratégie, de commerce, de politique. Il y a même un Biastro !
				<br /><br />
				Pour accéder au forum, tu dois d\'abord cliquer sur l\'onglet faction en haut de ton écran puis cliquer sur le menu forum. Là tu trouveras diverses catégories. Chaque partie du forum a son utilité.
				<br /><br />
				Pour passer cette étape, tu dois créer un sujet dans le forum ou répondre à un sujet déjà créé. Dis bonjour à tes nouveaux collègues.',
            'experienceReward' => 10,
            'creditReward' => 400,
            'resourceReward' => 0,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 18,
            'title' => 'Parler d\'Asylamba',
            'description' => 'Cette étape un peu symbolique t\'encourage, si tu as aimé ce que tu as vu pour l\'instant, à parler du jeu autour de toi pour que d\'autres personnes susceptibles d\'aimer ce genre de jeu puisse le découvrir.
				<br /><br />
				Pour ce faire tu as plusieurs possibilités, dont aimer et partager la page <a href="https://facebook.com/asylamba" target="_blank">Facebook</a> d\'Asylamba ou nous suivre sur <a href="https://twitter.com/asylamba" target="_blank">Twitter</a>.
				<br /><br />
				Merci à toi de m\'aider à propager le jeu et un grand bravo pour ton parcours jusqu\'ici. Bon jeu !
				<br /><br />
				Signé Jean-Mi, opérateur du jeu.',
            'experienceReward' => 10,
            'creditReward' => 100,
            'resourceReward' => 100,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 19,
            'title' => 'Construire la Raffinerie au niveau 10',
            'description' => 'Une bonne planète est une planète qui produit. Veille a progressivement augmenter le niveau de ta raffinerie de manière à toujours gagner suffisamment de ressources.',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 3000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 20,
            'title' => 'Construire le Stockage au niveau 8',
            'description' => 'N\'oublie pas de toujours avoir assez d\'espace de stockage pour passer la nuit en toute tranquilité. Lorsque le Stockage est plein, la Raffinerie arrête de produire.',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 2000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 21,
            'title' => 'Construire le Chantier Alpha au niveau 6',
            'description' => 'En plus de débloquer de nouveaux vaisseaux, augmenter le niveau de ton Chantier Alpha te permettra de disposer de plus grands hangars. Tu pourras alors commander la construction de plus de vaisseaux.',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 0,
            'shipReward' => array(5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 22,
            'title' => 'Construire la Raffinerie au niveau 16',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 6000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 23,
            'title' => 'Construire le Stockage au niveau 12',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 4000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 24,
            'title' => 'Construire la Technosphère au niveau 6',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 2000,
            'resourceReward' => 2000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 25,
            'title' => 'Développer la technologie "Châssis simple amélioré"',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 2000,
            'resourceReward' => 2000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 26,
            'title' => 'Construire le Chantier Alpha au niveau 15',
            'description' => 'Afin de construire le nouveau type de vaisseau que tes ingénieurs de la Technosphère viennent de découvrir, tu dois disposer d\'un Chantier Alpha plus grand. Construis ce dernier au niveau 15.',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 8000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 27,
            'title' => 'Construire un Satyre',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 0,
            'shipReward' => array(0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 28,
            'title' => 'Construire la Raffinerie au niveau 20',
            'description' => '',
            'experienceReward' => 10,
            'creditReward' => 0,
            'resourceReward' => 8000,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)),
        array(
            'id' => 29,
            'title' => 'Parrainer vos amis',
            'description' => 'Vous avez la possiblité de parrainer vos amis. Cela consiste à les faire découvrir le jeu. Et s\'ils commencent à jouer, vous serez récompensé.
				<br /><br />
				Tout est expliqué sur la page prévue à cet effet que tu peux trouver en cliquant sur le bouton de déconnexion tout en-haut de la page à droite et ensuite en cliquant sur "parrainage".
				<br /><br />
				Plus il y a de monde sur le jeu, plus il se portera bien. Ceci est valable pour moi comme pour toi. C\'est un petit geste qui m\'aidera à conquérir le monde avec Aslymaba. Merci beaucoup d\'avance. Bon jeu !
				<br /><br />
				Signé Jean-Mi, opérateur du jeu.',
            'experienceReward' => 10,
            'creditReward' => 1000,
            'resourceReward' => 0,
            'shipReward' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0))
    );
}
