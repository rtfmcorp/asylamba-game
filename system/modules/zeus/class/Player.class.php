<?php

/**
 * Player
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @update 20.05.13
 */

class Player {
	public $id = 0; 
	public $bind = 0;
	public $rColor = 0;
	public $rGodfather = NULL;
	public $name = '';
	public $sex = 0;
	public $description = '';
	public $avatar = '';
	public $status = 1;
	public $credit = 0;
	public $uPlayer = '';
	public $experience = 0;
	public $factionPoint = 0;
	public $level = 0;
	public $victory = 0;
	public $defeat = 0;
	public $stepTutorial = 1;
	public $stepDone = FALSE;
	public $iUniversity = 5000;
	public $partNaturalSciences = 25;
	public $partLifeSciences = 25;
	public $partSocialPoliticalSciences = 25;
	public $partInformaticEngineering = 25;
	public $dInscription = '';
	public $dLastConnection = '';
	public $dLastActivity = '';
	public $premium = 0; 	# 0 = publicité, 1 = pas de publicité
	public $statement = 0;

	protected $synchronized = FALSE;

	public function getId()					{ return $this->id; }
	public function getBind()				{ return $this->bind; }
	public function getRColor()				{ return $this->rColor; }
	public function getName()				{ return $this->name; }
	public function getAvatar()				{ return $this->avatar; }
	public function getStatus()				{ return $this->status; }
	public function getCredit()				{ return $this->credit; }
	public function getExperience()			{ return $this->experience; }
	public function getLevel()				{ return $this->level; }
	public function getVictory()			{ return $this->victory; }
	public function getDefeat()				{ return $this->defeat; }
	public function getStepTutorial()		{ return $this->stepTutorial; }
	public function getDInscription()		{ return $this->dInscription; }
	public function getDLastConnection()	{ return $this->dLastConnection; }
	public function getDLastActivity()		{ return $this->dLastActivity; }
	public function getPremium()			{ return $this->premium; }
	public function getStatement()			{ return $this->statement; }

