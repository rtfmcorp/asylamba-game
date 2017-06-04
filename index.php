<?php

set_time_limit(0);

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\Application;

define("P_TYPE", 'app');

$application = new Application();
$application->boot();
