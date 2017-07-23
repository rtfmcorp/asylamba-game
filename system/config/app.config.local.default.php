<?php
########
# MODE #
########
# définition des ROOT
define('PUBLICR',		'/public/');
define('CSS', 			PUBLICR . 'css/');
define('JS', 		 	PUBLICR . 'js/');
define('MEDIA', 		PUBLICR . 'media/');
define('LOG', 			PUBLICR . 'log/');
ini_set('display_errors', ($container->getParameter('environment') === 'dev'));
# active les APIs de confirmation
# active la création de personnage au niveau 5
define('HIGHMODE', $container->getParameter('environment') === 'dev');
# active les feuilles de style multiples par faction
# note : les style peuvent ne pas être à jour
define('COLORSTYLE', 			TRUE);
# active l'ajout du script analytics
define('ANALYTICS', 			FALSE);
# active la captation des données d'analyse (TB)
define('DATA_ANALYSIS', 		FALSE);
#########
# INFOS #
#########
# défini le nom du tableau de session
# note : utile si plusieurs instances cohabitent sur la même machine
define('SERVER_SESS',	 		'server');
# défini le titre du serveur (affiché comme titre)
define('APP_NAME',				'Asylamba');
# défini le sous-titre du serveur (affiché comme titre)
define('APP_SUBNAME',			'Expansion Stellaire');
# numéro de version de l'app
define('APP_VERSION',			'2.0.0');
# créateurs du projet
define('APP_CREATOR',			'Gil Clavien, Jacky Casas, Noé Zufferey');
# défini la description du serveur (affichée dans la page)
define('APP_DESCRIPTION',		'Asylamba, jeu par navigateur');
define('APP_ROOT', $container->getParameter('app_root'));
#################
# MISCELLANEOUS #
#################
# définition des ids des différants joueurs systèmes
define('ID_GAIA', 				1);
define('ID_JEANMI', 			2);
define('SHIFT_FACTION', 		2);
# date de début du serveur
// @TODO inject the value in Chronos properly
define('SERVER_START_TIME', $container->getParameter('server_start_time'));
define('SEGMENT_SHIFT', $container->getParameter('segment_shift'));
