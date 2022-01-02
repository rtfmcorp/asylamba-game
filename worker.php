<?php

set_time_limit(0);
ini_set('MAX_EXECUTION_TIME', -1);

require_once('vendor/autoload.php');

use Asylamba\Classes\Worker\Worker;

// The first index is the script name
array_shift($argv);

$options = [];
foreach($argv as $arg)
{
	$data = explode('=', $arg);
	$options[substr($data[0], 2)] = $data[1];
}

const P_TYPE = 'worker';
const ASM_UMODE = true;

$projectDir = __DIR__;

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->usePutenv(false);
$dotenv->load($projectDir.'/.env');

$worker = new Worker($options['process'], $projectDir);
$worker->boot();
