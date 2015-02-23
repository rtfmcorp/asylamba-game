<?php
# MODE
define('DEVMODE', 				TRUE);
define('APIMODE', 				TRUE);
define('COLORSTYLE', 			TRUE);
define('ANALYTICS', 			TRUE);
	ini_set('display_errors', 	DEVMODE);

# INFOS
define('SERVER_SESS',	 		'server3');
define('APP_NAME',				'Asylamba');
define('APP_SUBNAME',			'Serveur local');
define('APP_VERSION',			'0.11.2');
define('APP_CREATOR',			'Asylamba team');
define('APP_DESCRIPTION',		'Asylamba, jeu par navigateur');

# PATH
define('APP_ROOT',				'/expansion/dev12/');
define('GETOUT_ROOT',			APP_ROOT . 'buffer/');

# KEY / BINDING
define('APP_ID',				0);
define('KEY_SERVER', 			'key');
define('KEY_SCRIPT', 			'key');
define('KEY_BUFFER', 			'key');
define('KEY_API', 				'key');

# id du joueur rebelle de base
define('ID_GAIA', 				1);
define('ID_JEANMI', 			2);

# DB USER
define('DEFAULT_SQL_HOST',		'127.0.0.1');
define('DEFAULT_SQL_DTB',		'expansion-game');
define('DEFAULT_SQL_USER',		'root');
define('DEFAULT_SQL_PASS',		'');

# DB ADMIN
define('ADMIN_SQL_HOST',		DEFAULT_SQL_HOST);
define('ADMIN_SQL_DTB',		 	DEFAULT_SQL_DTB);
define('ADMIN_SQL_USER',		'root');
define('ADMIN_SQL_PASS',		'');
?>