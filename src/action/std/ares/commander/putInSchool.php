<?php

# change of line a commander

# int id 	 		id du commandant

use App\Classes\Library\Utils;
use App\Classes\Exception\ErrorException;
use App\Modules\Ares\Model\Commander;
use App\Modules\Gaia\Resource\PlaceResource;

$commanderId = $this->getContainer()->get('app.request')->query->get('id');
if ($commanderId === null) {
	throw new ErrorException('erreur dans le traitement de la requête');
}
$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('playerId')) {
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
$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
