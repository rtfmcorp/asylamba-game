<?php

require_once('../vendor/autoload.php');

use Asylamba\Classes\Kernel\ApplicationKernel;

const P_TYPE = 'app';
const ASM_UMODE = true;

$projectDir = dirname(__DIR__);

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load($projectDir.'/.env');

$application = new ApplicationKernel($projectDir);
$application->boot();
