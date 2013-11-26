<?php
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
define('GETOUT_ROOT',			'http://www.expansion-lejeu.ch/');
define('APP_NAME',				'Expansion');
define('APP_SUBNAME',			'Alpha 3');
define('APP_VERSION',			'0.11');
define('APP_CREATOR',			'Expansion team');
define('APP_DESCRIPTION',		'Expansion, jeu par navigateur');
define('APP_ID',				3);

define('FACEBOOK_LINK',			'#');
define('TWEETER_LINK',			'#');

define('APP_ROOT',				'/server3/');

# asm constantes
define('ASM_UMODE', 			TRUE);

# password
define('PWD_SCRIPT', 			'wjfhecasgkdsg');
define('PWD_API', 				'qsafdachiefas');

# alert constantes
define('ALERT_DEFAULT',			0);

define('ALERT_STD_INFO',		100);
define('ALERT_STD_ERROR',		101);
define('ALERT_STD_SUCCESS',		102);
define('ALERT_STD_FILLFORM',	103);	// error in form filling

define('ALERT_BUG_INFO',		200);
define('ALERT_BUG_ERROR',		201);
define('ALERT_BUG_SUCCESS',		202);

define('ALERT_GAM_GENERATOR',	300);	// building construction
define('ALERT_GAM_REFINERY',	301);	// refinery : silo full
define('ALERT_GAM_TECHNO',		302);	// new techno
define('ALERT_GAM_DOCK1',		303);	// ship construction
define('ALERT_GAM_DOCK2',		304);	// ship construction
define('ALERT_GAM_DOCK3',		305);	// mothership construction
define('ALERT_GAM_COMPLAT',		306);	// new route

define('ALERT_GAM_NOMORECASH',	307);
define('ALERT_GAM_RESEARCH',	308);	// reseach found
define('ALERT_GAM_NOTIF',		309);	// new notif
define('ALERT_GAM_MESSAGE',		310);	// new message
define('ALERT_GAM_SPY',			311);	// somebody is attacking you
define('ALERT_GAM_ATTACK',		312);	// fight
define('ALERT_GAM_MARKET',		313);	// transaction in the market	

# event constantes
define('EVENT_BASE', 			0);		// correspond à une construction (batiment ou vaisseau) dans une base	
define('EVENT_OUTGOING_ATTACK', 1);
define('EVENT_INCOMING_ATTACK', 2);

# constante de temps pour la mise à jour des événements dans loadEvent.php
define('TIME_EVENT_UPDATE',		300);	// 300 s = 5 minutes

# constantes pour le contre-espionnage
define('ANTISPY_OUT_OF_CIRCLE',	0);
define('ANTISPY_BIG_CIRCLE',	1);
define('ANTISPY_MIDDLE_CIRCLE',	2);
define('ANTISPY_LITTLE_CIRCLE',	3);

define('ANTISPY_DISPLAY_MODE', 	0);		// pour Game::antiSpyRadius()
define('ANTISPY_GAME_MODE', 	1);
?>