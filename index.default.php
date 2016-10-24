<?php

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\CTR;

# définition des ROOT
define('PUBLICR',		'http://localhost/[ your path here ]/public/');
define('SYSTEMR',		__DIR__ . '/system/');
define('CLASSES',               SYSTEMR . 'Classes/');

# définition des ROOT
define('CSS', 			PUBLICR . 'css/');
define('JS', 		 	PUBLICR . 'js/');
define('MEDIA', 		PUBLICR . 'media/');
define('LOG', 			PUBLICR . 'log/');

define('MODULES', 		SYSTEMR . 'Modules/');
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

# inclusion des fichiers de configurations
include CONFIG . 'app.config.local.php';
include CONFIG . 'app.config.global.php';

# définition des ROOT des MODULES
include_once MODULES . 'Ares/main.php';
include_once MODULES . 'Hermes/main.php';
include_once MODULES . 'Gaia/main.php';
include_once MODULES . 'Zeus/main.php';
include_once MODULES . 'Athena/main.php';
include_once MODULES . 'Promethee/main.php';
include_once MODULES . 'Artemis/main.php';
//include_once MODULES . 'Apollon/main.php';
include_once MODULES . 'Demeter/main.php';
include_once MODULES . 'Atlas/main.php';

# Action du controller
CTR::init();
CTR::checkPermission();
CTR::getInclude();
CTR::save();
