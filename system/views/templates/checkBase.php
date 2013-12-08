<?php
# choix de la base
# si base donnée en argument
if (CTR::$get->exist('base')) {
	if (CTRHelper::baseExist(CTR::$get->get('base'))) {
		CTR::$data->get('playerParams')->add('base', CTR::$get->get('base'));
	} else {
		header('Location: ' . APP_ROOT);
		exit();
	}
}
?>