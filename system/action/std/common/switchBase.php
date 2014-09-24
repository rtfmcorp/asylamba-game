<?php
$base = Utils::getHTTPData('base');
$page = Utils::getHTTPData('page'); # facultatif

if ($base !== FALSE) {
	if (CTRHelper::baseExist($base)) {
		CTR::$data->get('playerParams')->add('base', $base);
	}
}

if ($page !== FALSE) {
	switch ($page) {
		case 'generator' : $page = 'bases/view-generator'; break;
		case 'refinery' : $page = 'bases/view-refinery'; break;
		case 'dock1' : $page = 'bases/view-dock1'; break;
		case 'technosphere' : $page = 'bases/view-technosphere'; break;
	}
	CTR::redirect($page);
}
?>