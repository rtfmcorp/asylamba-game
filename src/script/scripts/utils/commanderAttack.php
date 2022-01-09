<?php

use App\Modules\Ares\Model\Commander;

#avance l'attaque de tous les officiers
$commanders = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class)->getMovingCommanders();

foreach ($commanders as $commander) {
	$commander->dArrival = $commander->dStart;
}

$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush(Commander::class);
