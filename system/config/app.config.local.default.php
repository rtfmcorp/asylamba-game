<?php
########
# MODE #
########

# active le buffer et la page des scripts sans mot de passe
define('DEVMODE', 				TRUE);
	ini_set('display_errors', 	DEVMODE);
# active les APIs de confirmation
# note : il faut remplir d'autre infos pour que ça marche
define('APIMODE', 				FALSE);
# active la création de personnage au niveau 5
define('HIGHMODE', 				TRUE);
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

########
# PATH #
########

# défini le chemin de base du serveur
# note : de préférance un chemin relatif
define('APP_ROOT',				'/asylamba/game/');
# défini l'URL de sortie
# note : très important pour l'utilisation de l'API
# note : en règle général une URL absolue
define('GETOUT_ROOT',			APP_ROOT . 'buffer/');

############
# DATABASE #
############

# défini l'hôte (machine cible) du serveur de donnée
define('DEFAULT_SQL_HOST',		'127.0.0.1');
define('ADMIN_SQL_HOST',		DEFAULT_SQL_HOST);
# défini la base de donnée
define('DEFAULT_SQL_DTB',		'asylamba-game');
define('ADMIN_SQL_DTB',		 	DEFAULT_SQL_DTB);
# défini l'utilisateur par défaut
define('DEFAULT_SQL_USER',		'root');
# défini le mot de passe de l'utilisateur par défaut
define('DEFAULT_SQL_PASS',		'');
# défini l'administrateur
define('ADMIN_SQL_USER',		'root');
# défini le mot de passe de l'administrateur
define('ADMIN_SQL_PASS',		'');

#################
# KEY / BINDING #
#################

# défini l'ID du serveur
# note : corresponds à l'ID du serveur sur le portail
define('APP_ID',				0);
# définission des clés
# note : doivent être sembables à celles enregistrées sur le portail
define('KEY_SERVER', 			'123456');
define('KEY_SCRIPT', 			'123456');
define('KEY_BUFFER', 			'123456');
define('KEY_API', 				'123456');

#################
# MISCELLANEOUS #
#################

# définition des ids des différants joueurs systèmes
define('ID_GAIA', 				1);
define('ID_JEANMI', 			2);
define('SHIFT_FACTION', 		2);

# date de début du serveur
define('SERVER_START_TIME', '2015-06-30 10:00:00');
define('SEGMENT_SHIFT', 650);
