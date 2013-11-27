<?php
$query = API::unParse($_SERVER['REQUEST_URI']);
$query = explode('/', $query);

foreach ($query as $q) {
	$args = explode('-', $q);

	if (count($args) == 2) {
		CTR::$get->add($args[0], $args[1]);
	}
}

# réglage de l'encodage
header('Content-type: text/html; charset=utf-8');

if (DEVMODE || CTR::$get->exist('password')) {
	switch (CTR::$get->get('a')) {
		# case 'ban': 				include API . 'apis/ban.php'; break;

		default:
		echo serialize(array(
			'statement' => 'error',
			'message' => 'API non reconnue par le système'
		));
		break;
	}
} else {
	echo serialize(array(
		'statement' => 'error',
		'message' => 'Accès refusé'
	));
}
?>