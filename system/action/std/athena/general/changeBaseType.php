<?php
# change the type of the base action

# int baseid 		id of the orbital base
# int type			new type for the base

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$recyclingLogManager = $this->getContainer()->get('athena.recycling_log_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$database = $this->getContainer()->get('database');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$baseMinLevelForChange = $this->getContainer()->getParameter('athena.obm.change_type_min_level');
$baseMinLevelForCapital = $this->getContainer()->getParameter('athena.obm.capital_min_level');
$entityManager = $this->getContainer()->get('entity_manager');
$eventDispatcher = $this->getContainer()->get('event_dispatcher');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$type = $request->query->get('type');


if ($baseId !== false and $type !== false and in_array($baseId, $verif)) {
    if (($orbitalBase = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
        $player = $playerManager->get($session->get('playerId'));

        if ($orbitalBase->typeOfBase == OrbitalBase::TYP_NEUTRAL) {
            if ($orbitalBase->levelGenerator >= $baseMinLevelForChange) {
                switch ($type) {
                    case OrbitalBase::TYP_COMMERCIAL:
                        $totalPrice = PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price');
                        if ($player->credit >= $totalPrice) {
                            $orbitalBase->typeOfBase = $type;
                            $playerManager->decreaseCredit($player, $totalPrice);

                            # change base type in session
                            for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
                                if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
                                    $session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_COMMERCIAL);
                                    break;
                                }
                            }
                            if (DATA_ANALYSIS) {
                                $qr = $database->prepare(
                                    'INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
                                );
                                $qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
                            }

                            $session->addFlashbag($orbitalBase->name . ' est désormais un Centre Industriel', Flashbag::TYPE_SUCCESS);
                        } else {
                            throw new ErrorException('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits');
                        }
                        break;
                    case OrbitalBase::TYP_MILITARY:
                        $totalPrice = PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price');
                        if ($player->credit >= $totalPrice) {
                            $orbitalBase->typeOfBase = $type;
                            $playerManager->decreaseCredit($player, $totalPrice);

                            # change base type in session
                            for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
                                if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
                                    $session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_MILITARY);
                                    break;
                                }
                            }

                            if (DATA_ANALYSIS) {
                                $qr = $database->prepare(
                                    'INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
                                );
                                $qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
                            }

                            $session->addFlashbag($orbitalBase->name . ' est désormais une Base Militaire', Flashbag::TYPE_SUCCESS);
                        } else {
                            throw new ErrorException('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits');
                        }
                        break;
                    default:
                        throw new ErrorException('Modification du type de la base orbitale impossible (seulement commercial ou militaire)');
                }
            } else {
                throw new ErrorException('Evolution de votre colonie impossible - niveau du générateur pas assez élevé');
            }
        } elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL or $orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY) {
            if ($type == OrbitalBase::TYP_CAPITAL) {
                if ($orbitalBase->levelGenerator >= $baseMinLevelForCapital) {
                    $playerBases = $orbitalBaseManager->getPlayerBases($session->get('playerId'));
                    
                    $capitalQuantity = 0;
                    foreach ($playerBases as $playerBase) {
                        if ($playerBase->typeOfBase == OrbitalBase::TYP_CAPITAL) {
                            $capitalQuantity++;
                        }
                    }
                    if ($capitalQuantity == 0) {
                        $totalPrice = PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'price');
                        if ($player->credit >= $totalPrice) {
                            $orbitalBase->typeOfBase = $type;
                            $playerManager->decreaseCredit($player, $totalPrice);

                            # change base type in session
                            for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
                                if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
                                    $session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_CAPITAL);
                                    break;
                                }
                            }

                            if (DATA_ANALYSIS) {
                                $qr = $database->prepare(
                                    'INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
                                );
                                $qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
                            }
                            $session->addFlashbag($orbitalBase->name . ' est désormais une capitale.', Flashbag::TYPE_SUCCESS);
                        } else {
                            throw new ErrorException('Modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits');
                        }
                    } else {
                        throw new ErrorException('Vous ne pouvez pas avoir plus d\'une Capitale. Sauf si vous en conquérez à vos ennemis bien sûr.');
                    }
                } else {
                    throw new ErrorException('Pour transformer votre base en capitale, vous devez augmenter votre générateur jusqu\'au niveau ' . $baseMinLevelForCapital . '.');
                }
            } elseif (($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL and $type == OrbitalBase::TYP_MILITARY)
                or ($orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY and $type == OrbitalBase::TYP_COMMERCIAL)) {
                # commercial --> military OR military --> commercial
                if ($type == OrbitalBase::TYP_COMMERCIAL) {
                    $totalPrice = PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price');
                } else {
                    $totalPrice = PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price');
                }
                if ($player->credit >= $totalPrice) {
                    $canChangeBaseType = true;
                    if ($type == OrbitalBase::TYP_COMMERCIAL) {
                        # delete all recycling missions and logs
                        $recyclingMissionManager->removeBaseMissions($orbitalBase->rPlace);

                        # verify if fleets are moving or not
                        # transfer to the mess the extra commanders and change line if needed
                        $firstLineCommanders = $commanderManager->getCommandersByLine($orbitalBase->rPlace, 1);
                        $totalQtyLine1 = count($firstLineCommanders);
                        $movingQtyLine1 = 0;
                        foreach ($firstLineCommanders as $commander) {
                            if ($commander->statement == Commander::MOVING) {
                                $movingQtyLine1++;
                            }
                        }
                        $secondLineCommanders = $commanderManager->getCommandersByLine($orbitalBase->rPlace, 2);
                        $totalQtyLine2 = count($secondLineCommanders);
                        $movingQtyLine2 = 0;
                        foreach ($secondLineCommanders as $commander) {
                            if ($commander->statement == Commander::MOVING) {
                                $movingQtyLine2++;
                            }
                        }

                        $totalQty = $totalQtyLine1 + $totalQtyLine2;
                        $movingQty = $movingQtyLine1 + $movingQtyLine2;

                        if ($totalQty >= 2) {
                            switch ($movingQty) {
                                case 2:
                                    $line1 = false;
                                    $line2 = false;
                                    foreach ($firstLineCommanders as $commander) {
                                        if ($commander->statement == Commander::MOVING) {
                                            if ($line1) {
                                                # move to line 2
                                                $commander->line = 2;
                                                $line2 = true;
                                                continue;
                                            }
                                            # stay on line 1
                                            $line1 = true;
                                            continue;
                                        }
                                        # move to the mess
                                        $commander->statement = Commander::RESERVE;
                                        $commanderManager->emptySquadrons($commander);
                                    }
                                    foreach ($secondLineCommanders as $commander) {
                                        if ($commander->statement == Commander::MOVING) {
                                            if ($line2) {
                                                # move to line 1
                                                $commander->line = 1;
                                                $line1 = true;
                                                continue;
                                            }
                                            # stay on line 2
                                            $line2 = true;
                                            continue;
                                        }
                                        # move to the mess
                                        $commander->statement = Commander::RESERVE;
                                        $commanderManager->emptySquadrons($commander);
                                    }
                                    break;
                                case 1:
                                    if ($movingQtyLine1 == 1) {
                                        if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
                                            // let stay one cmder on each line
                                            foreach ($firstLineCommanders as $commander) {
                                                if ($commander->statement != Commander::MOVING) {
                                                    # move to the mess
                                                    $commander->statement = Commander::RESERVE;
                                                    $commanderManager->emptySquadrons($commander);
                                                }
                                            }
                                            $line2 = false;
                                            foreach ($secondLineCommanders as $commander) {
                                                if (!$line2) {
                                                    $line2 = true;
                                                } else {
                                                    # move to the mess
                                                    $commander->statement = Commander::RESERVE;
                                                    $commanderManager->emptySquadrons($commander);
                                                }
                                            }
                                        } else {
                                            // change line of one from line 1 to 2
                                            $line2 = false;
                                            foreach ($firstLineCommanders as $commander) {
                                                if ($commander->statement != Commander::MOVING) {
                                                    if (!$line2) {
                                                        $line2 = true;
                                                    } else {
                                                        # move to the mess
                                                        $commander->statement = Commander::RESERVE;
                                                        $commanderManager->emptySquadrons($commander);
                                                    }
                                                }
                                            }
                                        }
                                    } else { # $movingQtyLine2 == 1
                                        if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
                                            // let stay one cmder on each line
                                            foreach ($secondLineCommanders as $commander) {
                                                if ($commander->statement != Commander::MOVING) {
                                                    # move to the mess
                                                    $commander->statement = Commander::RESERVE;
                                                    $commanderManager->emptySquadrons($commander);
                                                }
                                            }
                                            $line1 = false;
                                            foreach ($firstLineCommanders as $commander) {
                                                if (!$line1) {
                                                    $line1 = true;
                                                } else {
                                                    # move to the mess
                                                    $commander->statement = Commander::RESERVE;
                                                    $commanderManager->emptySquadrons($commander);
                                                }
                                            }
                                        } else {
                                            // change line of one from line 2 to 1
                                            $line1 = false;
                                            foreach ($firstLineCommanders as $commander) {
                                                if ($commander->statement != Commander::MOVING) {
                                                    if (!$line1) {
                                                        $line1 = true;
                                                    } else {
                                                        # move to the mess
                                                        $commander->statement = Commander::RESERVE;
                                                        $commanderManager->emptySquadrons($commander);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 0:
                                    if ($totalQtyLine1 == 0) {
                                        # one from line 2 to line 1
                                        $line1 = false;
                                        $line2 = false;
                                        foreach ($firstLineCommanders as $commander) {
                                            if (!$line1) {
                                                $line1 = true;
                                            } elseif (!$line2) {
                                                # move one to line 2
                                                $commander->line = 2;
                                                $line2 = true;
                                            } else {
                                                # move to the mess
                                                $commander->statement = Commander::RESERVE;
                                                $commanderManager->emptySquadrons($commander);
                                            }
                                        }
                                    } elseif ($totalQtyLine2 == 0) {
                                        # one from line 1 to line 2
                                        $line1 = false;
                                        $line2 = false;
                                        foreach ($secondLineCommanders as $commander) {
                                            if (!$line2) {
                                                $line2 = true;
                                            } elseif (!$line1) {
                                                # move one to line 1
                                                $commander->line = 1;
                                                $line1 = true;
                                            } else {
                                                # move to the mess
                                                $commander->statement = Commander::RESERVE;
                                                $commanderManager->emptySquadrons($commander);
                                            }
                                        }
                                    } else {
                                        # one on each line
                                        $line1 = false;
                                        foreach ($firstLineCommanders as $commander) {
                                            if (!$line1) {
                                                $line1 = true;
                                            } else {
                                                # move to the mess
                                                $commander->statement = Commander::RESERVE;
                                                $commanderManager->emptySquadrons($commander);
                                            }
                                        }
                                        $line2 = false;
                                        foreach ($secondLineCommanders as $commander) {
                                            if (!$line2) {
                                                $line2 = true;
                                            } else {
                                                # move to the mess
                                                $commander->statement = Commander::RESERVE;
                                                $commanderManager->emptySquadrons($commander);
                                            }
                                        }
                                    }
                                    break;
                                default:
                                    # the user can't change base type to commercial right now !
                                    $canChangeBaseType = false;
                            }
                        } else {
                            if ($totalQtyLine1 == 2) {
                                # switch one from line 1 to line 2
                                $firstLineCommanders[0]->line = 2;
                            }
                            if ($totalQtyLine2 == 2) {
                                # switch one from line 2 to line 1
                                $secondLineCommanders[1]->line = 1;
                            }
                        }
                    }
                    if ($canChangeBaseType) {
                        $playerManager->decreaseCredit($player, $totalPrice);
                        $orbitalBase->typeOfBase = $type;
                        # delete commercial buildings
                        for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
                            $maxLevel = $orbitalBaseHelper->getBuildingInfo($i, 'maxLevel', $type);
                            if ($orbitalBase->getBuildingLevel($i) > $maxLevel) {
                                $orbitalBase->setBuildingLevel($i, $maxLevel);
                            }
                        }
                        # delete buildings in queue
                        $buildingQueues = $buildingQueueManager->getBaseQueues($baseId);
                        foreach ($buildingQueues as $buildingQueue) {
                            $entityManager->remove($buildingQueue);
                        }
                        $entityManager->flush();
                        # send the right alert
                        if ($type == OrbitalBase::TYP_COMMERCIAL) {
                            # change base type in session
                            for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
                                if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
                                    $session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_COMMERCIAL);
                                    break;
                                }
                            }
                            $session->addFlashbag('Votre Base Militaire devient un Centre Commerciale. Vos bâtiments militaires superflus sont détruits.', Flashbag::TYPE_SUCCESS);
                        } else {
                            # change base type in session
                            for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
                                if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
                                    $session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_MILITARY);
                                    break;
                                }
                            }
                            $session->addFlashbag('Votre Centre Industriel devient une Base Militaire. Vos bâtiments commerciaux superflus sont détruits.', Flashbag::TYPE_SUCCESS);
                        }
                    } else {
                        throw new ErrorException('modification du type de la base orbitale impossible - vous avez trop de flottes en mouvement pour changer votre base en Centre Industriel');
                    }
                } else {
                    throw new ErrorException('modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits');
                }
            } else {
                throw new ErrorException('modification du type de la base orbitale impossible (seulement capitale, commercial ou militaire)');
            }
        } elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_CAPITAL) {
            /*switch ($type) {
                case OrbitalBase::TYP_COMMERCIAL:
                    $orbitalBase->typeOfBase = $type;
                    # casser les bâtiments en trop
                    # killer la file de construction
                    throw new ErrorException('Votre base orbitale devient commerciale.', ALERT_STD_SUCCESS);
                    break;
                case OrbitalBase::TYP_MILITARY:
                    $orbitalBase->typeOfBase = $type;
                    # casser les bâtiments en trop
                    # killer la file de construction
                    throw new ErrorException('Votre base orbitale devient militaire.', ALERT_STD_SUCCESS);
                    break;
                default :
                    throw new ErrorException('modification du type de la base orbitale impossible (seulement commercial ou militaire)', ALERT_STD_ERROR);
                    break;
            }*/
            throw new ErrorException('modification du type de la base orbitale impossible - c\'est déjà une capitale !');
        } else {
            throw new ErrorException('modification du type de la base orbitale impossible - type invalide');
        }
    } else {
        throw new ErrorException('cette base ne vous appartient pas');
    }
} else {
    throw new FormException('pas assez d\'informations pour changer le type de la base orbitale');
}
$entityManager->flush();
$entityManager->getRepository(OrbitalBase::class)->updateBuildingLevels($orbitalBase);
$eventDispatcher->dispatch(new PlaceOwnerChangeEvent($placeManager->get($orbitalBase->getId())));
