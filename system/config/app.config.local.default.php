<?php
	# mode de développement
	define('DEVMODE', 				TRUE);
	define('PORTALMODE', 			TRUE);
		ini_set('display_errors', 	DEVMODE);

	# constante de l'application
	define('SERVER_SESS',	 		'server3');
	define('APP_NAME',				'Asylamba');
	define('APP_SUBNAME',			'Serveur local');
	define('APP_VERSION',			'0.11.2');
	define('APP_CREATOR',			'Asylamba team');
	define('APP_DESCRIPTION',		'Asylamba, jeu par navigateur');
	define('APP_ID',				0);

	# root
	define('APP_ROOT',				'/expansion/dev12/');
	define('GETOUT_ROOT',			APP_ROOT . 'buffer/');

	# password
	define('PWD_SCRIPT', 			'kbxm655q');
	define('PWD_API', 				'qsafdachiefas');

	# id du joueur rebelle de base
	define('ID_GAIA', 				1);

	# DB USER
#	define('DEFAULT_SQL_USER',		'expansion_user');
#	define('DEFAULT_SQL_HOST',		'localhost');
#	define('DEFAULT_SQL_PASS',		'KtbMwzU3XqnnPwWG');
#	define('DEFAULT_SQL_DTB',		 'expansion_s3');

	define('DEFAULT_SQL_USER',		'root');
	define('DEFAULT_SQL_HOST',		'127.0.0.1');
	define('DEFAULT_SQL_PASS',		'');
	define('DEFAULT_SQL_DTB',		'expansion-game');

	# DB ADMIN
#	define('ADMIN_SQL_USER',		'expansion_user');
#	define('ADMIN_SQL_HOST',		'localhost');
#	define('ADMIN_SQL_PASS',		'KtbMwzU3XqnnPwWG');
#	define('ADMIN_SQL_DTB',			 'expansion_s3');

	define('ADMIN_SQL_USER',		'root');
	define('ADMIN_SQL_HOST',		'127.0.0.1');
	define('ADMIN_SQL_PASS',		'');
	define('ADMIN_SQL_DTB',		 	'expansion-game');
?>