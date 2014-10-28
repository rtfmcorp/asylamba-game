<?php

/**
 * Message Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
include_once ZEUS;

class Color {
	# Regime
	const DEMOCRATIC 				= 1;
	const ROYALISTIC 				= 2;
	const THEOCRATIC 				= 3;
	# constantes de prestiges
	# empire
	const POINTCONQUER				= 100;
	const POINTBUILDBIGSHIP			= 25;
	# negore
	const COEFFPOINTTRANSACTION		= 10;
	# cardan
	const BONUSOUTOFSECTOR			= 50;
	# kovakh
	const POINTBUILDLITTLESHIP 		= 1;
	const POINTCHANGETYPE 			= 50;
	const POINTBATTLE				= 2;
	# Synelle
	const POINTATTACKPLAYER 		= -10;
	const POINTDENFENDTODO  		= 20;
	# Nerve
	const COEFFPOINTCONQUER			= 10;
	# Aphéra
	const POINTSPY					= 10;
	const POINTRESEARCH				= 5;

	# const
	const NBRGOVERNMENT 			= 6;

	const CAMPAIGNTIME 				= 345600;
	const ELECTIONTIME				= 172800;
	const PUTSCHTIME 				= 25200;

	const PUTSCHPERCENTAGE			= 15;

	const ALIVE 					= 1;
	const DEAD 						= 0;

	const MANDATE		 			= 1;
	const CAMPAIGN		 			= 2;
	const ELECTION 					= 3;

	public $id 						= 0;
	public $alive 					= 0;
	public $credits					= 0;
	public $players 				= 0;
	public $activePlayers 			= 0;
	public $points					= 0;
	public $sectors					= 0;
	public $electionStatement		= 0;
	public $dLastElection			= '';

	public $chiefId					= 0;

	public function getId() { return $this->id; }

	public function getRegime() {
		if (in_array($this->id, array(1, 2, 3))) {
			return self::ROYALISTIC;			
		} elseif (in_array($this->id, array(5, 6, 7))) {
			return self::DEMOCRATIC;
		} else {
			return self::THEOCRATIC;			
		}
	}

	public function increaseCredit($credit) {
		$this->credits = $this->credits + $credit;
	}

	private function updateStatus() {
		include_once ZEUS;

		$limit = round($this->players / 4);
		if ($limit < 10) { $limit = 10; }
		if ($limit > 40) { $limit = 40; }

		$_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);

		ASM::$pam->load(array('rColor' => $this->id), array('factionPoint', 'DESC', 'experience', 'DESC'));
		for ($i = 0; $i < ASM::$pam->size(); $i++) {
			if (ASM::$pam->get($i)->status < PAM_TREASURER) {
				if ($i < $limit) {
					if (ASM::$pam->get($i)->status != PAM_PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer(ASM::$pam->get($i)->id);
						$notif->setTitle('Vous êtes sénateur');
						$notif->addBeg()
							->addTxt('Vos actions vous ont fait gagner assez de prestige pour faire partie du sénat.');
						ASM::$ntm->add($notif);
					}
					ASM::$pam->get($i)->status = PAM_PARLIAMENT;
				} else {
					if (ASM::$pam->get($i)->status == PAM_PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer(ASM::$pam->get($i)->id);
						$notif->setTitle('Vous n\'êtes plus sénateur');
						$notif->addBeg()
							->addTxt('Vous n\'avez plus assez de prestige pour rester dans le sénat.');
						ASM::$ntm->add($notif);
					}
					ASM::$pam->get($i)->status = PAM_STANDARD;
				}
			}
		}
		ASM::$pam->changeSession($_PAM1);
	}

	private function ballot($election) {
		$_PAM1 = ASM::$pam->getCurrentsession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('rColor' => $this->id, 'status' => PAM_CHIEF));
		$chiefId = ASM::$pam->get()->id;
		ASM::$pam->changeSession($_PAM1);

		$_VOM = ASM::$vom->getCurrentSession();
		ASM::$vom->newSession();
		ASM::$vom->load(array('rElection' => $election->id));
		$ballot = array();

		for ($i = 0; $i < ASM::$vom->size(); $i++) {
			if (array_key_exists(ASM::$vom->get($i)->rCandidate, $ballot)) {
				$ballot[ASM::$vom->get($i)->rCandidate]++;
			} else {
				$ballot[ASM::$vom->get($i)->rCandidate] = 1;
			}
		}

		if ($this->getRegime() == self::DEMOCRATIC) {
			if (count($ballot) > 0) {
				$_PAM1 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(array('status' => array(PAM_TREASURER, PAM_WARLORD, PAM_MINISTER, PAM_CHIEF), 'rColor' => $this->id));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
				}

				ASM::$pam->changeSession($_PAM1);

				arsort($ballot);
				reset($ballot);

				$_PAM2 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(array('id' => key($ballot)));
				ASM::$pam->get()->setStatus(PAM_CHIEF);

				$statusArray = ColorResource::getInfo($this->id, 'status');

				$notif = new Notification();
				$notif->setRPlayer(ASM::$pam->get()->id);
				$notif->setTitle('Votre avez été élu');
				$notif->addBeg()
					->addTxt(' Le peuple vous à soutenu, vous avez été élu ' . $statusArray[PAM_CHIEF] . ' de votre faction.');
				ASM::$ntm->add($notif);

				ASM::$pam->changeSession($_PAM2);
			}
			ASM::$vom->changeSession($_VOM);

		} elseif ($this->getRegime() == self::ROYALISTIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				if ($chiefId != key($ballot)) {
					next($ballot);
				}
				if (((current($ballot) / $this->activePlayers) * 100) >= self::PUTSCHPERCENTAGE) {
					$_PAM3 = ASM::$pam->getCurrentsession();
					ASM::$pam->newSession(FALSE);
					ASM::$pam->load(array('status' => array(PAM_TREASURER, PAM_WARLORD, PAM_MINISTER, PAM_CHIEF), 'rColor' => $this->id));
					for ($i = 0; $i < ASM::$pam->size(); $i++) {
						ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
					}
					ASM::$pam->changeSession($_PAM3);

					$_PAM2 = ASM::$pam->getCurrentsession();
					ASM::$pam->newSession(FALSE);
					ASM::$pam->load(array('id' => key($ballot)));
					ASM::$pam->get()->setStatus(PAM_CHIEF);

					$statusArray = ColorResource::getInfo($this->id, 'status');

					$notif = new Notification();
					$notif->setRPlayer(ASM::$pam->get()->id);
					$notif->setTitle('Votre coup d\'état a réussi');
					$notif->addBeg()
						->addTxt(' Le peuple vous à soutenu, vous avez renversé le ' . $statusArray[PAM_CHIEF] . ' de votre faction et avez pris sa place.');
					ASM::$ntm->add($notif);
					
					ASM::$pam->changeSession($_PAM2);
				} else {
					$_PAM2 = ASM::$pam->getCurrentsession();
					ASM::$pam->newSession(FALSE);
					ASM::$pam->load(array('id' => key($ballot)));

					$notif = new Notification();
					$notif->setRPlayer(ASM::$pam->get()->id);
					$notif->setTitle('Votre coup d\'état a échoué');
					$notif->addBeg()
						->addTxt(' Le peuple ne vous a pas soutenu, l\'ancien gouvernement reste en place.');
					ASM::$ntm->add($notif);

					$notif = new Notification();
					$notif->setRPlayer($chiefId);
					$notif->setTitle('Un coup d\'état a échoué');
					$notif->addBeg()
						->addTxt(' Le joueur ')
						->addLnk('diary/player-' . ASM::$pam->get()->id, ASM::$pam->get()->name)
						->addTxt(' a tenté un coup d\'état, celui-ci a échoué');
					ASM::$ntm->add($notif);
					
					ASM::$pam->changeSession($_PAM2);
				}
			}
			ASM::$vom->changeSession($_VOM);
		} else {
			$_PAM4 = ASM::$pam->getCurrentsession();
			ASM::$pam->newSession(FALSE);
			ASM::$pam->load(array('rColor' => $this->id, 'status' => PAM_CHIEF));
			if (ASM::$pam->size() > 0) {
				$_CAM1 = ASM::$pam->getCurrentsession();
				ASM::$cam->newSession();
				ASM::$cam->load(array('rPlayer' => ASM::$pam->get()->id, 'rElection' => $election->id));
				if (ASM::$cam->size() > 0) {
					if (rand(0, 1) == 0) {
						$ballot = array();
					}
				}
				ASM::$cam->changeSession($_CAM1);
			}
			ASM::$pam->changeSession($_PAM4);
			if (count($ballot) > 0) {
				$aleaNbr = rand(0, count($ballot) - 1);
				
				$_PAM3 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(array('status' => array(PAM_TREASURER, PAM_WARLORD, PAM_MINISTER, PAM_CHIEF), 'rColor' => $this->id));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
				}
				ASM::$pam->changeSession($_PAM3);

				for ($i = 0; $i < $aleaNbr; $i++) {
					next($ballot);
				}

				$_PAM2 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(array('id' => key($ballot)));
				ASM::$pam->get()->setStatus(PAM_CHIEF);

				$notif = new Notification();
				$notif->setRPlayer(ASM::$pam->get()->id);
				$notif->setTitle('Vous avez été nommé Guide');
				$notif->addBeg()
					->addTxt(' Les Oracles ont parlé, vous êtes désigné par la Grande Lumière pour guider Cardan vers la Gloire.');
				ASM::$ntm->add($notif);

				ASM::$pam->changeSession($_PAM2);
			} else {
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($chiefId);
				$notif->setTitle('Vous avez été nommé Guide');
				$notif->addBeg()
					->addTxt(' Les Oracles on parlé, vous êtes toujours désigné par la Grande Lumière pour guider Cardan vers la Gloire.');
				ASM::$ntm->add($notif);
			}
		}
	}

	public function uVoteLaw($law, $ballot) {
		if ($ballot) {
			//accepter la loi
			$law->statement = Law::EFFECTIVE;
			//envoyer un message
		} else {
			//refuser la loi
			$law->statement = Law::REFUSED;
			$this->credits += (LawResources::getInfo($law->type, 'price') + 90) / 100;
			//envoyer un message
		}
	}

	public function uFinishBonusLaw($law, $sector) {
		$law->statement = Law::OBSOLETE;
	}

	public function uFinishSectorTaxes($law, $sector) {
		if ($sector->rColor == $this->id) {
			$sector->tax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
	}

	public function uFinishSectorName($law, $sector) {
		if ($sector->rColor == $this->id) {
			$sector->name = $law->options['name'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
	}
	
	public function uFinishExportComercialTaxes($law, $tax) {
		if ($law->options['rColor'] == $this->id) {
			$tax->exportTax = $law->options['taxes'] / 2;
			$tax->importTax = $law->options['taxes'] / 2;
			$law->statement = Law::OBSOLETE;
		} else {
			$tax->exportTax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		}
	}

	public function uFinishImportComercialTaxes($law, $tax) {
		if ($law->options['rColor'] == $this->id) {
			$tax->exportTax = $law->options['taxes'] / 2;
			$tax->importTax = $law->options['taxes'] / 2;

		} else {
			$tax->importTax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		}
	}


	public function uMethod() {
		// 604800s = 7j
		$token = CTC::createContext();

		if ($this->getRegime() == self::DEMOCRATIC) {
			if ($this->electionStatement == self::MANDATE) {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration')) {
					$this->updateStatus();
					$S_ELM = ASM::$elm->getCurrentsession();
					ASM::$elm->newSession();
					$election = new Election();
					$election->rColor = $this->id;

					$date = new DateTime($this->dLastElection);
					$date->modify('+' . ColorResource::getInfo($this->id, 'mandateDuration') + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
					$election->dElection = $date->format('Y-m-d H:i:s');

					ASM::$elm->add($election);
					ASM::$elm->changeSession($S_ELM);
					$this->electionStatement = self::CAMPAIGN;
				}
			} elseif ($this->electionStatement == self::CAMPAIGN) {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + self::CAMPAIGNTIME) {
					$this->electionStatement = self::ELECTION;
				}
			} else {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + self::ELECTIONTIME + self::CAMPAIGNTIME) {
					$_ELM = ASM::$elm->getCurrentSession();
					ASM::$elm->newSession();
					ASM::$elm->load(array('rColor' => $this->id), array('id', 'DESC'), array('0', '1'));
					$this->ballot(ASM::$elm->get());
					$this->electionStatement = self::MANDATE;
					$this->dLastElection = ASM::$elm->get()->dElection;

					ASM::$elm->changeSession($_ELM);
				}
			}
		} elseif ($this->getRegime() == self::ROYALISTIC) {
			if ($this->electionStatement == self::MANDATE) {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration')) {
					$this->updateStatus();
				}
			} elseif ($this->electionStatement == self::ELECTION) {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > self::PUTSCHTIME) {
					$_ELM = ASM::$elm->getCurrentSession();
					ASM::$elm->newSession();
					ASM::$elm->load(array('rColor' => $this->id), array('id', 'DESC'), array('0', '1'));
					$this->ballot(ASM::$elm->get());
					$this->electionStatement = self::MANDATE;
					$this->dLastElection = ASM::$elm->get()->dElection;

					ASM::$elm->changeSession($_ELM);
				}
			}
		} else {
			if ($this->electionStatement == self::MANDATE) {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration')) {
					$this->updateStatus();
					$S_ELM = ASM::$elm->getCurrentsession();
					ASM::$elm->newSession();
					$election = new Election();
					$election->rColor = $this->id;

					$date = new DateTime($this->dLastElection);
					$date->modify('+' . ColorResource::getInfo($this->id, 'mandateDuration') + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
					$election->dElection = $date->format('Y-m-d H:i:s');

					ASM::$elm->add($election);
					ASM::$elm->changeSession($S_ELM);
					$this->electionStatement = self::CAMPAIGN;
				}
			} else {
				if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + self::CAMPAIGNTIME) {
					$_ELM = ASM::$elm->getCurrentSession();
					ASM::$elm->newSession();
					ASM::$elm->load(array('rColor' => $this->id), array('id', 'DESC'), array('0', '1'));
					$this->ballot(ASM::$elm->get());
					$this->electionStatement = self::MANDATE;
					$this->dLastElection = ASM::$elm->get()->dElection;

					ASM::$elm->changeSession($_ELM);
				}
			}
		}

		$_LAM = ASM::$lam->getCurrentSession();
		ASM::$lam->load(array('rColor' => $this->id, 'statement' => array(Law::VOTATION, Law::EFFECTIVE)));

		for ($i = 0; $i < ASM::$lam->size(); $i++) {
			if (ASM::$lam->get($i)->statement == Law::VOTATION && ASM::$lam->get($i)->dEndVotation < Utils::now()) {
				CTC::add(ASM::$lam->get($i)->dEndVotation, $this, 'uVoteLaw', array(ASM::$lam->get($i), ASM::$lam->get($i)->ballot()));
			} elseif (ASM::$lam->get($i)->statement == Law::EFFECTIVE && ASM::$lam->get($i)->dEnd < Utils::now()) {
				if (LawResources::getInfo(ASM::$lam->get($i)->type, 'bonusLaw')) {
					#lois à bonus
					CTC::add(ASM::$lam->get($i)->dEnd, $this, 'uFinishBonusLaw', array(ASM::$lam->get($i), ASM::$sem->get()));
				} else {
					#loi à upgrade
					switch (ASM::$lam->get($i)->type) {
						case 1:
							$_SEM = ASM::$sem->getCurrentsession();
							ASM::$sem->load(array('id' => ASM::$lam->get($i)->options['rSector']));
							CTC::add(ASM::$lam->get($i)->dEnd, $this, 'uFinishSectorTaxes', array(ASM::$lam->get($i), ASM::$sem->get()));
							ASM::$sem->changeSession($_SEM);
							break;
						case 2:
							$_SEM = ASM::$sem->getCurrentsession();
							ASM::$sem->load(array('id' => ASM::$lam->get($i)->options['rSector']));
							CTC::add(ASM::$lam->get($i)->dEnd, $this, 'uFinishSectorName', array(ASM::$lam->get($i), ASM::$sem->get()));
							ASM::$sem->changeSession($_SEM);
							break;
						case 3:
							$_CTM = ASM::$ctm->getCurrentsession();
							ASM::$ctm->load(array('faction' => $this->id, 'relatedFaction' => ASM::$lam->get($i)->options['rColor']));
							CTC::add(ASM::$lam->get($i)->dEnd, $this, 'uFinishExportComercialTaxes', array(ASM::$lam->get($i), ASM::$ctm->get()));
							ASM::$ctm->changeSession($_CTM);
							break;
						case 4:
							$_CTM = ASM::$ctm->getCurrentsession();
							ASM::$ctm->load(array('faction' => $this->id, 'relatedFaction' => ASM::$lam->get($i)->options['rColor']));
							CTC::add(ASM::$lam->get($i)->dEnd, $this, 'uFinishImportComercialTaxes', array(ASM::$lam->get($i), ASM::$ctm->get()));
							ASM::$ctm->changeSession($_CTM);
							break;
						
						default:
							break;
					}
				}
			}
		}

		ASM::$lam->changeSession($_LAM);

		CTC::applyContext($token);
	}
}