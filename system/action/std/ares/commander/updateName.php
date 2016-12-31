<?php

# affect a commander

# int id 	 		id du commandant
# string name 		nom du commandant

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');


if (($commanderId = $request->request->get('id')) === null || ($name = $request->request->get('name')) === null) {
	throw new ErrorException('manque d\'information');
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession();
$commanderManager->load(array('c.id' => $commandantId, 'c.rPlayer' => $session->get('playerId')));
if ($commanderManager->size() !== 1) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$commander = $commanderManager->get();
$p = $this->getContainer()->get('parser');
$name = $p->protect($name);
if (strlen($name) > 1 AND strlen($name) < 26) {
	$commander->setName($name);
	$this->getContainer()->get('app.response')->flashbag->add('le nom de votre commandant est maintenant ' . $name, Response::FLASHBAG_SUCCESS);
} else {
	throw new ErrorException('le nom doit comporter entre 2 et 25 caractères');
}

$commanderManager->changeSession($S_COM1);