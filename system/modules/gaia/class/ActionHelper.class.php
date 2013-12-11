<?php
abstract class ActionHelper {

	public static function loot($ob, &$link, &$box, $id, $place, $commanderSession) {
		$link .= '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/loot.png" alt="pillage" /></a>';

		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$box .= '<h5>Effectuer un pillage</h5>';
			# check si au moins un commandant est disponible
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->changeSession($commanderSession);
			$commanderQuantity = 0;
			for ($i = 0; $i < ASM::$com->size(); $i++) { 
				if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
	  				$commanderQuantity++;
				}
			}
			if ($commanderQuantity > 0) {
				# check si assez de PA
				if ($place->getRSystem() == $ob->getSystem()) {
					$time = Game::getTimeTravelInSystem($ob->getPosition(), $place->getPosition());
					$pa = Game::getPAToTravel($time);
				} else {
					$time = Game::getTimeTravelOutOfSystem($ob->getXSystem(), $ob->getYSystem(), $place->getXSystem(), $place->getYSystem());
					$pa = Game::getPAToTravel($time);
				}

				if (CTR::$data->get('playerInfo')->get('actionPoint') >= $pa) {
					for ($i = 0; $i < ASM::$com->size(); $i++) {
						if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
							$box .= '<a href="' . APP_ROOT . 'action/a-loot/commanderid-' . ASM::$com->get($i)->getId() . '/placeid-' . $place->getId() . '/redirect-' . $place->getId() . '" class="commander">';
								$box .= '<img class="avatar" src="' . MEDIA . 'commander/small/c1-l1-c' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="' . ASM::$com->get($i)->getName() . '" />';
								$box .= '<span class="label">';
									$box .= '<strong>' . ASM::$com->get($i)->getName() . '</strong><br />';
									$box .= ASM::$com->get($i)->getPev() . ' pev<br />';
									$box .= Format::numberFormat(ASM::$com->get($i)->getPev() * COEFFLOOT) . ' de soute';
								$box .= '</span>';
								$box .= '<span class="value">';
									$box .= $pa . ' <img alt="points d\'attaque" src="' . MEDIA . 'resources/pa.png" class="icon-color"><br />';
									$box .= Chronos::secondToFormat($time, 'lite') . ' <img alt="temps" src="' . MEDIA . 'resources/time.png" class="icon-color">';
								$box .= '</span>';
							$box .= '</a>';
						}
					}
				} else {
					$box .= '<p class="info">Vous ne disposez pas d\'assez de points d\'attaque pour piller cette planète. Cette attaque nécessite ' . $pa . ' points d\'attaque. Essayez de lancer une attaque plus près de votre base.</p>';
				}
			} else {
				$box .= '<p class="info">Vous n\'avez aucun commandant en fonction sur ' . $ob->getName() . '. <a href="' . APP_ROOT . 'bases/base-' . $ob->getId() . '/view-school">Affectez un commandant</a> et envoyez un pillage depuis ' . $ob->getName() . '.</p>';
			}
			ASM::$com->changeSession($S_COM2);
		$box .= '</div>';
	}

	public static function conquest($ob, &$link, &$box, $id, $place, $commanderSession, $movingCommandersSession, $technologies) {
		$link .= '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/colonize.png" alt="conquête" /></a>';

		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$box .= '<h5>Effectuer une conquête</h5>';
			# check si technologie CONQUEST débloquée
			if ($technologies->getTechnology(Technology::CONQUEST) == 1) {
				# check si la technologie BASE_QUANTITY a un niveau assez élevé
				$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
				$obQuantity = CTR::$data->get('playerBase')->get('ob')->size();
				$msQuantity = CTR::$data->get('playerBase')->get('ms')->size();
				$coloQuantity = 0;
				$S_COM2 = ASM::$com->getCurrentSession();
				ASM::$com->changeSession($movingCommandersSession);
				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					if (ASM::$com->get($i)->getTypeOfMove() == COM_COLO) {
						$coloQuantity++;
					}
				}
				ASM::$com->changeSession($S_COM2);
				if ($obQuantity + $msQuantity + $coloQuantity < $maxBasesQuantity) {
					# check si au moins un commandant est disponible
					$S_COM2 = ASM::$com->getCurrentSession();
					ASM::$com->changeSession($commanderSession);
					$commanderQuantity = 0;
					for ($i = 0; $i < ASM::$com->size(); $i++) { 
						if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
							$commanderQuantity++;
						}
					}
					if ($commanderQuantity > 0) {
						# check si assez de crédits
						$creditPrice = ($obQuantity + $coloQuantity) * CREDITCOEFFTOCONQUER;

						if (CTR::$data->get('playerInfo')->get('credit') >= $creditPrice) {
							# check si assez de points d'attaque
							if ($place->getRSystem() == $ob->getSystem()) {
								$time = Game::getTimeTravelInSystem($ob->getPosition(), $place->getPosition());
								$pa = Game::getPAToTravel($time);
							} else {
								$time = Game::getTimeTravelOutOfSystem($ob->getXSystem(), $ob->getYSystem(), $place->getXSystem(), $place->getYSystem());
								$pa = Game::getPAToTravel($time);
							}

							if (CTR::$data->get('playerInfo')->get('actionPoint') >= $pa) {
								for ($i = 0; $i < ASM::$com->size(); $i++) {
									if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
										$box .= '<a href="' . APP_ROOT . 'action/a-conquer/commanderid-' . ASM::$com->get($i)->getId() . '/placeid-' . $place->getId() . '/redirect-' . $place->getId() . '" class="commander">';
											$box .= '<img class="avatar" src="' . MEDIA . 'commander/small/c1-l1-c' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="' . ASM::$com->get($i)->getName() . '" />';
											$box .= '<span class="label">';
												$box .= '<strong>' . ASM::$com->get($i)->getName() . '</strong><br />';
												$box .= ASM::$com->get($i)->getPev() . ' pev';
											$box .= '</span>';
											$box .= '<span class="value">';
												$box .= $pa . ' <img alt="points d\'attaque" src="' . MEDIA . 'resources/pa.png" class="icon-color"><br />';
												$box .= Chronos::secondToFormat($time, 'lite') . ' <img alt="temps" src="' . MEDIA . 'resources/time.png" class="icon-color"><br />';
												$box .= Format::numberFormat($creditPrice) . ' <img alt="credit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
											$box .= '</span>';
										$box .= '</a>';
									}
								}
							} else {
								$box .= '<p class="info">Vous ne disposez pas d\'assez de points d\'attaque pour conquérir cette planète.</p>';
							}
						} else {
							$box .= '<p class="info">Vous n\'avez pas assez de crédits pour lancer la conquête. Il faut ' . $creditPrice . ' crédits.</p>';
						}
					} else {
						$box .= '<p class="info">Vous n\'avez aucun commandant en fonction sur ' . $ob->getName() . '. <a href="' . APP_ROOT . 'bases/base-' . $ob->getId() . '/view-school">Affectez un commandant</a> et envoyez un pillage depuis ' . $ob->getName() . '.</p>';
					}
					ASM::$com->changeSession($S_COM2);
				} else {
					$box .= '<p class="info">Pour pouvoir conquérir une planète supplémentaire, il faut augmenter le niveau de la technologie ' . TechnologyResource::getInfo(Technology::BASE_QUANTITY, 'name') . '.</p>';
				}
			} else {
				$box .= '<p class="info">Pour pouvoir conquérir une planète, il faut développer la technologie ' . TechnologyResource::getInfo(Technology::CONQUEST, 'name') . '. </p>';
			}
		$box .= '</div>';
	}

	public static function colonize($ob, &$link, &$box, $id, $place, $commanderSession, $movingCommandersSession, $technologies) {
		$link .= '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/colonize.png" alt="colonisation" /></a>';

		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$box .= '<h5>Effectuer une colonisation</h5>';
			# check si technologie COLONIZATION débloquée
			if ($technologies->getTechnology(Technology::COLONIZATION) == 1) {
				# check si la technologie BASE_QUANTITY a un niveau assez élevé
				$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
				$obQuantity = CTR::$data->get('playerBase')->get('ob')->size();
				$msQuantity = CTR::$data->get('playerBase')->get('ms')->size();
				$coloQuantity = 0;
				$S_COM2 = ASM::$com->getCurrentSession();
				ASM::$com->changeSession($movingCommandersSession);
				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					if (ASM::$com->get($i)->getTypeOfMove() == COM_COLO) {
						$coloQuantity++;
					}
				}
				ASM::$com->changeSession($S_COM2);
				if ($obQuantity + $msQuantity + $coloQuantity < $maxBasesQuantity) {
					# check si au moins un commandant est disponible
					$S_COM2 = ASM::$com->getCurrentSession();
					ASM::$com->changeSession($commanderSession);
					$commanderQuantity = 0;
					for ($i = 0; $i < ASM::$com->size(); $i++) { 
						if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
							$commanderQuantity++;
						}
					}
					if ($commanderQuantity > 0) {
						# check si assez de crédits
						$creditPrice = ($obQuantity + $coloQuantity) * CREDITCOEFFTOCOLONIZE;

						if (CTR::$data->get('playerInfo')->get('credit') >= $creditPrice) {
							# check si assez de points d'attaque
							if ($place->getRSystem() == $ob->getSystem()) {
								$time = Game::getTimeTravelInSystem($ob->getPosition(), $place->getPosition());
								$pa = Game::getPAToTravel($time);
							} else {
								$time = Game::getTimeTravelOutOfSystem($ob->getXSystem(), $ob->getYSystem(), $place->getXSystem(), $place->getYSystem());
								$pa = Game::getPAToTravel($time);
							}

							if (CTR::$data->get('playerInfo')->get('actionPoint') >= $pa) {
								for ($i = 0; $i < ASM::$com->size(); $i++) { 
									if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
										$box .= '<a href="' . APP_ROOT . 'action/a-colonize/commanderid-' . ASM::$com->get($i)->getId() . '/placeid-' . $place->getId() . '/redirect-' . $place->getId() . '" class="commander">';
											$box .= '<img class="avatar" src="' . MEDIA . 'commander/small/c1-l1-c' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="' . ASM::$com->get($i)->getName() . '" />';
											$box .= '<span class="label">';
												$box .= '<strong>' . ASM::$com->get($i)->getName() . '</strong><br />';
												$box .= ASM::$com->get($i)->getPev() . ' pev';
											$box .= '</span>';
											$box .= '<span class="value">';
												$box .= $pa . ' <img alt="points d\'attaque" src="' . MEDIA . 'resources/pa.png" class="icon-color"><br />';
												$box .= Chronos::secondToFormat($time, 'lite') . ' <img alt="temps" src="' . MEDIA . 'resources/time.png" class="icon-color"><br />';
												$box .= Format::numberFormat($creditPrice) . ' <img alt="credit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
											$box .= '</span>';
										$box .= '</a>';
									}
								}
							} else {
								$box .= '<p class="info">Vous ne disposez pas d\'assez de points d\'attaque pour piller cette planète.</p>';
							}
						} else {
							$box .= '<p class="info">Vous n\'avez pas assez de crédits pour envoyer la colonisation. Il faut ' . $creditPrice . ' crédits.</p>';
						}
					} else {
						$box .= '<p class="info">Vous n\'avez aucun commandant en fonction sur ' . $ob->getName() . '. <a href="' . APP_ROOT . 'bases/base-' . $ob->getId() . '/view-school">Affectez un commandant</a> et envoyez un pillage depuis ' . $ob->getName() . '.</p>';
					}
					ASM::$com->changeSession($S_COM2);
				} else {
					$box .= '<p class="info">Pour pouvoir coloniser une planète supplémentaire, il faut augmenter le niveau de la technologie ' . TechnologyResource::getInfo(Technology::BASE_QUANTITY, 'name') . '.</p>';
				}
			} else {
				$box .= '<p class="info">Pour pouvoir coloniser une planète, il faut développer la technologie ' . TechnologyResource::getInfo(Technology::COLONIZATION, 'name') . '.</p>';
			}
		$box .= '</div>';
	}

	public static function proposeRC($ob, &$link, &$box, $id, $place) {
		$tmpLink = '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/proposeRC.png" alt="conquête" /></a>';
		
		# gérer soit l'un soit l'autre
		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$sendResources = FALSE;
			$proposed = FALSE;
			$notAccepted = FALSE;
			$standby = FALSE;
			if ($ob->getRPlayer() == $place->getRPlayer()) {
				if ($ob->getRPlace() != $place->getId()) {
					$sendResources = TRUE;
				}
			} else {
				if ($ob->getLevelCommercialPlateforme() > 0) {
					if ($place->getLevelCommercialPlateforme() > 0) {
						# check si on a déjà une route avec ce joueur
						$S_CRM1 = ASM::$crm->getCurrentSession();
						ASM::$crm->changeSession($ob->routeManager);
						for ($i = 0; $i < ASM::$crm->size(); $i++) { 
							if (ASM::$crm->get($i)->getROrbitalBaseLinked() == $ob->getRPlace()) {
								if (ASM::$crm->get($i)->getROrbitalBase() == $place->getId()) {
									switch(ASM::$crm->get($i)->getStatement()) {
										case CRM_PROPOSED: $notAccepted = TRUE; break;
										case CRM_ACTIVE: $sendResources = TRUE; break;
										case CRM_STANDBY: $standby = TRUE; break;
									}
								}
							}
							if (ASM::$crm->get($i)->getROrbitalBase() == $ob->getRPlace()) {
								if (ASM::$crm->get($i)->getROrbitalBaseLinked() == $place->getId()) {
									switch(ASM::$crm->get($i)->getStatement()) {
										case CRM_PROPOSED: $proposed = TRUE; break;
										case CRM_ACTIVE: $sendResources = TRUE; break;
										case CRM_STANDBY: $standby = TRUE; break;
									}
								}
							}
						}
						ASM::$crm->changeSession($S_CRM1);
						if ($sendResources == FALSE) {
							if ($proposed == TRUE) {
								$box .= '<h5>Route commerciale en suspens</h5>';
								$box .= '<p>Le joueur n\'a pas encore accepté votre proposition de route commerciale. ';
								$box .= 'Si vous voulez voir le statut de la route ou annuler votre proposition, rendez-vous dans la ';
								$box .= '<a href="' . APP_ROOT . 'bases/base-' . $ob->getRPlace() . '/view-commercialplateforme">plateforme commerciale</a>';
								$box .= '.</p>';
							} elseif ($notAccepted == TRUE) {
								$box .= '<h5>Route commerciale en suspens</h5>';
								$box .= '<p>Ce joueur vous a proposé une route commerciale. ';
								$box .= 'Si vous voulez l\'accepter ou la refuser, rendez-vous dans la ';
								$box .= '<a href="' . APP_ROOT . 'bases/base-' . $ob->getRPlace() . '/view-commercialplateforme">plateforme commerciale</a>';
								$box .= '.</p>';
							} elseif ($standby == TRUE) {
								$box .= '<h5>Route commerciale en suspens</h5>';
								$box .= '<p>En temps de guerre, toutes les transactions entre votre base et la sienne sont bloquées. ';
								$box .= 'Si vous voulez voir le statut de la route ou y mettre fin, rendez-vous dans la ';
								$box .= '<a href="' . APP_ROOT . 'bases/base-' . $ob->getRPlace() . '/view-commercialplateforme">plateforme commerciale</a>';
								$box .= '.</p>';
							} else {
								# check si encore des routes libres
								$S_CRM1 = ASM::$crm->getCurrentSession();
								ASM::$crm->changeSession($ob->routeManager);
								$usedRoutes = ASM::$crm->size();
								for ($i = 0; $i < ASM::$crm->size(); $i++) {
									if (ASM::$crm->get($i)->getROrbitalBaseLinked() == $ob->getRPlace()) {
										if (ASM::$crm->get($i)->getStatement() == CRM_PROPOSED) {
											# on soustrait les routes qu'on nous a proposé et qu'on n'a pas encore accepté
											$usedRoutes--;
										}
									}
								}
								$maxRoute = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob->getLevelCommercialPlateforme(), 'nbRoutesMax');
								if ($usedRoutes < $maxRoute) {
									$distance = Game::getDistance($ob->getXSystem(), $place->getXSystem(), $ob->getYSystem(), $place->getYSystem());
									$bonusA = ($ob->getSector() != $place->getRSector()) ? CRM_ROUTEBONUSSECTOR : 1;
									$bonusB = (CTR::$data->get('playerInfo')->get('color')) != $place->getPlayerColor() ? CRM_ROUTEBONUSCOLOR : 1;
									$price = Game::getRCPrice($distance, $ob->getPlanetPopulation(), $place->getPopulation(), CRM_COEFROUTEPRICE);
									$income = Game::getRCIncome($distance, $ob->getPlanetPopulation(), $place->getPopulation(), CRM_COEFROUTEINCOME, $bonusA, $bonusB);

									$box .= '<h5>Proposer une route commerciale</h5>';

									$box .= '<div class="rc">';
										$box .= '<p>Longueur de la route <strong>' . Format::numberFormat($distance) . ' Al.</strong></p>';
										$box .= '<p>Coût de la mise en place <strong>' . Format::numberFormat($price) . ' <img alt="credit" src="' . MEDIA . 'resources/credit.png" class="icon-color"></strong></p>';
										$box .= '<p>Revenu par relève <strong>' . Format::numberFormat($income) . ' <img alt="credit" src="' . MEDIA . 'resources/credit.png" class="icon-color"></strong></p>';

										if (CTR::$data->get('playerInfo')->get('credit') >= $price) {
											# bouton actif
											$box .= '<a class="button" href="' . APP_ROOT . 'action/a-proposeroute/basefrom-' . $ob->getRPlace() . '/baseto-' . $place->getId() . '/redirect-' . $place->getId() . '">';
												$box .= 'proposer';
											$box .= '</a>';
										} else {
											# bouton pas actifs
											$box .= '<span class="button">';
												$box .= 'crédits insuffisants';
											$box .= '</span>';
										}
									$box .= '</div>';
								} else {
									$box .= '<h5>Proposer une route commerciale</h5>';
									$box .= '<p class="info">Toutes vos routes commerciales sont déjà créées. Pour pouvoir proposer une nouvelle route, il faut soit augmenter le niveau de votre plateforme commerciale, soit annuler une route existante.</p>';
								}
								ASM::$crm->changeSession($S_CRM1);
							}
						}
					} else {
						$box .= '<h5>Proposer une route commerciale</h5>';
						$box .= '<p class="info">Impossible de proposer une route commerciale à ce joueur car il n\'a pas encore de plateforme commerciale.</p>';
					}
				} else {
					$box .= '<h5>Proposer une route commerciale</h5>';
					$box .= '<p class="info">Pour proposer une route commerciale, il faut construire la plateforme commerciale.</p>';
				}
			}
			if ($sendResources == TRUE) {
				$tmpLink = '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/sendResource.png" alt="ressources" /></a>';
				$box .= '<h5>Envoyer des ressources</h5>';

				// ENVOI DE RESSOURCES
				$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $ob->getLevelRefinery(), 'storageSpace');
				$currentStorage = $ob->getResourcesStorage();
				$maxResourcesToSend = $currentStorage - ($storageSpace * OBM_STOCKLIMIT);
				if ($maxResourcesToSend <= 0) {
					$box .= '<p class="info">Pour pouvoir envoyer des ressources, il faut que votre entrepôt soit à ' . OBM_STOCKLIMIT * 100 . '% rempli.</p>';
				} else {	
					$box .= '<div class="rc">';
						$box .= '<p>Ressources en stock <strong>' . Format::numberFormat($currentStorage) . ' <img alt="ressource" src="' . MEDIA . 'resources/resource.png" class="icon-color"></strong></p>';
						$box .= '<p>Capacité d\'envoi maximum <strong>' . Format::numberFormat($maxResourcesToSend) . ' <img alt="ressource" src="' . MEDIA . 'resources/resource.png" class="icon-color"></strong></p>';

						$box .= '<form action="' . APP_ROOT . 'action/a-giveresource/baseid-' . $ob->getRPlace() . '/otherbaseid-' . $place->getId() . '/redirect-' . $place->getId() . '" method="POST">';
							$box .= '<p><input type="text" value="0" name="quantity" /></p>';
							$box .= '<p><input type="submit" value="envoyer" /></p>';
						$box .= '</form>';
					$box .= '</div>';
				}
			}
			
		$box  .= '</div>';
		$link .= $tmpLink;
	}

	public static function motherShip($ob, &$link, &$box, $id) {
		$link .= '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/motherShip.png" alt="vaisseau mère" /></a>';

		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$box .= '<h5>Envoyer un vaisseau-mère</h5>';
			$box .= '<p class="info">la fonctionnalité n\'est pas encore implémentée. Soyez un peu patient !</p>';
		$box .= '</div>';
	}

	public static function move($ob, &$link, &$box, $id, $place, $commanderSession) {
		$link .= '<a href="#" class="actionbox-sh" data-target="' . $id . '"><img src="' . MEDIA . 'map/action/move.png" alt="flotte" /></a>';

		$box .= '<div data-id="' . $id . '" class="act-bull" style="display:' . (($id == 1) ? 'block' : 'none') . ';" >';
			$box .= '<h5>Déplacer une flotte</h5>';
			# check s'il y a une place libre dans la destination
			if (count($place->commanders) < 3) {
				# check si au moins 1 commandant disponible
				$S_COM2 = ASM::$com->getCurrentSession();
				ASM::$com->changeSession($commanderSession);
				$commanderQuantity = 0;
				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					if (ASM::$com->get($i)->getStatement() == COM_AFFECTED) {
						$commanderQuantity++;
					}
				}
				if ($commanderQuantity > 0) {
					# check si assez de PA
					if ($place->getRSystem() == $ob->getSystem()) {
						$time = Game::getTimeTravelInSystem($ob->getPosition(), $place->getPosition());
						$pa = Game::getPAToTravel($time);
					} else {
						$time = Game::getTimeTravelOutOfSystem($ob->getXSystem(), $ob->getYSystem(), $place->getXSystem(), $place->getYSystem());
						$pa = Game::getPAToTravel($time);
					}

					if (CTR::$data->get('playerInfo')->get('actionPoint') >= $pa) {
						for ($i = 0; $i < ASM::$com->size(); $i++) {
							$box .= '<a href="' . APP_ROOT . 'action/a-movefleet/commanderid-' . ASM::$com->get($i)->getId() . '/placeid-' . $place->getId() . '/redirect-' . $place->getId() . '" class="commander">';
								$box .= '<img class="avatar" src="' . MEDIA . 'commander/small/c1-l1-c' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="' . ASM::$com->get($i)->getName() . '" />';
								$box .= '<span class="label">';
									$box .= '<strong>' . ASM::$com->get($i)->getName() . '</strong><br />';
									$box .= ASM::$com->get($i)->getPev() . ' pev';
								$box .= '</span>';
								$box .= '<span class="value">';
									$box .= $pa . ' <img alt="points d\'attaque" src="' . MEDIA . 'resources/pa.png" class="icon-color"><br />';
									$box .= Chronos::secondToFormat($time, 'lite') . ' <img alt="temps" src="' . MEDIA . 'resources/time.png" class="icon-color"><br />';
								$box .= '</span>';
							$box .= '</a>';
						}
					} else {
						$box .= '<p class="info">Vous ne disposez pas d\'assez de points d\'attaque pour déplacer une flotte sur cette base.</p>';
					}
				} else {
					$box .= '<p class="info">Vous n\'avez aucun commandant susceptible d\'être transféré sur cette base.</p>';
				}
				ASM::$com->changeSession($S_COM2);
			} else {
				$box .= '<p class="info">Cette base n\'a pas la capacité d\'accueillir une flotte supplémentaire.</p>';
			}
		$box .= '</div>';
	}
}
?>