<?php

use Asylamba\Classes\Library\Utils;

$server = $this->getContainer()->get('server');
$clientManager = $this->getContainer()->get('client_manager');
$rtc = $this->getContainer()->get('realtime_action_scheduler');
$processManager = $this->getContainer()->get('process_manager');
$memoryManager = $this->getContainer()->get('memory_manager');

$memoryManager->refreshNodeMemory();
$masterData = $memoryManager->getNodeMemory();
$poolData = $memoryManager->getPoolMemory();
echo '<h3>Serveur</h3>';
echo ('<p>Mémoire utilisée au pool: ' . $poolData['allocated_memory'] . 'b</p>');
echo ('<p>Mémoire allouée au pool: ' . $poolData['memory'] . 'b</p>');
echo ('<p>Mémoire utilisée au master: ' . $masterData['allocated_memory'] . 'b</p>');
echo ('<p>Mémoire allouée au master: ' . $masterData['memory'] . 'b</p>');
echo ('<p>Heure : ' . Utils::now() . '</p>');

echo ('<h3>Process</h3>');
echo '<div style="display:flex;justify-content:space-around;">';
foreach ($processManager->getProcesses() as $process) {
	$tasks = $process->getTasks();
	echo ("<div><ul><li>Name : {$process->getName()}</li>");
	echo "<li>Mémoire allouée: {$process->getAllocatedMemory()}b</li>";
	echo "<li>Mémoire utilisée: {$process->getMemory()}</li>";
	echo "<li>Temps estimé de travail: {$process->getExpectedWorkTime()}</li>";
	echo ("<li>Tasks ".count($tasks)."</li>");
	foreach($tasks as $task) {
		var_dump($task);
	}
	echo ('</ul></div>');
}
echo '</div>';
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
