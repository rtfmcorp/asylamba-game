<?php
# réglage de l'encodage
header('Content-type: text/html; charset=utf-8');

if (DEVMODE || CTR::$get->exist('password')) {
	ini_set('display_errors', 	TRUE);
	switch (CTR::$get->get('a')) {
		case 'newgalaxy': 			include SCRIPT . 'scripts/newgalaxy.php'; break;
		case 'addbugtracker':		include SCRIPT . 'scripts/addbugtracker.php'; break;
		case 'dump':				include SCRIPT . 'scripts/dump.php'; break;
		case 'apitest':				include SCRIPT . 'scripts/apitest.php'; break;

		default: echo 'Script inconnu ou non-référencé'; break;
	}
} else {
	echo 'Accès refusé';
}
?>