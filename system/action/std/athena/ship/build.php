<?php
include_once ATHENA;
include_once PROMETHEE;
# build ship action

# int baseid 		id (rPlace) de la base orbitale
# int ship 			id du vaisseau
# int quantity 		nombre de vaisseaux à construire

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('ship')) {
	$ship = CTR::$get->get('ship');
} elseif (CTR::$post->exist('ship')) {
	$ship = CTR::$post->get('ship');
} else {
	$ship = FALSE;
}
if (CTR::$get->exist('quantity')) {
	$quantity = CTR::$get->get('quantity');
} elseif (CTR::$post->exist('quantity')) {
	$quantity = CTR::$post->get('quantity');
} else {
	$quantity = FALSE;
}

if ($baseId !== FALSE AND $ship !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif) AND $quantity != 0) { 
	if (ShipResource::isAShip($ship)) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId));
		$ob  = ASM::$obm->get();
		if (OrbitalBaseResource::isAShipFromDock1($ship)) {
			$dockType = 1;
		} elseif (OrbitalBaseResource::isAShipFromDock2($ship)) {
			$dockType = 2;
			$quantity = 1;
		} else {
			$dockType = 3;
			$quantity = 1;
		}
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->newSession(ASM_UMODE);
		ASM::$sqm->load(array('rOrbitalBase' => $baseId, 'dockType' => $dockType), array('position'));
		$technos = new Technology(CTR::$data->get('playerId'));
		if (ShipResource::haveRights($ship, 'resource', $ob->getResourcesStorage(), $quantity)
			AND ShipResource::haveRights($ship, 'queue', ASM::$sqm->size())
			AND ShipResource::haveRights($ship, 'shipTree', $ob)
			AND ShipResource::haveRights($ship, 'pev', $ob, $quantity)
			AND ShipResource::haveRights($ship, 'techno', $technos)) {
			// remet à jour les positions des queues
			if (ASM::$sqm->size() > 0) {
				for ($i = 0; $i < ASM::$sqm->size(); $i++) {
					$sq = ASM::$sqm->get($i);
					$sq->setPosition($i + 1);
				}
			}
			// construit le(s) nouveau(x) vaisseau(x)
			$sq = new ShipQueue();
			$sq->setROrbitalBase($baseId);
			$sq->setDockType($dockType);
			$sq->setShipNumber($ship);
			$sq->setQuantity($quantity);

			$time = ShipResource::getInfo($ship, 'time') * $quantity;
			switch ($dockType) {
				case 1:
					$playerBonus = PlayerBonus::DOCK1_SPEED;
					break;
				case 2:
					$playerBonus = PlayerBonus::DOCK2_SPEED;
					break;
				case 3:
					$playerBonus = PlayerBonus::DOCK3_SPEED;
					break;
			}
			$bonus = $time * CTR::$data->get('playerBonus')->get($playerBonus) / 100;
			$sq->setRemainingTime(round($time - $bonus));
			$sq->setPosition(ASM::$sqm->size() + 1);
			ASM::$sqm->add($sq);
			// débit des ressources au joueur
			$ob->decreaseResources(ShipResource::getInfo($ship, 'resourcePrice') * $quantity);

			// ajout de l'event dans le contrôleur
			$date = Utils::now();
			for ($i = 0; $i < ASM::$sqm->size(); $i++) { 
				$date = Utils::addSecondsToDate($date, ASM::$sqm->get($i)->getRemainingTime());
			}
			CTR::$data->get('playerEvent')->add($date, EVENT_BASE, $baseId);

			// alerte
			if ($quantity == 1) {
				CTR::$alert->add('Construction d\'' . (ShipResource::isAFemaleShipName($ship) ? 'une ' : 'un ') . ShipResource::getInfo($ship, 'codeName') . ' commandée', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('Construction de ' . $quantity . ' ' . ShipResource::getInfo($ship, 'codeName') . Format::addPlural($quantity) . ' commandée', ALERT_STD_SUCCESS);
			}
		} else {
			CTR::$alert->add('les conditions ne sont pas remplies pour construire ce vaisseau', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
		ASM::$sqm->changeSession($S_SQM1);
	} else {
		CTR::$alert->add('construction de vaisseau impossible - vaisseau inconnu', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour construire un vaisseau', ALERT_STD_FILLFORM);
}
?>