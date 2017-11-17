<?php

namespace Asylamba\Modules\Athena\Helper;

use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;

class ShipHelper
{
    /** @var SessionWrapper **/
    protected $sessionWrapper;
    /** @var OrbitalBaseHelper **/
    protected $orbitalBaseHelper;
    /** @var TechnologyHelper **/
    protected $technologyHelper;
    /** @var ShipQueueManager **/
    protected $shipQueueManager;
    
    /**
     * @param SessionWrapper $session
     * @param OrbitalBaseHelper $orbitalBaseHelper
     * @param TechnologyHelper $technologyHelper
     * @param ShipQueueManager $shipQueueManager
     */
    public function __construct(
        SessionWrapper $session,
        OrbitalBaseHelper $orbitalBaseHelper,
        TechnologyHelper $technologyHelper,
        ShipQueueManager $shipQueueManager
    ) {
        $this->sessionWrapper = $session;
        $this->orbitalBaseHelper = $orbitalBaseHelper;
        $this->technologyHelper = $technologyHelper;
        $this->shipQueueManager = $shipQueueManager;
    }

    public function haveRights($shipId, $type, $sup, $quantity = 1)
    {
        if (ShipResource::isAShip($shipId)) {
            switch ($type) {
                // assez de ressources pour construire ?
                case 'resource':
                    $price = ShipResource::getInfo($shipId, 'resourcePrice') * $quantity;
                    if ($shipId == ShipResource::CERBERE || $shipId == ShipResource::PHENIX) {
                        if ($this->sessionWrapper->get('playerInfo')->get('color') == ColorResource::EMPIRE) {
                            # bonus if the player is from the Empire
                            $price -= round($price * ColorResource::BONUS_EMPIRE_CRUISER / 100);
                        }
                    }
                    return !($sup < $price);
                // assez de points d'action pour construire ?
                case 'pa':
                    return !($sup < self::getInfo($shipId, 'pa'));
                // encore de la place dans la queue ?
                // $sup est un objet de type OrbitalBase
                // $quantity est le nombre de batiments dans la queue
                case 'queue':
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
                case 'shipTree':
                    if (ShipResource::isAShipFromDock1($shipId)) {
                        $level = $sup->getLevelDock1();
                        return ($shipId < $this->orbitalBaseHelper->getBuildingInfo(2, 'level', $level, 'releasedShip'));
                    } elseif (ShipResource::isAShipFromDock2($shipId)) {
                        $level = $sup->getLevelDock2();
                        return (($shipId - 6) < $this->orbitalBaseHelper->getBuildingInfo(3, 'level', $level, 'releasedShip'));
                    } else {
                        $level = $sup->getLevelDock3();
                        return (($shipId - 12) < $this->orbitalBaseHelper->getBuildingInfo(4, 'level', $level, 'releasedShip'));
                    }
                    break;
                // assez de pev dans le storage et dans la queue ?
                // $sup est un objet de type OrbitalBase
                case 'pev':
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
                        $shipQueues = $this->shipQueueManager->getByBaseAndDockType($sup->rPlace, 1);
                        foreach ($shipQueues as $shipQueue) {
                            $inQueue += ShipResource::getInfo($shipQueue->shipNumber, 'pev') * $shipQueue->quantity;
                        }
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
                        $shipQueues = $this->shipQueueManager->getByBaseAndDockType($sup->rPlace, 2);
                        foreach ($shipQueues as $shipQueue) {
                            $inQueue += ShipResource::getInfo($shipQueue->shipNumber, 'pev') * 1;
                        }
                        //ce qu'on veut rajouter
                        $wanted = ShipResource::getInfo($shipId, 'pev') * $quantity;
                        //comparaison
                        return ($wanted + $inQueue + $inStorage <= $totalSpace);
                    } else {
                        return true;
                    }
                    break;
                // a la technologie nécessaire pour constuire ce vaisseau ?
                // $sup est un objet de type Technology
                case 'techno':
                    if ($sup->getTechnology(ShipResource::getInfo($shipId, 'techno')) == 1) {
                        return true;
                    } else {
                        return 'il vous faut développer la technologie ' . $this->technologyHelper->getInfo(ShipResource::getInfo($shipId, 'techno'), 'name');
                    }
                    break;
                default:
                    throw new ErrorException('type invalide dans haveRights de ShipResource');
            }
        } else {
            throw new ErrorException('shipId invalide (entre 0 et 14) dans haveRights de ShipResource');
        }
    }

    public function dockLevelNeededFor($shipId)
    {
        if (ShipResource::isAShipFromDock1($shipId)) {
            $building = OrbitalBaseResource::DOCK1;
            $size = 40;
            $shipId++;
        } elseif (ShipResource::isAShipFromDock2($shipId)) {
            $building = OrbitalBaseResource::DOCK2;
            $size = 20;
            $shipId -= 5;
        } else {
            $building = OrbitalBaseResource::DOCK3;
            $size = 10;
            $shipId -= 11;
        }
        for ($i = 0; $i <= $size; $i++) {
            $relasedShip = $this->orbitalBaseHelper->getBuildingInfo($building, 'level', $i, 'releasedShip');
            if ($relasedShip == $shipId) {
                return $i;
            }
        }
    }
}
