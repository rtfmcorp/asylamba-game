<?php

set_time_limit(0);
ini_set('MAX_EXECUTION_TIME', -1);

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\Application;

define("P_TYPE", 'app');

$application = new Application();
$application->boot();
