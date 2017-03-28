<?php

$server = $this->getContainer()->get('server');
$clientManager = $this->getContainer()->get('client_manager');

echo '<h3>Serveur</h3>';
echo ('<p>Mémoire utilisée : ' . memory_get_usage() . '</p>');
echo ('<p>Mémoire allouée : ' . memory_get_usage(true) . '</p>');

echo '<h3>Clients</h3><ul id="clients">';
foreach ($clientManager->clients as $id => $client) {
	echo("<li><pre>");var_dump($client);echo('</pre></li>');
}
echo '</ul>';