<?php

namespace Asylamba\Modules\Athena\Helper;

use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;

class OrbitalBaseHelper
{
    /** @var TechnologyHelper **/
    protected $technologyHelper;
    
    /**
     * @param TechnologyHelper $technologyHelper
     */
    public function __construct(TechnologyHelper $technologyHelper)
    {
        $this->technologyHelper = $technologyHelper;
    }
    
    public function isABuilding($building)
    {
        return in_array($building, OrbitalBaseResource::$orbitalBaseBuildings);
    }

    public function isAShipFromDock1($ship)
    {
        return in_array($ship, OrbitalBaseResource::$dock1Ships);
    }

    public function isAShipFromDock2($ship)
    {
        return in_array($ship, OrbitalBaseResource::$dock2Ships);
    }

    public function isAShipFromDock3($ship)
    {
        return in_array($ship, OrbitalBaseResource::$dock3Ships);
    }

    public function fleetQuantity($typeOfBase)
    {
        switch ($typeOfBase) {
            case OrbitalBase::TYP_NEUTRAL:
                return 2; break;
            case OrbitalBase::TYP_COMMERCIAL:
                return 2; break;
            case OrbitalBase::TYP_MILITARY:
                return 5; break;
            case OrbitalBase::TYP_CAPITAL:
                return 5; break;
            default:
                return 0; break;
        }
    }

    public function getInfo($buildingNumber, $info, $level = 0, $sup = 'default')
    {
        return $this->getBuildingInfo($buildingNumber, $info, $level, $sup);
    }
    
