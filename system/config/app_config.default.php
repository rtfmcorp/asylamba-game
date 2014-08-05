<?php
/* DEFAULT APP_CONFIG FILE
	fill the constants
	and rename the file without the ".default"
*/

# réglage temporel et niveau d'erreur
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/Zurich');
	ini_set('memory_limit', '512M');
	ini_set('error_prepend_string', 'System Error #');
	ini_set('error_log', 'public/log/php/' . date('Y-m-d') . '.log');

# mode de développement
define('DEVMODE', 				TRUE);
	ini_set('display_errors', 	DEVMODE);

# démarrage de la session
session_start();

# constante de l'application
define('SERVER_SESS',	 		'server3');
define('APP_NAME',				'Expansion');
define('APP_SUBNAME',			'Alpha 3');
define('APP_VERSION',			'0.11');
define('APP_CREATOR',			'Expansion team');
define('APP_DESCRIPTION',		'Expansion, jeu par navigateur');
define('APP_ID',				3);

define('FACEBOOK_LINK',			'http://facebook.com/asylamba');
define('TWITTER_LINK',			'#');
define('GOOGLE_PLUS_LINK',		'http://plus.google.com/+Asylamba-game');

define('APP_ROOT',				'/Expansion/dev12/'); 						# TO FILL
define('GETOUT_ROOT',			APP_ROOT . 'buffer');

# asm constantes
define('ASM_UMODE', 			TRUE);

# password
define('PWD_SCRIPT', 			''); 										# TO FILL
define('PWD_API', 				''); 										# TO FILL
?>