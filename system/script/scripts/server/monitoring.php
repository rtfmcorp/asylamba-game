<?php

use Asylamba\Classes\Library\Utils;

$server = $this->getContainer()->get('server');
$clientManager = $this->getContainer()->get('client_manager');
$rtc = $this->getContainer()->get('realtime_action_scheduler');

echo '<h3>Serveur</h3>';
echo ('<p>Mémoire utilisée : ' . memory_get_usage() . '</p>');
echo ('<p>Mémoire allouée : ' . memory_get_usage(true) . '</p>');
echo ('<p>Heure : ' . Utils::now() . '</p>');

echo '<div style="display:flex;justify-content:space-between;">';
echo '<div><h3>Schedulers</h3><h4> Real Time Action Scheduler<ul>';
foreach ($rtc->getQueue() as $date => $elements)
{
	echo '<li><pre>';
	var_dump($date);
	var_dump($elements);
	echo '</pre></li>';
}
echo '</ul></div>';

echo '<div><h3>Clients</h3><ul id="clients">';
foreach ($clientManager->clients as $id => $client) {
	echo("<li><pre>");var_dump($client);echo('</pre></li>');
}
echo '</ul></div></div>';
