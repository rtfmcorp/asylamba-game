<?php

# affect a commander

# int id 	 		id du commandant
# string name 		nom du commandant

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');


if (($commanderId = $request->request->get('id')) === null || ($name = $request->request->get('name')) === null) {
	throw new ErrorException('manque d\'information');
}

$commanderManager = $this->getContainer()->get('ares.commander_manager');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$p = $this->getContainer()->get('parser');
$name = $p->protect($name);
if (strlen($name) > 1 AND strlen($name) < 26) {
	$commander->setName($name);
	$session->addFlashbag('le nom de votre commandant est maintenant ' . $name, Flashbag::TYPE_SUCCESS);
} else {
	throw new ErrorException('le nom doit comporter entre 2 et 25 caractères');
}