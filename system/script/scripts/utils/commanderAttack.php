<?php
#avance l'attaque de tous les officiers
use Asylamba\Modules\Ares\Model\Commander;

$commanderManager = $this->getContainer()->get('ares.commander_manager');

$commanderManager->newSession();
$commanderManager->load(['c.statement' => Commander::MOVING]);

for ($i = 0; $i < $commanderManager->size(); $i++) {
	$commanderManager->get($i)->dArrival = $commanderManager->get($i)->dStart;
}