<?php

# change of line a commander

# int id 	 		id du commandant

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Resource\PlaceResource;

$commanderId = $this->getContainer()->get('app.request')->query->get('id');
if ($commanderId === null) {
    throw new ErrorException('erreur dans le traitement de la requête');
}
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $this->getContainer()->get('session_wrapper')->get('playerId')) {
    throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$orbitalBase = $orbitalBaseManager->get($commander->rBase);

if ($commander->statement == Commander::RESERVE) {
    $commanders = $commanderManager->getBaseCommanders($commander->rBase, [Commander::INSCHOOL]);

    if (count($commanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
        $commander->statement = Commander::INSCHOOL;
        $commander->uCommander = Utils::now();
    } else {
        throw new ErrorException('Votre école est déjà pleine.');
    }
} elseif ($commander->statement == Commander::INSCHOOL) {
    $commander->statement = Commander::RESERVE;
    $commander->uCommander = Utils::now();
} else {
    throw new ErrorException('Vous ne pouvez rien faire avec cet officier.');
}
$this->getContainer()->get('entity_manager')->flush();
