<?php

set_time_limit(0);

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\Application;

$application = new Application();
$application->boot();
