<?php
# change the type of the base action

# int baseid 		id of the orbital base
# int type			new type for the base

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Gaia\Resource\PlaceResource;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$type = Utils::getHTTPData('type');


if ($baseId !== FALSE AND $type !== FALSE AND in_array($baseId, $verif)) {
 	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$obm->size() > 0) {
		$orbitalBase = ASM::$obm->get();
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
		$player = ASM::$pam->get();

		if ($orbitalBase->typeOfBase == OrbitalBase::TYP_NEUTRAL) {
			if ($orbitalBase->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) {
				switch ($type) {
					case OrbitalBase::TYP_COMMERCIAL:
						$totalPrice = PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price');
						if ($player->credit >= $totalPrice) {

							$orbitalBase->typeOfBase = $type;
							$player->decreaseCredit($totalPrice);

							# change base type in session
							for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
								if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									CTR::$data->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_COMMERCIAL);
									break;
								}
							}
							if (DATA_ANALYSIS) {
								$db = Database::getInstance();
								$qr = $db->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([CTR::$data->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}

							CTR::$alert->add($orbitalBase->name . ' est désormais un Centre Industriel', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
						}
						break;
					case OrbitalBase::TYP_MILITARY:
						$totalPrice = PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price');
						if ($player->credit >= $totalPrice) {

							$orbitalBase->typeOfBase = $type;
							$player->decreaseCredit($totalPrice);

							# change base type in session
							for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
								if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									CTR::$data->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_MILITARY);
									break;
								}
							}

							if (DATA_ANALYSIS) {
								$db = Database::getInstance();
								$qr = $db->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([CTR::$data->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}

							CTR::$alert->add($orbitalBase->name . ' est désormais une Base Militaire', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('Evolution de votre colonie impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
						}
						break;
					default :
						CTR::$alert->add('Modification du type de la base orbitale impossible (seulement commercial ou militaire)', ALERT_STD_ERROR);
						break;
				}
			} else {
				CTR::$alert->add('Evolution de votre colonie impossible - niveau du générateur pas assez élevé', ALERT_STD_ERROR);
			}
		} elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_COMMERCIAL OR $orbitalBase->typeOfBase == OrbitalBase::TYP_MILITARY) {
			if ($type == OrbitalBase::TYP_CAPITAL) {
				if ($orbitalBase->levelGenerator >= OBM_LEVEL_MIN_FOR_CAPITAL) {
					$S_OBM2 = ASM::$obm->getCurrentSession();
					ASM::$obm->newSession();
					ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));
					
					$capitalQuantity = 0;
					for ($i = 0; $i < ASM::$obm->size(); $i++) { 
						if (ASM::$obm->get($i)->typeOfBase == OrbitalBase::TYP_CAPITAL) {
							$capitalQuantity++;
						}
					}
					ASM::$obm->changeSession($S_OBM2);

					if ($capitalQuantity == 0) {
						$totalPrice = PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'price');
						if ($player->credit >= $totalPrice) {
							$orbitalBase->typeOfBase = $type;
							$player->decreaseCredit($totalPrice);

							# change base type in session
							for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
								if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									CTR::$data->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_CAPITAL);
									break;
								}
							}

							if (DATA_ANALYSIS) {
								$db = Database::getInstance();
								$qr = $db->prepare('INSERT INTO 
									DA_BaseAction(`from`, type, opt1, weight, dAction)
									VALUES(?, ?, ?, ?, ?)'
								);
								$qr->execute([CTR::$data->get('playerId'), 4, $type, DataAnalysis::creditToStdUnit($totalPrice), Utils::now()]);
							}
							
							CTR::$alert->add($orbitalBase->name . ' est désormais une capitale.', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('Modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
						}
					} else {
							CTR::$alert->add('Vous ne pouvez pas avoir plus d\'une Capitale. Sauf si vous en conquérez à vos ennemis bien sûr.', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Pour transformer votre base en capitale, vous devez augmenter votre générateur jusqu\'au niveau ' . OBM_LEVEL_MIN_FOR_CAPITAL . '.', ALERT_STD_ERROR);
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
						$S_REM1 = ASM::$rem->getCurrentSession();
						ASM::$rem->newSession();
						ASM::$rem->load(array('rBase' => $orbitalBase->rPlace));
						for ($i = ASM::$rem->size() - 1; $i >= 0; $i--) {
							ASM::$rlm->deleteAllFromMission(ASM::$rem->get($i)->id);
							ASM::$rem->deleteById(ASM::$rem->get($i)->id);
						}
						ASM::$rem->changeSession($S_REM1);

						# verify if fleets are moving or not
						# transfer to the mess the extra commanders and change line if needed
						$S_COM2 = ASM::$com->getCurrentSession();

						ASM::$com->newSession();
						ASM::$com->load(array('c.rBase' => $orbitalBase->rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 1));
						$totalQtyLine1 = ASM::$com->size();
						$movingQtyLine1 = 0;
						for ($i = 0; $i < ASM::$com->size(); $i++) { 
							if (ASM::$com->get($i)->statement == Commander::MOVING) {
								$movingQtyLine1++;
							}
						}
						$S_COM_Sess1 = ASM::$com->getCurrentSession();

						ASM::$com->newSession();
						ASM::$com->load(array('c.rBase' => $orbitalBase->rPlace, 'c.statement' => array(Commander::AFFECTED, Commander::MOVING), 'c.line' => 2));
						$totalQtyLine2 = ASM::$com->size();
						$movingQtyLine2 = 0;
						for ($i = 0; $i < ASM::$com->size(); $i++) { 
							if (ASM::$com->get($i)->statement == Commander::MOVING) {
								$movingQtyLine2++;
							}
						}
						$S_COM_Sess2 = ASM::$com->getCurrentSession();

						$totalQty = $totalQtyLine1 + $totalQtyLine2;
						$movingQty = $movingQtyLine1 + $movingQtyLine2;

						if ($totalQty >= 2) {
							switch ($movingQty){
								case 2 :
									$line1 = FALSE;
									$line2 = FALSE;
									ASM::$com->changeSession($S_COM_Sess1);
									for ($i = 0; $i < ASM::$com->size(); $i++) { 
										if (ASM::$com->get($i)->statement == Commander::MOVING) {
											if ($line1) {
												# move to line 2
												ASM::$com->get($i)->line = 2;
												$line2 = TRUE;
											} else {
												# stay on line 1
												$line1 = TRUE;
											}
										} else {
											# move to the mess
											ASM::$com->get($i)->statement = Commander::RESERVE;
											ASM::$com->get($i)->emptySquadrons();
										}
									}
									ASM::$com->changeSession($S_COM_Sess2);
									for ($i = 0; $i < ASM::$com->size(); $i++) { 
										if (ASM::$com->get($i)->statement == Commander::MOVING) {
											if ($line2) {
												# move to line 1
												ASM::$com->get($i)->line = 1;
												$line1 = TRUE;
											} else {
												# stay on line 2
												$line2 = TRUE;
											}
										} else {
											# move to the mess
											ASM::$com->get($i)->statement = Commander::RESERVE;
											ASM::$com->get($i)->emptySquadrons();
										}
									}
									break;
								case 1 :
									if ($movingQtyLine1 == 1) {
										if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
											// let stay one cmder on each line
											ASM::$com->changeSession($S_COM_Sess1);
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (ASM::$com->get($i)->statement != Commander::MOVING) {
													# move to the mess
													ASM::$com->get($i)->statement = Commander::RESERVE;
													ASM::$com->get($i)->emptySquadrons();
												}
											}
											ASM::$com->changeSession($S_COM_Sess2);
											$line2 = FALSE;
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (!$line2) {
													$line2 = TRUE;
												} else {
													# move to the mess
													ASM::$com->get($i)->statement = Commander::RESERVE;
													ASM::$com->get($i)->emptySquadrons();
												}
											}
										} else {
											// change line of one from line 1 to 2
											ASM::$com->changeSession($S_COM_Sess1);
											$line2 = FALSE;
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (ASM::$com->get($i)->statement != Commander::MOVING) {
													if (!$line2) {
														$line2 = TRUE;
													} else {
														# move to the mess
														ASM::$com->get($i)->statement = Commander::RESERVE;
														ASM::$com->get($i)->emptySquadrons();
													}
												}
											}
										}
									} else { # $movingQtyLine2 == 1
										if ($totalQtyLine1 >= 1 && $totalQtyLine2 >= 1) {
											// let stay one cmder on each line
											ASM::$com->changeSession($S_COM_Sess2);
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (ASM::$com->get($i)->statement != Commander::MOVING) {
													# move to the mess
													ASM::$com->get($i)->statement = Commander::RESERVE;
													ASM::$com->get($i)->emptySquadrons();
												}
											}
											ASM::$com->changeSession($S_COM_Sess1);
											$line1 = FALSE;
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (!$line1) {
													$line1 = TRUE;
												} else {
													# move to the mess
													ASM::$com->get($i)->statement = Commander::RESERVE;
													ASM::$com->get($i)->emptySquadrons();
												}
											}
										} else {
											// change line of one from line 2 to 1
											ASM::$com->changeSession($S_COM_Sess2);
											$line1 = FALSE;
											for ($i = 0; $i < ASM::$com->size(); $i++) { 
												if (ASM::$com->get($i)->statement != Commander::MOVING) {
													if (!$line1) {
														$line1 = TRUE;
													} else {
														# move to the mess
														ASM::$com->get($i)->statement = Commander::RESERVE;
														ASM::$com->get($i)->emptySquadrons();
													}
												}
											}
										}
									}
									break;
								case 0 :
									if ($totalQtyLine1 == 0) {
										# one from line 2 to line 1
										ASM::$com->changeSession($S_COM_Sess1);
										$line1 = FALSE;
										$line2 = FALSE;
										for ($i = 0; $i < ASM::$com->size(); $i++) { 
											if (!$line1) {
												$line1 = TRUE;
											} else if (!$line2) {
												# move one to line 2
												ASM::$com->get($i)->line = 2;
												$line2 = TRUE;
											} else {
												# move to the mess
												ASM::$com->get($i)->statement = Commander::RESERVE;
												ASM::$com->get($i)->emptySquadrons();
											}
										}
									} else if ($totalQtyLine2 == 0) {
										# one from line 1 to line 2
										ASM::$com->changeSession($S_COM_Sess2);
										$line1 = FALSE;
										$line2 = FALSE;
										for ($i = 0; $i < ASM::$com->size(); $i++) { 
											if (!$line2) {
												$line2 = TRUE;
											} else if (!$line1) {
												# move one to line 1
												ASM::$com->get($i)->line = 1;
												$line1 = TRUE;
											} else {
												# move to the mess
												ASM::$com->get($i)->statement = Commander::RESERVE;
												ASM::$com->get($i)->emptySquadrons();
											}
										}
									} else {
										# one on each line
										ASM::$com->changeSession($S_COM_Sess1);
										$line1 = FALSE;
										for ($i = 0; $i < ASM::$com->size(); $i++) { 
											if (!$line1) {
												$line1 = TRUE;
											} else {
												# move to the mess
												ASM::$com->get($i)->statement = Commander::RESERVE;
												ASM::$com->get($i)->emptySquadrons();
											}
										}
										ASM::$com->changeSession($S_COM_Sess2);
										$line2 = FALSE;
										for ($i = 0; $i < ASM::$com->size(); $i++) { 
											if (!$line2) {
												$line2 = TRUE;
											} else {
												# move to the mess
												ASM::$com->get($i)->statement = Commander::RESERVE;
												ASM::$com->get($i)->emptySquadrons();
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
								ASM::$com->changeSession($S_COM_Sess1);
								ASM::$com->get()->line = 2;
							}
							if ($totalQtyLine2 == 2) {
								# switch one from line 2 to line 1
								ASM::$com->changeSession($S_COM_Sess2);
								ASM::$com->get()->line = 1;
							}
						}

						ASM::$com->changeSession($S_COM2);
					}
					if ($canChangeBaseType) {
						$player->decreaseCredit($totalPrice);
						$orbitalBase->typeOfBase = $type;
						# delete commercial buildings
						for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
							$maxLevel = OrbitalBaseResource::getBuildingInfo($i, 'maxLevel', $type);
							if ($orbitalBase->getBuildingLevel($i) > $maxLevel) {
								$orbitalBase->setBuildingLevel($i, $maxLevel);
							}
						}
						# delete buildings in queue
						$S_BQM1 = ASM::$bqm->getCurrentSession();
						ASM::$bqm->newSession(ASM_UMODE);
						ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));
						for ($i = ASM::$bqm->size() - 1; $i >= 0; $i--) {
							ASM::$bqm->deleteById(ASM::$bqm->get($i)->id);
						}
						ASM::$bqm->changeSession($S_BQM1);
						# send the right alert
						if ($type == OrbitalBase::TYP_COMMERCIAL) {
							# change base type in session
							for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
								if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									CTR::$data->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_COMMERCIAL);
									break;
								}
							}
							CTR::$alert->add('Votre Base Militaire devient un Centre Commerciale. Vos bâtiments militaires superflus sont détruits.', ALERT_STD_SUCCESS);
						} else {
							# change base type in session
							for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
								if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
									CTR::$data->get('playerBase')->get('ob')->get($i)->add('type', OrbitalBase::TYP_MILITARY);
									break;
								}
							}
							CTR::$alert->add('Votre Centre Industriel devient une Base Militaire. Vos bâtiments commerciaux superflus sont détruits.', ALERT_STD_SUCCESS);
						}
					} else {
						CTR::$alert->add('modification du type de la base orbitale impossible - vous avez trop de flottes en mouvement pour changer votre base en Centre Industriel', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('modification du type de la base orbitale impossible - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('modification du type de la base orbitale impossible (seulement capitale, commercial ou militaire)', ALERT_STD_ERROR);
			}
		} elseif ($orbitalBase->typeOfBase == OrbitalBase::TYP_CAPITAL) {
			/*switch ($type) {
				case OrbitalBase::TYP_COMMERCIAL:
					$orbitalBase->typeOfBase = $type;
					# casser les bâtiments en trop
					# killer la file de construction
					CTR::$alert->add('Votre base orbitale devient commerciale.', ALERT_STD_SUCCESS);
					break;
				case OrbitalBase::TYP_MILITARY:
					$orbitalBase->typeOfBase = $type;
					# casser les bâtiments en trop
					# killer la file de construction
					CTR::$alert->add('Votre base orbitale devient militaire.', ALERT_STD_SUCCESS);
					break;
				default :
					CTR::$alert->add('modification du type de la base orbitale impossible (seulement commercial ou militaire)', ALERT_STD_ERROR);
					break;
			}*/
			CTR::$alert->add('modification du type de la base orbitale impossible - c\'est déjà une capitale !', ALERT_STD_ERROR);
		} else {
			CTR::$alert->add('modification du type de la base orbitale impossible - type invalide', ALERT_STD_ERROR);
		}
		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour changer le type de la base orbitale', ALERT_STD_FILLFORM);
}