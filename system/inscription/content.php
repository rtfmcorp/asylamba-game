<?php

$request = $this->getContainer()->get('app.request');
$registrationPath = $this->getContainer()->getParameter('inscription');

// choix des étapes
if ($request->query->get('step') == 1 || !$request->query->has('step')) {
	if (!$request->query->has('bindkey')) {
		include $registrationPath . 'step/ally.php';
	}
} elseif ($request->query->get('step') == 2) {
	include $registrationPath . 'step/profil.php';
} elseif ($request->query->get('step') == 3) {
	include $registrationPath . 'step/place.php';
} elseif ($request->query->get('step') == 4) {
	include $registrationPath . 'step/save.php';
}
