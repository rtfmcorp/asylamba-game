<?php

set_time_limit(0);
ini_set('MAX_EXECUTION_TIME', -1);

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\Application;

const P_TYPE = 'app';

$projectDir = __DIR__;

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->usePutenv(false);
$dotenv->load($projectDir.'/.env');

$application = new Application($projectDir);
$application->boot();
