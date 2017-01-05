<?php

namespace Asylamba\Modules\Athena\Helper;

use Asylamba\Classes\Container\Session;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;

class ShipHelper {
	/** @var Session **/
	protected $session;
	/** @var OrbitalBaseHelper **/
	protected $orbitalBaseHelper;
	/** @var TechnologyHelper **/
	protected $technologyHelper;
	/** @var ShipQueueManager **/
	protected $shipQueueManager;
	
	/**
	 * @param Session $session
	 * @param OrbitalBaseHelper $orbitalBaseHelper
	 * @param TechnologyHelper $technologyHelper
	 * @param ShipQueueManager $shipQueueManager
	 */
	public function __construct(
		Session $session,
		OrbitalBaseHelper $orbitalBaseHelper,
		TechnologyHelper $technologyHelper,
		ShipQueueManager $shipQueueManager
	)
	{
		$this->session = $session;
		$this->orbitalBaseHelper = $orbitalBaseHelper;
		$this->technologyHelper = $technologyHelper;
		$this->shipQueueManager = $shipQueueManager;
	}

	public function haveRights($shipId, $type, $sup, $quantity = 1) {
		if (ShipResource::isAShip($shipId)) {
			switch ($type) {
				// assez de ressources pour construire ?
				case 'resource' : 
					$price = ShipResource::getInfo($shipId, 'resourcePrice') * $quantity;
					if ($shipId == ShipResource::CERBERE || $shipId == ShipResource::PHENIX) {
						if ($this->session->get('playerInfo')->get('color') == ColorResource::EMPIRE) {
							# bonus if the player is from the Empire
							$price -= round($price * ColorResource::BONUS_EMPIRE_CRUISER / 100);
						}
					}
					return !($sup < $price);
				// assez de points d'action pour construire ?
				case 'pa' : 
					return !($sup < self::getInfo($shipId, 'pa'));
				// encore de la place dans la queue ?
				// $sup est un objet de type OrbitalBase
				// $quantity est le nombre de batiments dans la queue
				case 'queue' :
					if ($this->orbitalBaseHelper->isAShipFromDock1($shipId)) {
						$maxQueue = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $sup->levelDock1, 'nbQueues');
					} elseif ($this->orbitalBaseHelper->isAShipFromDock2($shipId)) {
						$maxQueue = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK2, 'level', $sup->levelDock2, 'nbQueues');
					} else {
						$maxQueue = 0;
					}
					return ($quantity < $maxQueue); 
				// droit de construire le vaisseau ?
				// $sup est un objet de type OrbitalBase
				case 'shipTree' :
					if (ShipResource::isAShipFromDock1($shipId)) {
						$level = $sup->getLevelDock1();
						return ($shipId < $this->orbitalBaseHelper->getBuildingInfo(2, 'level', $level, 'releasedShip'));
					} else if (ShipResource::isAShipFromDock2($shipId)) {
						$level = $sup->getLevelDock2();
						return (($shipId - 6) < $this->orbitalBaseHelper->getBuildingInfo(3, 'level', $level, 'releasedShip'));
					} else {
						$level = $sup->getLevelDock3();
						return (($shipId - 12) < $this->orbitalBaseHelper->getBuildingInfo(4, 'level', $level, 'releasedShip'));
					}
					break;
				// assez de pev dans le storage et dans la queue ?
				// $sup est un objet de type OrbitalBase
				case 'pev' :
					if (ShipResource::isAShipFromDock1($shipId)) {
						//place dans le hangar
						$totalSpace = $this->orbitalBaseHelper->getBuildingInfo(2, 'level', $sup->getLevelDock1(), 'storageSpace');
						//ce qu'il y a dans le hangar
						$storage = $sup->getShipStorage();
						$inStorage = 0;
						for ($i = 0; $i < 6; $i++) {
							$inStorage += ShipResource::getInfo($i, 'pev') * $storage[$i];
						}
						//ce qu'il y a dans la queue
						$inQueue = 0;
						$S_SQM1 = $this->shipQueueManager->getCurrentSession();
						$this->shipQueueManager->changeSession($sup->dock1Manager);
						if ($this->shipQueueManager->size() > 0) {
							for ($i = 0; $i < $this->shipQueueManager->size(); $i++) {
								$inQueue += ShipResource::getInfo($this->shipQueueManager->get($i)->shipNumber, 'pev') * $this->shipQueueManager->get($i)->quantity;
							}
						}
						$this->shipQueueManager->changeSession($S_SQM1);
						//ce qu'on veut rajouter
						$wanted = ShipResource::getInfo($shipId, 'pev') * $quantity;
						//comparaison
						return ($wanted + $inQueue + $inStorage <= $totalSpace);
					} elseif (ShipResource::isAShipFromDock2($shipId)) {
						//place dans le hangar
						$totalSpace = $this->orbitalBaseHelper->getBuildingInfo(3, 'level', $sup->getLevelDock2(), 'storageSpace');
						//ce qu'il y a dans le hangar
						$storage = $sup->getShipStorage();
						$inStorage = 0;
						for ($i = 6; $i < 12; $i++) {
							$inStorage += ShipResource::getInfo($i, 'pev') * $storage[$i];
						}
						//ce qu'il y a dans la queue
						$inQueue = 0;
						$S_SQM2 = $this->shipQueueManager->getCurrentSession();
						$this->shipQueueManager->changeSession($sup->dock2Manager);
						if ($this->shipQueueManager->size() > 0) {
							for ($i = 0; $i < $this->shipQueueManager->size(); $i++) {
								$inQueue += ShipResource::getInfo($this->shipQueueManager->get($i)->shipNumber, 'pev') * 1;
							}
						}
						$this->shipQueueManager->changeSession($S_SQM2);
						//ce qu'on veut rajouter
						$wanted = ShipResource::getInfo($shipId, 'pev') * $quantity;
						//comparaison
						return ($wanted + $inQueue + $inStorage <= $totalSpace);
					} else {
						return TRUE;
					}
					break;
				// a la technologie nécessaire pour constuire ce vaisseau ?
				// $sup est un objet de type Technology
				case 'techno' :
					if ($sup->getTechnology(ShipResource::getInfo($shipId, 'techno')) == 1) {
						return TRUE;
					} else {
						return 'il vous faut développer la technologie ' . $this->technologyHelper->getInfo(ShipResource::getInfo($shipId, 'techno'), 'name');
					}
					break;
				default :
					throw new ErrorException('type invalide dans haveRights de ShipResource');
			}
		} else {
			throw new ErrorException('shipId invalide (entre 0 et 14) dans haveRights de ShipResource');
		}
	}

	public function dockLevelNeededFor($shipId) {
		if (ShipResource::isAShipFromDock1($shipId)) { 
			$building = OrbitalBaseResource::DOCK1; $size = 40; $shipId++;
		} elseif (ShipResource::isAShipFromDock2($shipId)) {
			$building = OrbitalBaseResource::DOCK2; $size = 20; $shipId -= 5;
		} else {
			$building = OrbitalBaseResource::DOCK3; $size = 10; $shipId -= 11;
		}
		for ($i = 0; $i <= $size; $i++) { 
			$relasedShip = $this->orbitalBaseHelper->getBuildingInfo($building, 'level', $i, 'releasedShip');
			if ($relasedShip == $shipId) {
				return $i;
			}
		}
	}
}