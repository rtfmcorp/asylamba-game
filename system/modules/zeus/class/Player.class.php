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
	public $name = '';
	public $avatar = '';
	public $status = 1;
	public $description = '';
	public $credit = 0;
	public $uPlayer = '';
	public $experience = 0;
	public $level = 0;
	public $victory = 0;
	public $defeat = 0;
	public $stepTutorial = 0;
	public $iUniversity = 5000;
	public $partNaturalSciences = 25;
	public $partLifeSciences = 25;
	public $partSocialPoliticalSciences = 25;
	public $partInformaticEngineering = 25;
	public $dInscription = '';
	public $dLastConnection = '';
	public $dLastActivity = '';
	public $premium = 0;
	public $statement = 0;

	protected $synchronized = FALSE;

	public function getId()					{ return $this->id; }
	public function getBind()				{ return $this->bind; }
	public function getRColor()				{ return $this->rColor; }
	public function getName()				{ return $this->name; }
	public function getAvatar()				{ return $this->avatar; }
	public function getStatus()				{ return $this->status; }
	public function getDescription()		{ return $this->description; }
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
	public function setDescription($v) 		{ $this->description = $v; }
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
		$token = CTC::createContext();
		$now   = Utils::now();

		if (Utils::interval($this->uPlayer, $now, 'h') > 0) {
			# update time
			$hours = Utils::intervalDates($now, $this->uPlayer);
			$this->uPlayer = $now;

			include_once ATHENA;
			include_once HERMES;
			include_once PROMETHEE;
			include_once ARES;

			# load orbital bases
			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlayer' => $this->id));
			# load the bonus
			$playerBonus = new PlayerBonus($this->id);
			$playerBonus->load();
			# load the commercial routes
			$S_COM1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('rPlayer' => $this->id, 'statement' => array(COM_AFFECTED, COM_MOVING)), array('experience', 'DESC', 'statement', 'ASC'));
			# load the researches
			$S_RSM1 = ASM::$rsm->getCurrentSession();
			ASM::$rsm->newSession();
			ASM::$rsm->load(array('rPlayer' => $this->id));

			foreach ($hours as $key => $hour) {
				CTC::add($hour, $this, 'uCredit', array(ASM::$obm->getCurrentSession(), $playerBonus, ASM::$com->getCurrentSession(), ASM::$rsm->getCurrentSession()));
			}

			ASM::$rsm->changeSession($S_RSM1);
			ASM::$com->changeSession($S_COM1);
			ASM::$obm->changeSession($S_OBM1);
		}

		CTC::applyContext($token);
	}

	public function uCredit($obmSession, $playerBonus, $comSession, $rsmSession) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->changeSession($obmSession);

		$popTax = 0; $nationTax = 0;
		$credits = $this->credit;
		$uniInvests = 0; $schoolInvests = 0; $antiSpyInvests = 0;
		$naturalTech = 0; $lifeTech = 0; $socialTech = 0; $informaticTech = 0;

		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);
			$popTax = Game::getTaxFromPopulation($base->getPlanetPopulation());
			$popTax += $popTax * $playerBonus->bonus->get(PlayerBonus::POPULATION_TAX) / 100;
			$nationTax = $base->getTax() * $popTax / 100;

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

			// investissements
			$uniInvests += $this->iUniversity;
			$naturalTech += ($this->iUniversity * $this->partNaturalSciences / 100);
			$lifeTech += ($this->iUniversity * $this->partLifeSciences / 100);
			$socialTech += ($this->iUniversity * $this->partSocialPoliticalSciences / 100);
			$informaticTech += ($this->iUniversity * $this->partInformaticEngineering / 100);
			$schoolInvests += $base->getISchool();
			$antiSpyInvests += $base->getIAntiSpy();

			// paiement à l'alliance
			//--> faire paiement à l'alliance !!!!
		}

		// si la balance de crédit est positive
		if ($credits >= ($uniInvests + $schoolInvests + $antiSpyInvests)) {
			$credits -= ($uniInvests + $schoolInvests + $antiSpyInvests);
			$newCredit = $credits;
		} else { // si elle est négative
			$ratioDifference = (($uniInvests + $schoolInvests + $antiSpyInvests) == 0) ? 100 : floor($credits / ($uniInvests + $schoolInvests + $antiSpyInvests) * 100);
			$uniInvests = 0; $schoolInvests = 0; $antiSpyInvests = 0;

			for ($i = 0; $i < ASM::$obm->size(); $i++) {
				$orbitalBase = ASM::$obm->get($i);

				$newIUniversity = ceil($this->iUniversity * $ratioDifference / 100);
				$newISchool = ceil($orbitalBase->getISchool() * $ratioDifference / 100);
				$newIAntiSpy = ceil($orbitalBase->getIAntiSpy() * $ratioDifference / 100);
				$orbitalBase->setIUniversity($newIUniversity);
				$orbitalBase->setISchool($newISchool);
				$orbitalBase->setIAntiSpy($newIAntiSpy);
				$credits -= ($newIUniversity + $newISchool + $newIAntiSpy);

				$uniInvests += $newISchool;
				$naturalTech += ($newISchool * $this->partNaturalSciences / 100);
				$lifeTech += ($newISchool * $this->partLifeSciences / 100);
				$socialTech += ($newISchool * $this->partSocialPoliticalSciences / 100);
				$informaticTech += ($newISchool * $this->partInformaticEngineering / 100);
				$schoolInvests += $newIUniversity;
				$antiSpyInvests += $newIAntiSpy;
			}

			$n = new Notification();
			$n->setRPlayer($this->id);
			$n->setTitle('Caisses vides');
			$n->addBeg()->addTxt('Domaine')->addSep();
			$n->addTxt('Vous ne disposez pas d\'assez de crédits.')->addBrk()->addTxt('Les impôts que vous percevez ne suffisent plus à payer vos investissements.');
			$n->addTxt(' Seuls ')->addStg($ratioDifference . '%')->addTxt(' des crédits d\'investissements peuvent être honorés.')->addBrk();
			$n->addTxt(' Vos investissements ont été modifiés afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			$n->addSep()->addLnk('financial', 'vers les finances →');
			$n->addEnd();
			
			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);

			$newCredit = $credits;
		}

		// payer les vaisseaux mères --> to do

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
					$commander->setStatement(COM_ONSALE);
					$commander->setRPlayer(0);

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
				for ($i=0; $i < $comList->size() - 2; $i++) { 
					$n->addTxt($comList->get($i) . ', ');
				}
				$n->addTxt($comList->get($comList->size()-2) . ' et ' . $comList->get($comList->size()-1) . '.');
				$n->addBrk()->addTxt('Ils sont tous allé proposer leurs services sur le marché. Si vous voulez les récupérer, vous pouvez vous y rendre et les racheter.');
			}
			$n->addEnd();
			$S_NTM1 = ASM::$ntm->getCurrentSession();
			ASM::$ntm->newSession();
			ASM::$ntm->add($n);
			ASM::$ntm->changeSession($S_NTM1);
		}

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
		if (intval($credit)) {
			$this->credit += abs($credit);
			if ($this->synchronized) {
				CTR::$data->get('playerInfo')->add('credit', $this->credit);
			}
		} else {
			CTR::$alert->add('Un nombre est requis');
			CTR::$alert->add(' dans increaseCredit() de Player', ALERT_STD_ERROR);
		}
	}

	public function decreaseCredit($credit) {
		if (intval($credit)) {
			$this->credit -= abs($credit);
			if ($this->synchronized) {
				CTR::$data->get('playerInfo')->add('credit', $this->credit);
			}
		} else {
			CTR::$alert->add('Un nombre est requis');
			CTR::$alert->add(' dans decreaseCredit() de Player', ALERT_STD_ERROR);
		}
	}

	public function increaseExperience($exp) {
		if (intval($exp)) {
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
				$n->addSep()->addTxt('Vous pouvez dès lors disposer d\'un espion supplémentaire, pensez à en acheter.');
				$n->addEnd();

				$S_NTM1 = ASM::$ntm->getCurrentSession();
				ASM::$ntm->newSession();
				ASM::$ntm->add($n);
				ASM::$ntm->changeSession($S_NTM1);
			}
		} else {
			CTR::$alert->add('Un nombre est requis', ALERT_BUG_ERROR);
			CTR::$alert->add(' dans increaseExperience() de Player', ALERT_BUG_ERROR);
		}
	}
}