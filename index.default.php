<?php
# définition des ROOT
define('PUBLICR',		'http://localhost/[ your path here ]/public/');
define('SYSTEMR',		'system/');

# définition des ROOT
define('CSS', 			PUBLICR . 'css/');
define('JS', 		 	PUBLICR . 'js/');
define('MEDIA', 		PUBLICR . 'media/');
define('LOG', 			PUBLICR . 'log/');

define('MODULES', 		SYSTEMR . 'modules/');
define('CLASSES',		SYSTEMR . 'classes/');
define('LIB', 			CLASSES . 'lib/');
define('CONFIG', 		SYSTEMR . 'config/');
define('EVENT', 		SYSTEMR . 'event/');

define('INSCRIPTION', 	SYSTEMR . 'inscription/');
define('CONNECTION', 	SYSTEMR . 'connection/');

define('ACTION', 		SYSTEMR . 'action/std/');
define('AJAX', 			SYSTEMR . 'action/ajax/');

define('API', 			SYSTEMR . 'api/');
define('SCRIPT',		SYSTEMR . 'script/');
define('BUFFER',		SYSTEMR . 'buffer/');

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
define('ATLAS',			MODULES . 'atlas/main.php');

# inclusion des fichiers de configurations
include CONFIG . 'app.config.local.php';
include CONFIG . 'app.config.global.php';

# inclusion des classes
include CLASSES . 'loader.php';

# Action du controller
CTR::init();
CTR::checkPermission();
CTR::getInclude();
CTR::save();
?>