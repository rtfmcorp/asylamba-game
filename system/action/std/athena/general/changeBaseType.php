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
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$recyclingLogManager = $this->getContainer()->get('athena.recycling_log_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$database = $this->getContainer()->get('database');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$baseMinLevelForChange = $this->getContainer()->getParameter('athena.obm.change_type_min_level');
$baseMinLevelForCapital = $this->getContainer()->getParameter('athena.obm.capital_min_level');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$type = $request->query->get('type');


if ($baseId !== FALSE AND $type !== FALSE AND in_array($baseId, $verif)) {
 	$S_OBM1 = $orbitalBaseManager->getCurrentSession();
	$orbitalBaseManager->newSession();
	$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

	if ($orbitalBaseManager->size() > 0) {
		$orbitalBase = $orbitalBaseManager->get();
		$S_PAM1 = $playerManager->getCurrentSession();
		$playerManager->newSession();
		$playerManager->load(array('id' => $session->get('playerId')));
		$player = $playerManager->get();

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
								$qr = $database->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}

							$response->flashbag->add($orbitalBase->name . ' est désormais un Centre Industriel', Response::FLASHBAG_SUCCESS);
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
								$qr = $database->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}

							$response->flashbag->add($orbitalBase->name . ' est désormais une Base Militaire', Response::FLASHBAG_SUCCESS);
						} else {
							throw new ErrorException('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits');
						}
						break;
					default :
						throw new ErrorException('Modification du type de la base orbitale impossible (seulement commercial ou militaire)');
				}
			} else {
				throw new ErrorException('Evolution de votre colonie impossible - niveau du générateur pas assez élevé');
			}
		} elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL OR $orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY) {
			if ($type == OrbitalBase::TYP_CAPITAL) {
				if ($orbitalBase->levelGenerator >= $baseMinLevelForCapital) {
					$S_OBM2 = $orbitalBaseManager->getCurrentSession();
					$orbitalBaseManager->newSession();
					$orbitalBaseManager->load(array('rPlayer' => $session->get('playerId')));
					
					$capitalQuantity = 0;
					for ($i = 0; $i < $orbitalBaseManager->size(); $i++) { 
						if ($orbitalBaseManager->get($i)->typeOfBase == OrbitalBase::TYP_CAPITAL) {
							$capitalQuantity++;
						}
					}
					$orbitalBaseManager->changeSession($S_OBM2);

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
								$qr = $database->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([$session->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}
							$response->flashbag->add($orbitalBase->name . ' est désormais une capitale.', Response::FLASHBAG_SUCCESS);
						} else {
							throw new ErrorException('Modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits');
						}
					} else {
							throw new ErrorException('Vous ne pouvez pas avoir plus d\'une Capitale. Sauf si vous en conquérez à vos ennemis bien sûr.');
					}
				} else {
					throw new ErrorException('Pour transformer votre base en capitale, vous devez augmenter votre générateur jusqu\'au niveau ' . $baseMinLevelForCapital . '.');
				}
			} elseif (($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL AND $type == OrbitalBase::TYP_MILITARY)
				OR ($orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY AND $type == OrbitalBase::TYP_COMMERCIAL)) {
				# commercial --> military OR military --> commercial
				if ($type == OrbitalBase::TYP_COMMERCIAL) {
					$totalPrice = PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price');
				} else {
					$totalPrice = PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price');
				}
				if ($player->credit >= $totalPrice) {
					$canChangeBaseType = TRUE;
					if ($type == OrbitalBase::TYP_COMMERCIAL) {
						# delete all recycling missions and logs
						$S_REM1 = $recyclingMissionManager->getCurrentSession();
						$recyclingMissionManager->newSession();
						$recyclingMissionManager->load(array('rBase' => $orbitalBase->rPlace));
						for ($i = $recyclingMissionManager->size() - 1; $i >= 0; $i--) {
							$recyclingLogManager->deleteAllFromMission($recyclingMissionManager->get($i)->id);
							$recyclingMissionManager->deleteById($recyclingMissionManager->get($i)->id);
						}
						$recyclingMissionManager->changeSession($S_REM1);

						# verify if fleets are moving or not
						# transfer to the mess the extra commanders and change line if needed
						$S_COM2 = $commanderManager->getCurrentSession();

						$commanderManager->newSession();
						$commanderManager->load(array('c.rBase' => $orbitalBase->rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
						$totalQtyLine1 = $commanderManager->size();
						$movingQtyLine1 = 0;
						for ($i = 0; $i < $commanderManager->size(); $i++) { 
							if ($commanderManager->get($i)->statement == Commander::MOVING) {
								$movingQtyLine1++;
							}
						}
						$S_COM_Sess1 = $commanderManager->getCurrentSession();

						$commanderManager->newSession();
						$commanderManager->load(array('c.rBase' => $orbitalBase->rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
						$totalQtyLine2 = $commanderManager->size();
						$movingQtyLine2 = 0;
						for ($i = 0; $i < $commanderManager->size(); $i++) { 
							if ($commanderManager->get($i)->statement == Commander::MOVING) {
								$movingQtyLine2++;
							}
						}
						$S_COM_Sess2 = $commanderManager->getCurrentSession();

						$totalQty = $totalQtyLine1 + $totalQtyLine2;
						$movingQty = $movingQtyLine1 + $movingQtyLine2;

						if ($totalQty >= 2) {
							switch ($movingQty){
								case 2 :
									$line1 = FALSE;
									$line2 = FALSE;
									$commanderManager->changeSession($S_COM_Sess1);
									for ($i = 0; $i < $commanderManager->size(); $i++) { 
										if ($commanderManager->get($i)->statement == Commander::MOVING) {
											if ($line1) {
												# move to line 2
												$commanderManager->get($i)->line = 2;
												$line2 = TRUE;
											} else {
												# stay on line 1
												$line1 = TRUE;
											}
										} else {
											# move to the mess
											$commanderManager->get($i)->statement = Commander::RESERVE;
											$commanderManager->get($i)->emptySquadrons();
										}
									}
									$commanderManager->changeSession($S_COM_Sess2);
									for ($i = 0; $i < $commanderManager->size(); $i++) { 
										if ($commanderManager->get($i)->statement == Commander::MOVING) {
											if ($line2) {
												# move to line 1
												$commanderManager->get($i)->line = 1;
												$line1 = TRUE;
											} else {
												# stay on line 2
												$line2 = TRUE;
											}
										} else {
											# move to the mess
											$commanderManager->get($i)->statement = Commander::RESERVE;
											$commanderManager->get($i)->emptySquadrons();
										}
									}
									break;
								case 1 :
									if ($movingQtyLine1 == 1) {
										if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
											// let stay one cmder on each line
											$commanderManager->changeSession($S_COM_Sess1);
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if ($commanderManager->get($i)->statement != Commander::MOVING) {
													# move to the mess
													$commanderManager->get($i)->statement = Commander::RESERVE;
													$commanderManager->get($i)->emptySquadrons();
												}
											}
											$commanderManager->changeSession($S_COM_Sess2);
											$line2 = FALSE;
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if (!$line2) {
													$line2 = TRUE;
												} else {
													# move to the mess
													$commanderManager->get($i)->statement = Commander::RESERVE;
													$commanderManager->get($i)->emptySquadrons();
												}
											}
										} else {
											// change line of one from line 1 to 2
											$commanderManager->changeSession($S_COM_Sess1);
											$line2 = FALSE;
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if ($commanderManager->get($i)->statement != Commander::MOVING) {
													if (!$line2) {
														$line2 = TRUE;
													} else {
														# move to the mess
														$commanderManager->get($i)->statement = Commander::RESERVE;
														$commanderManager->get($i)->emptySquadrons();
													}
												}
											}
										}
									} else { # $movingQtyLine2 == 1
										if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
											// let stay one cmder on each line
											$commanderManager->changeSession($S_COM_Sess2);
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if ($commanderManager->get($i)->statement != Commander::MOVING) {
													# move to the mess
													$commanderManager->get($i)->statement = Commander::RESERVE;
													$commanderManager->get($i)->emptySquadrons();
												}
											}
											$commanderManager->changeSession($S_COM_Sess1);
											$line1 = FALSE;
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if (!$line1) {
													$line1 = TRUE;
												} else {
													# move to the mess
													$commanderManager->get($i)->statement = Commander::RESERVE;
													$commanderManager->get($i)->emptySquadrons();
												}
											}
										} else {
											// change line of one from line 2 to 1
											$commanderManager->changeSession($S_COM_Sess2);
											$line1 = FALSE;
											for ($i = 0; $i < $commanderManager->size(); $i++) { 
												if ($commanderManager->get($i)->statement != Commander::MOVING) {
													if (!$line1) {
														$line1 = TRUE;
													} else {
														# move to the mess
														$commanderManager->get($i)->statement = Commander::RESERVE;
														$commanderManager->get($i)->emptySquadrons();
													}
												}
											}
										}
									}
									break;
								case 0 :
									if ($totalQtyLine1 == 0) {
										# one from line 2 to line 1
										$commanderManager->changeSession($S_COM_Sess1);
										$line1 = FALSE;
										$line2 = FALSE;
										for ($i = 0; $i < $commanderManager->size(); $i++) { 
											if (!$line1) {
												$line1 = TRUE;
											} else if (!$line2) {
												# move one to line 2
												$commanderManager->get($i)->line = 2;
												$line2 = TRUE;
											} else {
												# move to the mess
												$commanderManager->get($i)->statement = Commander::RESERVE;
												$commanderManager->get($i)->emptySquadrons();
											}
										}
									} else if ($totalQtyLine2 == 0) {
										# one from line 1 to line 2
										$commanderManager->changeSession($S_COM_Sess2);
										$line1 = FALSE;
										$line2 = FALSE;
										for ($i = 0; $i < $commanderManager->size(); $i++) { 
											if (!$line2) {
												$line2 = TRUE;
											} else if (!$line1) {
												# move one to line 1
												$commanderManager->get($i)->line = 1;
												$line1 = TRUE;
											} else {
												# move to the mess
												$commanderManager->get($i)->statement = Commander::RESERVE;
												$commanderManager->get($i)->emptySquadrons();
											}
										}
									} else {
										# one on each line
										$commanderManager->changeSession($S_COM_Sess1);
										$line1 = FALSE;
										for ($i = 0; $i < $commanderManager->size(); $i++) { 
											if (!$line1) {
												$line1 = TRUE;
											} else {
												# move to the mess
												$commanderManager->get($i)->statement = Commander::RESERVE;
												$commanderManager->get($i)->emptySquadrons();
											}
										}
										$commanderManager->changeSession($S_COM_Sess2);
										$line2 = FALSE;
										for ($i = 0; $i < $commanderManager->size(); $i++) { 
											if (!$line2) {
												$line2 = TRUE;
											} else {
												# move to the mess
												$commanderManager->get($i)->statement = Commander::RESERVE;
												$commanderManager->get($i)->emptySquadrons();
											}
										}
									}
									break;
								default :
									# the user can't change base type to commercial right now !
									$canChangeBaseType = FALSE;
							}
						} else {
							if ($totalQtyLine1 == 2) {
								# switch one from line 1 to line 2
								$commanderManager->changeSession($S_COM_Sess1);
								$commanderManager->get()->line = 2;
							}
							if ($totalQtyLine2 == 2) {
								# switch one from line 2 to line 1
								$commanderManager->changeSession($S_COM_Sess2);
								$commanderManager->get()->line = 1;
							}
						}

						$commanderManager->changeSession($S_COM2);
					}
					if ($canChangeBaseType) {
						$playerManager->decreaseCredit($player, $totalPrice);
						$orbitalBase->typeOfBase = $type;
						# delete commercial buildings
						for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
							$maxLevel = OrbitalBaseResource::getBuildingInfo($i, 'maxLevel', $type);
							if ($orbitalBase->getBuildingLevel($i) > $maxLevel) {
								$orbitalBase->setBuildingLevel($i, $maxLevel);
							}
						}
						# delete buildings in queue
						$S_BQM1 = $buildingQueueManager->getCurrentSession();
						$buildingQueueManager->newSession(ASM_UMODE);
						$buildingQueueManager->load(array('rOrbitalBase' => $baseId), array('dEnd'));
						for ($i = $buildingQueueManager->size() - 1; $i >= 0; $i--) {
							$buildingQueueManager->deleteById($buildingQueueManager->get($i)->id);
						}
						$buildingQueueManager->changeSession($S_BQM1);
						# send the right alert
						if ($type == OrbitalBase::TYP_COMMERCIAL) {
							# change base type in session
							for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
								if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									$session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_COMMERCIAL);
									break;
								}
							}
							$response->flashbag->add('Votre Base Militaire devient un Centre Commerciale. Vos bâtiments militaires superflus sont détruits.', Response::FLASHBAG_SUCCESS);
						} else {
							# change base type in session
							for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
								if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									$session->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_MILITARY);
									break;
								}
							}
							$response->flashbag->add('Votre Centre Industriel devient une Base Militaire. Vos bâtiments commerciaux superflus sont détruits.', Response::FLASHBAG_SUCCESS);
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
		$playerManager->changeSession($S_PAM1);
	} else {
		throw new ErrorException('cette base ne vous appartient pas');
	}
	$orbitalBaseManager->changeSession($S_OBM1);
} else {
	throw new FormException('pas assez d\'informations pour changer le type de la base orbitale');
}