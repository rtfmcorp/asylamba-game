<?php

# affect a commander

# int id 	 		id du commandant
# string name 		nom du commandant

use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);


if (($commanderId = $request->request->get('id')) === null || ($name = $request->request->get('name')) === null) {
	throw new ErrorException('manque d\'information');
}

$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
	throw new ErrorException('Ce commandant n\'existe pas ou ne vous appartient pas');
}
$p = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$name = $p->protect($name);
if (strlen($name) > 1 AND strlen($name) < 26) {
	$commander->setName($name);
	$session->addFlashbag('le nom de votre commandant est maintenant ' . $name, Flashbag::TYPE_SUCCESS);
} else {
	throw new ErrorException('le nom doit comporter entre 2 et 25 caractères');
}
