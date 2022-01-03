<?php

use Asylamba\Modules\Athena\Resource\ShipResource;

$container = $this->getContainer();
$jsPath = $container->getParameter('js');

$getJsHost = function () use ($container) {
	$serverHost = $container->getParameter('server_host');

	return ($container->hasParameter('server_port') && 80 !== ($serverPort = $container->getParameter('server_port')))
		? \sprintf('%s:%s', $serverHost, $serverPort)
		: $serverHost;
};

$shipsName = [];
for ($i = 0; $i < 12; $i++) {
	$shipsName[] = "'" . ShipResource::getInfo($i, 'codeName') . "'";
}
$shipsName = implode(', ', $shipsName);

$shipsPev = [];
for ($i = 0; $i < 12; $i++) {
	$shipsPev[] = ShipResource::getInfo($i, 'pev');
}
$shipsPev = implode(', ', $shipsPev);

	if ($this->getContainer()->getParameter('environment') === 'dev') {
		echo '<script type="text/javascript" src="' . $jsPath . 'jquery1.8.2.min.js"></script>';
	} else {
		echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
	}
	
	echo '<script type="text/javascript">';
		echo 'jQuery(document).ready(function($) {';
			echo 'game = {';
				echo 'path: \'http://' . $getJsHost() . '/\',';
				echo 'shipsName: [' . ($shipsName) . '],';
				echo 'shipsPev: [' . $shipsPev . '],';
			echo '};';
		echo '});';
	echo '</script>';
	echo '<script type="text/javascript" src="' . $jsPath . 'main.js"></script>';
	echo '<script type="text/javascript" src="' . $jsPath . 'main.desktop.js"></script>';
	echo '<script type="text/javascript" src="' . $jsPath . 'autocomplete.module.js"></script>';
echo '</body>';
echo '</html>';
