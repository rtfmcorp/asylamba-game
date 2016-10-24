<?php

use Asylamba\Classes\Worker\CTR;

// choix des étapes
if (CTR::$get->get('step') == 1 || !CTR::$get->exist('step')) {
	if (!CTR::$get->exist('bindkey')) {
		include INSCRIPTION . 'step/ally.php';
	}
} elseif (CTR::$get->get('step') == 2) {
	include INSCRIPTION . 'step/profil.php';
} elseif (CTR::$get->get('step') == 3) {
	include INSCRIPTION . 'step/place.php';
} elseif (CTR::$get->get('step') == 4) {
	include INSCRIPTION . 'step/save.php';
}