	public function setId($v) { 
		$this->id = $v; 
		if ($v == CTR::$data->get('playerId')) {
			$this->synchronized = TRUE;
		}
	}
	public function setBind($v) {
		$this->bind = $v;
	}
	public function setRColor($v) { 
		$this->rColor = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('color', $v);
		}
	}
	public function setName($v) {
		$this->name = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('name', $v);
		}
	}
	public function setAvatar($v) { 
		$this->avatar = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('avatar', $v);
		}
	}
	public function setStatus($v) 			{ $this->status = $v; }
	public function setCredit($v) { 
		$this->credit = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('credit', $v);
		}
	}
	public function setExperience($v) { 
		$this->experience = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('experience', $v);
		}
	}
	public function setLevel($v) { 
		$this->level = $v; 
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('level', $v);
		}
	}
	public function setVictory($v) 			{ $this->victory = $v; }
	public function setDefeat($v) 			{ $this->defeat = $v; }
	public function setStepTutorial($v) 	{ $this->stepTutorial = $v; }
	public function setDInscription($v) 	{ $this->dInscription = $v; }
	public function setDLastConnection($v) 	{ $this->dLastConnection = $v; }
	public function setDLastActivity($v) 	{ $this->dLastActivity = $v; }
	public function setPremium($v) 			{ $this->premium = $v; }
	public function setStatement($v) 		{ $this->statement = $v; }

	public function increaseVictory($i) 	{ $this->victory += $i; }
	public function increaseDefeat($i) 		{ $this->defeat += $i; }


	// UPDATE METHOD
	public function uMethod() {
		if ($this->statement != PAM_DEAD) {
			$token = CTC::createContext('player');
			$now   = Utils::now();

			if (Utils::interval($this->uPlayer, $now, 'h') > 0) {
				# update time
				$hours = Utils::intervalDates($now, $this->uPlayer);
				$this->uPlayer = $now;

				include_once ATHENA;
				include_once HERMES;
				include_once PROMETHEE;
				include_once ARES;
				include_once DEMETER;
				include_once GAIA;

				# load orbital bases
				$S_OBM1 = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession();
				ASM::$obm->load(array('rPlayer' => $this->id));

				# load the bonus
				$playerBonus = new PlayerBonus($this->id);
				$playerBonus->load();

				# load the commanders
				$S_COM1 = ASM::$com->getCurrentSession();
				ASM::$com->newSession();
				ASM::$com->load(
					array(
						'c.rPlayer' => $this->id,
						'c.statement' => array(Commander::AFFECTED, Commander::MOVING)), 
					array(
						'c.experience', 'DESC',
						'c.statement', 'ASC')
				);

				# load the researches
				$S_RSM1 = ASM::$rsm->getCurrentSession();
				ASM::$rsm->newSession();
				ASM::$rsm->load(array('rPlayer' => $this->id));

				# load the colors (faction)
				$S_CLM1 = ASM::$clm->getCurrentSession();
				ASM::$clm->newSession();
				ASM::$clm->load(array());

				foreach ($hours as $key => $hour) {
					CTC::add($hour, $this, 'uCredit', array(ASM::$obm->getCurrentSession(), $playerBonus, ASM::$com->getCurrentSession(), ASM::$rsm->getCurrentSession(), ASM::$clm->getCurrentSession()));
				}

				ASM::$clm->changeSession($S_CLM1);
				ASM::$rsm->changeSession($S_RSM1);
				ASM::$com->changeSession($S_COM1);
				ASM::$obm->changeSession($S_OBM1);
			}

			CTC::applyContext($token);
		}
	}

	public function uCredit($obmSession, $playerBonus, $comSession, $rsmSession, $clmSession) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->changeSession($obmSession);

		$popTax = 0; $nationTax = 0;
		$credits = $this->credit;
		$schoolInvests = 0; $antiSpyInvests = 0;

		$totalGain = 0;

		# university investments
		$uniInvests = $this->iUniversity;
		$naturalTech = ($this->iUniversity * $this->partNaturalSciences / 100);
		$lifeTech = ($this->iUniversity * $this->partLifeSciences / 100);
		$socialTech = ($this->iUniversity * $this->partSocialPoliticalSciences / 100);
		$informaticTech = ($this->iUniversity * $this->partInformaticEngineering / 100);

		$S_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->changeSession($clmSession);
		
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);
			$popTax = Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase);
			$popTax += $popTax * $playerBonus->bonus->get(PlayerBonus::POPULATION_TAX) / 100;
			$nationTax = $base->tax * $popTax / 100;

			// revenu des routes commerciales
			$routesIncome = 0;
			$S_CRM1 =  ASM::$crm->getCurrentSession();
			ASM::$crm->changeSession($base->routeManager);
			for ($r = 0; $r < ASM::$crm->size(); $r++) {
				if (ASM::$crm->get($r)->getStatement() == CRM_ACTIVE) {
					$routesIncome += ASM::$crm->get($r)->getIncome();
				}
			}
			$routesIncome += $routesIncome * $playerBonus->bonus->get(PlayerBonus::COMMERCIAL_INCOME) / 100;
			ASM::$crm->changeSession($S_CRM1);

			$credits += ($popTax - $nationTax + $routesIncome);
			$totalGain += $popTax - $nationTax + $routesIncome;

			// investments
			$schoolInvests += $base->getISchool();
			$antiSpyInvests += $base->getIAntiSpy();

			// paiement à l'alliance
			if ($this->rColor != 0) {
				for ($j = 0; $j < ASM::$clm->size(); $j++) { 
					if (ASM::$clm->get($j)->id == $base->sectorColor) {
						ASM::$clm->get($j)->increaseCredit($nationTax);
						break;
					}
				}
			}
		}
		ASM::$clm->changeSession($S_CLM1);

		// si la balance de crédit est positive
		$totalInvests = $uniInvests + $schoolInvests + $antiSpyInvests;
		if ($credits >= $totalInvests) {
			$credits -= $totalInvests;
			$newCredit = $credits;
		} else { // si elle est négative
			$n = new Notification();
			$n->setRPlayer($this->id);
			$n->setTitle('Caisses vides');
			$n->addBeg()->addTxt('Domaine')->addSep();
			$n->addTxt('Vous ne disposez pas d\'assez de crédits.')->addBrk()->addTxt('Les impôts que vous percevez ne suffisent plus à payer vos investissements.');

			if ($totalInvests - $uniInvests <= $totalGain) {
				# we can decrease only the uni investments
				$newIUniversity = $totalGain - $schoolInvests - $antiSpyInvests;

				$this->iUniversity = $newIUniversity;
				$credits -= ($newIUniversity + $schoolInvests + $antiSpyInvests);

				# recompute the real amount for each research
				$naturalTech = ($this->iUniversity * $this->partNaturalSciences / 100);
				$lifeTech = ($this->iUniversity * $this->partLifeSciences / 100);
				$socialTech = ($this->iUniversity * $this->partSocialPoliticalSciences / 100);
				$informaticTech = ($this->iUniversity * $this->partInformaticEngineering / 100);

				$n->addBrk()->addTxt(' Vos investissements dans l\'université ont été modifiés afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			} else {
				# we have to decrease the other investments too
				# investments in university to 0
				$this->iUniversity = 0;
				# then we decrease the other investments with a ratio
				$ratioDifference = floor($totalGain / ($schoolInvests + $antiSpyInvests) * 100);

				$naturalTech = 0; $lifeTech = 0; $socialTech = 0; $informaticTech = 0;

				for ($i = 0; $i < ASM::$obm->size(); $i++) {
					$orbitalBase = ASM::$obm->get($i);

					$newISchool = ceil($orbitalBase->getISchool() * $ratioDifference / 100);
					$newIAntiSpy = ceil($orbitalBase->getIAntiSpy() * $ratioDifference / 100);

					$orbitalBase->setISchool($newISchool);
					$orbitalBase->setIAntiSpy($newIAntiSpy);

					$credits -= ($newISchool + $newIAntiSpy);

					$naturalTech += ($newISchool * $this->partNaturalSciences / 100);
					$lifeTech += ($newISchool * $this->partLifeSciences / 100);
					$socialTech += ($newISchool * $this->partSocialPoliticalSciences / 100);
					$informaticTech += ($newISchool * $this->partInformaticEngineering / 100);
					
				}
				$n->addTxt(' Seuls ')->addStg($ratioDifference . '%')->addTxt(' des crédits d\'investissements peuvent être honorés.')->addBrk();
				$n->addTxt(' Vos investissements dans l\'université ont été mis à zéro et les autres diminués de façon pondérée afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			}

			$n->addSep()->addLnk('financial', 'vers les finances →');
			$n->addEnd();
			
			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);

			$newCredit = $credits;
		}

		// payer les commandants
		$nbOfComNotPaid = 0;
		$comList = new ArrayList();
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->changeSession($comSession);
		for ($i = (ASM::$com->size() - 1); $i >= 0; $i--) {
			$commander = ASM::$com->get($i);
			if ($commander->getStatement() == 1 OR $commander->getStatement() == 2) {
				if ($newCredit >= (COM_LVLINCOMECOMMANDER * $commander->getLevel())) {
					$newCredit -= (COM_LVLINCOMECOMMANDER * $commander->getLevel());
				} else {
					# on remet les vaisseaux dans les hangars
					$commander->emptySquadrons();
					
					# on vend le commandant
					$commander->setStatement(COM_ONSALE);
					$commander->setRPlayer(ID_GAIA);

					// TODO : vendre le commandant au marché 
					//			(ou alors le mettre en statement COM_DESERT et supprimer ses escadrilles)

					$comList->add($nbOfComNotPaid, $commander->getName());
					$nbOfComNotPaid++;
				}
			}
		}
		ASM::$com->changeSession($S_COM1);
		// si au moins un commandant n'a pas pu être payé --> envoyer une notif
		if ($nbOfComNotPaid) {	
			$n = new Notification();
			$n->setRPlayer($this->id);
			$n->setTitle('Commandant impayé');

			$n->addBeg()->addTxt('Domaine')->addSep();
			if ($nbOfComNotPaid == 1) {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer votre commandant ' . $comList->get(0) . '. Celui-ci a donc déserté ! ');
				$n->addBrk()->addTxt('Il est allé proposer ses services sur le marché. Si vous voulez le récupérer, vous pouvez vous y rendre et le racheter.');
			} else {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer certains de vos commandants. Ils ont donc déserté ! ')->addBrk();
				$n->addTxt('Voici la liste de ces commandants : ');
				for ($i = 0; $i < $comList->size() - 2; $i++) { 
					$n->addTxt($comList->get($i) . ', ');
				}
				$n->addTxt($comList->get($comList->size() - 2) . ' et ' . $comList->get($comList->size() - 1) . '.');
				$n->addBrk()->addTxt('Ils sont tous allés proposer leurs services sur le marché. Si vous voulez les récupérer, vous pouvez vous y rendre et les racheter.');
			}
			$n->addEnd();
			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);
		}

		// payer l'entretien des vaisseaux
		// vaisseaux affectés
/*		$nbOfShipsNotPaid = 0; 				EN CHANTIER
		$comList = new ArrayList();
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->changeSession($comSession);
		for ($i = (ASM::$com->size() - 1); $i >= 0; $i--) {
			$commander = ASM::$com->get($i);
			if ($commander->getStatement() == 1 OR $commander->getStatement() == 2) {
				if ($newCredit >= (COM_LVLINCOMECOMMANDER * $commander->getLevel())) {
					$newCredit -= (COM_LVLINCOMECOMMANDER * $commander->getLevel());
				} else {
					# on remet les vaisseaux dans les hangars
					$commander->emptySquadrons();
					
					# on vend le commandant
					$commander->setStatement(COM_ONSALE);
					$commander->setRPlayer(ID_GAIA);

					$comList->add($nbOfComNotPaid, $commander->getName());
					$nbOfComNotPaid++;
				}
			}
		}*/
		ASM::$com->changeSession($S_COM1);
		// vaisseaux sur la planète
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);
			$cost = Game::getFleetCost($base->shipStorage, 'on the floor');

			if ($newCredit >= $cost) {
				$newCredit -= $cost;
			} else {
				// n'arrive pas à tous les payer !
				for ($j = ShipResource::SHIP_QUANTITY-1; $j >= 0; $j--) { 
					if ($base->shipStorage[$j] > 0) {
						$unitCost = ShipResource::getInfo($j, 'cost');

						$possibleMaintenable = floor($newCredit / $unitCost);
						if ($possibleMaintenable > $base->shipStorage[$j]) {
							$possibleMaintenable = $base->shipStorage[$j];
						}
						$newCredit -= $possibleMaintenable * $unitCost;

						$toKill = $base->shipStorage[$j] - $possibleMaintenable;
						if ($toKill > 0) {
							$base->removeShipFromDock($j, $toKill);

							$n = new Notification();
							$n->setRPlayer($this->id);
							$n->setTitle('Entretien vaisseau impayé');

							$n->addBeg()->addTxt('Domaine')->addSep();
							if ($toKill == 1) {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien d\'un(e) ' . ShipResource::getInfo($j, 'codeName') . ' sur ' . $base->name . '. Ce vaisseau part donc à la casse ! ');
							} else {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien de ' . $toKill . ' ' . ShipResource::getInfo($j, 'codeName') . 's sur ' . $base->name . '. Ces vaisseaux partent donc à la casse ! ');
							}
							$n->addEnd();
							$S_NTM1 = ASM::$ntm->getCurrentSession();
							ASM::$ntm->newSession();
							ASM::$ntm->add($n);
							ASM::$ntm->changeSession($S_NTM1);
						}
					}
				}
			}
		}
		// vaisseaux en vente TODO
