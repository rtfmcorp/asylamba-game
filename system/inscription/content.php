<?php

$request = $this->getContainer()->get('app.request');

// choix des étapes
if ($request->query->get('step') == 1 || !$request->query->has('step')) {
	if (!$request->query->has('bindkey')) {
		include INSCRIPTION . 'step/ally.php';
	}
} elseif ($request->query->get('step') == 2) {
	include INSCRIPTION . 'step/profil.php';
} elseif ($request->query->get('step') == 3) {
	include INSCRIPTION . 'step/place.php';
} elseif ($request->query->get('step') == 4) {
	include INSCRIPTION . 'step/save.php';
}