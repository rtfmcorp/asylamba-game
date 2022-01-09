<?php

const P_TYPE = 'app';
const ASM_UMODE = true;

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
	return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