/*		$nbOfShipsNotPaid = 0;
		$comList = new ArrayList();
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->changeSession($comSession);
		for ($i = (ASM::$com->size() - 1); $i >= 0; $i--) {
			$commander = ASM::$com->get($i);
			if ($commander->getStatement() == 1 OR $commander->getStatement() == 2) {
				if ($newCredit >= (COM_LVLINCOMECOMMANDER * $commander->getLevel())) {
					$newCredit -= (COM_LVLINCOMECOMMANDER * $commander->getLevel());
				} else {
					# on remet les vaisseaux dans les hangars
					$commander->emptySquadrons();
					
					# on vend le commandant
					$commander->setStatement(COM_ONSALE);
					$commander->setRPlayer(ID_GAIA);

					// TODO : vendre le commandant au marché 
					//			(ou alors le mettre en statement COM_DESERT et supprimer ses escadrilles)

					$comList->add($nbOfComNotPaid, $commander->getName());
					$nbOfComNotPaid++;
				}
			}
		}
		ASM::$com->changeSession($S_COM1);
		// si des vaisseaux ne peuvent pas être entretenus --> envoyer une notif
		if ($nbOfComNotPaid) {	
			$n = new Notification();
			$n->setRPlayer($this->id);
			$n->setTitle('Commandant impayé');

			$n->addBeg()->addTxt('Domaine')->addSep();
			if ($nbOfComNotPaid == 1) {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer votre commandant ' . $comList->get(0) . '. Celui-ci a donc déserté ! ');
				$n->addBrk()->addTxt('Il est allé proposer ses services sur le marché. Si vous voulez le récupérer, vous pouvez vous y rendre et le racheter.');
			} else {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer certains de vos commandants. Ils ont donc déserté ! ')->addBrk();
				$n->addTxt('Voici la liste de ces commandants : ');
				for ($i = 0; $i < $comList->size() - 2; $i++) { 
					$n->addTxt($comList->get($i) . ', ');
				}
				$n->addTxt($comList->get($comList->size() - 2) . ' et ' . $comList->get($comList->size() - 1) . '.');
				$n->addBrk()->addTxt('Ils sont tous allés proposer leurs services sur le marché. Si vous voulez les récupérer, vous pouvez vous y rendre et les racheter.');
			}
			$n->addEnd();
			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);
		}*/

		// faire les recherches
		$S_RSM1 = ASM::$rsm->getCurrentSession();
		ASM::$rsm->changeSession($rsmSession);
		if (ASM::$rsm->size() == 1) {
			// add the bonus
			$naturalTech += $naturalTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$lifeTech += $lifeTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$socialTech += $socialTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$informaticTech += $informaticTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;

			$tech = ASM::$rsm->get();
			$tech->update($this->id, $naturalTech, $lifeTech, $socialTech, $informaticTech);
		} else {
			CTR::$alert->add('une erreur est survenue lors de la mise à jour des investissements de recherche');
			CTR::$alert->add(' pour le joueur ' . $this->id . '.', ALERT_STD_ERROR);
		}
		ASM::$rsm->changeSession($S_RSM1);

		$this->credit = $newCredit;
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('credit', $newCredit);
		}

		ASM::$obm->changeSession($S_OBM1);
	}

	// OBJECT METHOD
	public function increaseCredit($credit) {
		$this->credit += abs($credit);

		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('credit', $this->credit);
		}
	}

	public function decreaseCredit($credit) {
		$this->credit -= abs($credit);
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('credit', $this->credit);
		}
	}

	public function increaseExperience($exp) {
		$exp = round($exp);
		$this->experience += $exp;
		if ($this->synchronized) {
			CTR::$data->get('playerInfo')->add('experience', $this->experience);
		}
		$nextLevel =  PAM_BASELVLPLAYER * pow(2, ($this->level - 1));
		if ($this->experience >= $nextLevel) {
			$this->level++;
			if ($this->synchronized) {
				CTR::$data->get('playerInfo')->add('level', $this->level);
			}
			$n = new Notification();
			$n->setTitle('Niveau supérieur');
			$n->setRPlayer($this->id);
			$n->addBeg()->addTxt('Félicitations, vous gagnez un niveau, vous êtes ')->addStg('niveau ' . $this->level)->addTxt('.');
			if ($this->level == 2) {
				$n->addSep()->addTxt('Attention, à partir de maintenant vous ne bénéficiez plus de la protection des nouveaux arrivants, n\'importe quel joueur peut désormais piller votre planète. ');
				$n->addTxt('Pensez donc à développer vos flottes pour vous défendre.');
			}
			if ($this->level == 4) {
				$n->addSep()->addTxt('Attention, à partir de maintenant un joueur adverse peut conquérir votre planète ! Si vous n\'en avez plus, le jeu est terminé pour vous. ');
				$n->addTxt('Pensez donc à étendre votre royaume en colonisant d\'autres planètes.');
			}
			$n->addEnd();

			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);

			# parrainage : au niveau 3, le parrain gagne 1M crédits
			if ($this->level == 3 AND $this->rGodfather != NULL) {
				# add 1'000'000 credits to the godfather
				$S_PAM1 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				ASM::$pam->load(array('id' => $this->rGodfather));
				if (ASM::$pam->size() == 1) {
					ASM::$pam->get()->increaseCredit(1000000);

					# send a message to the godfather
					$n = new Notification();
					$n->setRPlayer($this->rGodfather);
					$n->setTitle('Récompense de parrainage');
					$n->addBeg()->addTxt('Un de vos filleuls a atteint le niveau 3. ');
					$n->addTxt('Il s\'agit de ');
					$n->addLnk('embassy/player-' . $this->getId(), '"' . $this->name . '"')->addTxt('.');
					$n->addBrk()->addTxt('Vous venez de gagner 1\'000\'000 crédits. N\'hésitez pas à parrainer d\'autres personnes pour gagner encore plus.');
					$n->addEnd();

					$S_NTM2 = ASM::$ntm->getCurrentSession();
					ASM::$ntm->newSession();
					ASM::$ntm->add($n);
					ASM::$ntm->changeSession($S_NTM2);
				} 
				ASM::$pam->changeSession($S_PAM1);
			}
		}
	}
}