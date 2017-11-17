<?php

use Asylamba\Modules\Ares\Model\Commander;

#avance l'attaque de tous les officiers
$commanders = $this->getContainer()->get('ares.commander_manager')->getMovingCommanders();

foreach ($commanders as $commander) {
    $commander->dArrival = $commander->dStart;
}

$this->getContainer()->get('entity_manager')->flush(Commander::class);
