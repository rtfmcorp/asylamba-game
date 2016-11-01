<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;

$base = Utils::getHTTPData('base');
$page = Utils::getHTTPData('page'); # facultatif

if ($base !== FALSE) {
	if (CTR::$data->baseExist($base)) {
		CTR::$data->get('playerParams')->add('base', $base);
	}
}

if ($page !== FALSE) {
	switch ($page) {
		case 'generator' : $page = 'bases/view-generator'; break;
		case 'refinery' : $page = 'bases/view-refinery'; break;
		case 'dock1' : $page = 'bases/view-dock1'; break;
		case 'dock2' : $page = 'bases/view-dock2'; break;
		case 'technosphere' : $page = 'bases/view-technosphere'; break;
		case 'commercialroute' : $page = 'bases/view-commercialplateforme/mode-route'; break;
		case 'sell' : $page = 'bases/view-commercialplateforme/mode-sell'; break;
		case 'school' : $page = 'bases/view-school'; break;
		case 'spatioport' : $page = 'bases/view-spatioport'; break;
	}
	CTR::redirect($page);
}
