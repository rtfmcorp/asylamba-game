<?php
/* DEFAULT INDEX FILE
	fill the constants
	and rename the file without the ".default"
*/

	# mode de développement
	define('DEVMODE', 				TRUE);
		ini_set('display_errors', 	DEVMODE);

	# constante de l'application
	define('SERVER_SESS',	 		'server3');
	define('APP_NAME',				'Expansion');
	define('APP_SUBNAME',			'Alpha 3');
	define('APP_VERSION',			'0.11.2');
	define('APP_CREATOR',			'Expansion team');
	define('APP_DESCRIPTION',		'Expansion, jeu par navigateur');
	define('APP_ID',				3);

	# root
	define('APP_ROOT',				'/expansion/dev12/');
	define('GETOUT_ROOT',			APP_ROOT . 'buffer/');
#	define('GETOUT_ROOT',			'http://asylamba.com/');

	# password
	define('PWD_SCRIPT', 			'kbxm655q');
	define('PWD_API', 				'qsafdachiefas');

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