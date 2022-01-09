<?php

use App\Modules\Ares\Model\Commander;

#avance l'attaque de tous les officiers
$commanders = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class)->getMovingCommanders();

foreach ($commanders as $commander) {
	$commander->dArrival = $commander->dStart;
}

$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush(Commander::class);
