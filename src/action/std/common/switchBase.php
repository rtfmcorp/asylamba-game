<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);

$base = $request->query->get('base');
$page = $request->query->get('page'); # facultatif

if ($base !== FALSE) {
	if ($session->baseExist($base)) {
		$session->get('playerParams')->add('base', $base);
	}
}

if ($page !== null) {
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
	$this->getContainer()->get('app.response')->redirect($page);
} else {
	// otherwise no redirection
}
