<?php

set_time_limit(0);
ini_set('MAX_EXECUTION_TIME', -1);

require_once('vendor/autoload.php');

use Asylamba\Classes\Kernel\ApplicationKernel;

const P_TYPE = 'app';
const ASM_UMODE = true;

$projectDir = __DIR__;

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->usePutenv(false);
$dotenv->load($projectDir.'/.env');

try {
	$application = new ApplicationKernel($projectDir);
	$application->boot();
} catch (\Throwable $t) {
	dd($t);
}
