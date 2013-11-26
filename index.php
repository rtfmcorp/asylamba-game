<?php
# définition des ROOT
define('PUBLICR',		'http://game.expansion-lejeu.ch/server3/public/');
# define('PUBLICR',		'http://localhost/expansion/dev12/public/');
define('SYSTEMR',		'system/');

#ajotu d'un commenaitre sépcial pour git
# définition des ROOT
define('CSS', 			PUBLICR . 'css/');
define('JS', 		 	PUBLICR . 'js/');
define('MEDIA', 		PUBLICR . 'media/');
define('LOG', 			PUBLICR . 'log/');

define('MODULESV', 		SYSTEMR . 'modules/');
define('CLASSES',		SYSTEMR . 'classes/');
define('LIB', 			SYSTEMR . 'lib/');
define('CONFIG', 		SYSTEMR . 'config/');
define('EVENT', 		SYSTEMR . 'event/');

define('INSCRIPTION', 	SYSTEMR . 'inscription/');
define('CONNECTION', 	SYSTEMR . 'connection/');

define('ACTION', 		SYSTEMR . 'action/std/');
define('AJAX', 			SYSTEMR . 'action/ajax/');

define('API', 			SYSTEMR . 'api/');
define('SCRIPT',		SYSTEMR . 'script/');

define('TEMPLATE', 		SYSTEMR . 'views/templates/');
define('PAGES', 		SYSTEMR . 'views/pages/');
define('COMPONENT', 	SYSTEMR . 'views/components/');

# définition des ROOT des MODULES
define('ARES', 			MODULES . 'ares/main.php');
define('HERMES', 		MODULES . 'hermes/main.php');
define('GAIA', 			MODULES . 'gaia/main.php');
define('ZEUS', 			MODULES . 'zeus/main.php');
define('ATHENA', 		MODULES . 'athena/main.php');
define('PROMETHEE', 	MODULES . 'promethee/main.php');
define('ARTEMIS', 		MODULES . 'artemis/main.php');
define('APOLLON', 		MODULES . 'apollon/main.php');
define('DEMETER', 		MODULES . 'demeter/main.php');

# Action du controller
CTR::initialize();
CTR::checkPermission();
CTR::getInclude();
CTR::save();
?>