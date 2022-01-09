<?php
# switch dock mode (production/storage) action

# int baseId 		id de la base orbitale
# int dock 			dock number

use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$dock = $request->query->get('dock');


if ($baseId !== FALSE AND $dock !== FALSE AND in_array($baseId, $verif)) { 
	if (($base = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) === null) {
		$cancel = TRUE;
		throw new ErrorException('modification du mode du dock impossible - base inconnue');
	}
	switch ($dock) {
		case 1:
			if ($base->getIsProductionDock1() == 1) {
				$base->setIsProductionDock1(0);
			} else {
				$base->setIsProductionDock1(1);
			}
			break;
		case 2:
			if ($base->getIsProductionDock2() == 1) {
				$base->setIsProductionDock2(0);
			} else {
				$base->setIsProductionDock2(1);
			}
			break;
		default:
			throw new ErrorException('modification du mode du dock impossible - dock inconnue');
			break;
	}
} else {
	throw new FormException('pas assez d\'informations pour changer le mode du dock');
}
