<?php
$base = Utils::getHTTPData('base');
$page = Utils::getHTTPData('page'); # facultatif

if ($base !== FALSE) {
	if (CTRHelper::baseExist($base)) {
		CTR::$data->get('playerParams')->add('base', $base);
	}
}

if ($page !== FALSE) {
	CTR::redirect($page);
}
?>