    public function getBuildingInfo($buildingNumber, $info, $level = 0, $sup = 'default')
    {
        if ($this->isABuilding($buildingNumber)) {
            if (in_array($info, ['name', 'column', 'frenchName', 'imageLink', 'description'])) {
                return OrbitalBaseResource::$building[$buildingNumber][$info];
            } elseif ($info == 'techno') {
                if (in_array($buildingNumber, array(3,4,6,8,9))) {
                    return OrbitalBaseResource::$building[$buildingNumber][$info];
                } else {
                    return -1;
                }
            } elseif ($info == 'maxLevel') {
                # $level is the type of the base
                return OrbitalBaseResource::$building[$buildingNumber][$info][$level];
            } elseif ($info == 'level') {
                if ($level <= 0 or $level > count(OrbitalBaseResource::$building[$buildingNumber]['level'])) {
                    return false;
                }
                if ($sup == 'time') {
                    return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][0];
                } elseif ($sup == 'resourcePrice') {
                    return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][1];
                } elseif ($sup == 'points') {
                    return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][2];
                } else {
                    if ($sup == 'nbQueues') {
                        if ($buildingNumber == 0 or $buildingNumber == 2 or $buildingNumber == 3 or $buildingNumber == 5) {
                            return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                        }
                    } elseif ($sup == 'storageSpace') {
                        if ($buildingNumber == 7) {
                            return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                        } elseif ($buildingNumber == 2 or $buildingNumber == 3) {
                            return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][4];
                        }
                    } elseif ($sup == 'refiningCoefficient' and $buildingNumber == 1) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                    } elseif ($sup == 'releasedShip' and ($buildingNumber == 2 or $buildingNumber == 3)) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][5];
                    } elseif ($sup == 'releasedShip' and $buildingNumber == 4) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][4];
                    } elseif ($sup == 'nbCommercialShip' and $buildingNumber == 6) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                    } elseif ($sup == 'nbRecyclers' and $buildingNumber == 8) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                    } elseif ($sup == 'nbRoutesMax' and $buildingNumber == 9) {
                        return OrbitalBaseResource::$building[$buildingNumber][$info][$level-1][3];
                    } else {
                        throw new ErrorException('4e argument invalide dans getBuildingInfo de OrbitalBaseResource');
                    }
                }
            } else {
                throw new ErrorException('2e argument invalide dans getBuildingInfo de OrbitalBaseResource');
            }
        } else {
            throw new ErrorException('1er argument invalide (entre 0 et 7) dans getBuildingInfo de OrbitalBaseResource');
        }
        return false;
    }

    public function haveRights($buildingId, $level, $type, $sup)
    {
        if ($this->isABuilding($buildingId)) {
            switch ($type) {
                // assez de ressources pour contruire ?
                case 'resource':
                    return ($sup < $this->getBuildingInfo($buildingId, 'level', $level, 'resourcePrice')) ? false : true;
                    break;
                // encore de la place dans la queue ?
                // $sup est le nombre de batiments dans la queue
                case 'queue':
                    // $buildingId n'est pas utilisé
                    return ($sup < $this->getBuildingInfo($buildingId, 'level', $level, 'nbQueues')) ? true : false;
                    break;
                // droit de construire le batiment ?
                // $sup est un objet de type OrbitalBase
                case 'buildingTree':
                    $diminution = null;
                    switch ($buildingId) {
                        case OrbitalBaseResource::GENERATOR:
                            $diminution = 0;
                            break;
                        case OrbitalBaseResource::REFINERY:
                            $diminution = 0;
                            break;
                        case OrbitalBaseResource::DOCK1:
                            $diminution = 0;
                            break;
                        case OrbitalBaseResource::DOCK2:
                            $diminution = 20;
                            break;
                        case OrbitalBaseResource::DOCK3:
                            $diminution = 30;
                            break;
                        case OrbitalBaseResource::TECHNOSPHERE:
                            $diminution = 0;
                            break;
                        case OrbitalBaseResource::COMMERCIAL_PLATEFORME:
                            $diminution = 10;
                            break;
                        case OrbitalBaseResource::STORAGE:
                            $diminution = 0;
                            break;
                        case OrbitalBaseResource::RECYCLING:
                            $diminution = 10;
                            break;
                        case OrbitalBaseResource::SPATIOPORT:
                            $diminution = 20;
                            break;
                        default:
                            throw new ErrorException('buildingId invalide (entre 0 et 9) dans haveRights de OrbitalBaseResource');
                    }
                    if ($diminution !== null) {
                        if ($buildingId == OrbitalBaseResource::GENERATOR) {
                            if ($level > OrbitalBaseResource::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
                                return 'niveau maximum atteint';
                            } else {
                                return true;
                            }
                        } else {
                            if ($level == 1 and $sup->typeOfBase == OrbitalBase::TYP_NEUTRAL and ($buildingId == OrbitalBaseResource::SPATIOPORT or $buildingId == OrbitalBaseResource::DOCK2)) {
                                return 'vous devez évoluer votre colonie pour débloquer ce bâtiment';
                            }
                            if ($level > OrbitalBaseResource::$building[$buildingId]['maxLevel'][$sup->typeOfBase]) {
                                return 'niveau maximum atteint';
                            } elseif ($level > ($sup->realGeneratorLevel - $diminution)) {
                                return 'le niveau du générateur n\'est pas assez élevé';
                            } else {
                                return true;
                            }
                        }
                    }
                    break;
                // a la technologie pour construire ce bâtiment ?
                // $sup est un objet de type Technology
                case 'techno':
                    if ($this->getBuildingInfo($buildingId, 'techno') == -1) {
                        return true;
                    }
                    if ($sup->getTechnology($this->getBuildingInfo($buildingId, 'techno')) == 1) {
                        return true;
                    } else {
                        return 'il vous faut développer la technologie ' . $this->technologyHelper->getInfo($this->getBuildingInfo($buildingId, 'techno'), 'name');
                    }
                    break;
                default:
                    throw new ErrorException('$type invalide (entre 1 et 4) dans haveRights de OrbitalBaseResource');
            }
        } else {
            throw new ErrorException('buildingId invalide (entre 0 et 9) dans haveRights de OrbitalBaseResource');
        }
    }
}
