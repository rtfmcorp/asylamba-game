<?php

use Asylamba\Modules\Athena\Resource\ShipResource;

$shipsName;
for ($i = 0; $i < 12; $i++) {
	$shipsName[] = "'" . ShipResource::getInfo($i, 'codeName') . "'";
}
$shipsName = implode(', ', $shipsName);

$shipsPev;
for ($i = 0; $i < 12; $i++) {
	$shipsPev[] = ShipResource::getInfo($i, 'pev');
}
$shipsPev = implode(', ', $shipsPev);

	if ($this->getContainer()->getParameter('environment') === 'dev') {
		echo '<script type="text/javascript" src="' . JS . 'jquery1.8.2.min.js"></script>';
	} else {
		echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
	}
	
	echo '<script type="text/javascript">';
		echo 'jQuery(document).ready(function($) {';
			echo 'game = {';
				echo 'path: \'http://' . $this->getContainer()->getParameter('server_host') . '/\',';
				echo 'shipsName: [' . ($shipsName) . '],';
				echo 'shipsPev: [' . $shipsPev . '],';
			echo '};';
		echo '});';
	echo '</script>';
	echo '<script type="text/javascript" src="' . JS . 'main.js"></script>';
	echo '<script type="text/javascript" src="' . JS . 'main.desktop.js"></script>';
	echo '<script type="text/javascript" src="' . JS . 'autocomplete.module.js"></script>';
echo '</body>';
echo '</html>';