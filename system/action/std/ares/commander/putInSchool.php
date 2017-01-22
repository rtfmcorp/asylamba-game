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
$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $this->getContainer()->get('app.session')->get('playerId')));

if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$commander = $commanderManager->get();

$orbitalBase = $orbitalBaseManager->get($commander->rBase);

if ($commander->statement == Commander::RESERVE) {
	$S_COM2 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rBase' => $commander->rBase, 'c.statement' => Commander::INSCHOOL));

	if ($commanderManager->size() < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
		$commander->statement = Commander::INSCHOOL;
		$commander->uCommander = Utils::now();
	} else {
		throw new ErrorException('Votre école est déjà pleine.');
	}

	$commanderManager->changeSession($S_COM2);
} elseif ($commander->statement == Commander::INSCHOOL) {
	$commander->statement = Commander::RESERVE;
	$commander->uCommander = Utils::now();
} else {
	throw new ErrorException('Vous ne pouvez rien faire avec cet officier.');
}
$commanderManager->changeSession($S_COM1);