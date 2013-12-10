<?php
$base = Utils::getHTTPData('base');

if ($base !== FALSE) {
	if (CTRHelper::baseExist($base)) {
		CTR::$data->get('playerParams')->add('base', $base);
	}
}
?>