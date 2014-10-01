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
	#constantes de prestiges
	#empire
	const POINTCONQUER				= 100;
	const POINTBUILDBIGSHIP			= 25;
	#negore
	const COEFFPOINTTRANSACTION		= 10;
	#cardan
	const BONUSOUTOFSECTOR			= 50;
	#kovakh
	const POINTBUILDLITTLESHIP 		= 1;
	const POINTCHANGETYPE 			= 50;
	const POINTBATTLE				= 2;
	#Synelle
	const POINTATTACKPLAYER 		= -10;
	const POINTDENFENDTODO  		= 20;
	#Nerve
	const COEFFPOINTCONQUER			= 10;
	#Aphéra
	const POINTSPY					= 10;
	const POINTRESEARCH				= 5;

	#const
	const NBRGOVERNMENT 	= 6;

	const CAMPAIGNTIME 		= 345600;
	const ELECTIONTIME		= 172800;

	const ALIVE 			= 1;
	const DEAD 				= 0;

	const MANDATE		 	= 1;
	const CAMPAIGN		 	= 2;
	const ELECTION 			= 3;

	public $id 					= 0;
	public $alive 				= 0;
	public $credits				= 0;
	public $players 			= 0;
	public $activePlayers 		= 0;
	public $points				= 0;
	public $sectors				= 0;
	public $electionStatement	= 0;
	public $dLastElection		= '';

	public function getId() { return $this->id; }

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

		ASM::$pam->load(array('rColor' => $this->id), array('factionPoint', 'DESC'));
		for ($i = 0; $i < ASM::$pam->size(); $i++) {
			if (ASM::$pam->get($i)->status < PAM_TREASURER) {
				if ($i < $limit) {
					ASM::$pam->get($i)->status = PAM_PARLIAMENT;
				} else {
					ASM::$pam->get($i)->status = PAM_STANDARD;
				}
			}
		}
		ASM::$pam->changeSession($_PAM1);
	}

	private function ballot($election) {
		$royalisticRegime = array(1, 2, 3);
		$democraticRegime = array(5, 6, 7);

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

		if (count($ballot) > 0) {
			$_PAM1 = ASM::$pam->getCurrentsession();
			ASM::$pam->newSession(FALSE);
			ASM::$pam->load(array('status' => array(PAM_TREASURER, PAM_WARLORD, PAM_MINISTER, PAM_CHIEF), 'rColor' => $this->id));
			for ($i = 0; $i < ASM::$pam->size(); $i++) {
				ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
			}

			ASM::$pam->changeSession($_PAM1);
		}

		if (in_array($this->id, $royalisticRegime)) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				$_PAM2 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(array('id' => key($ballot)));
				ASM::$pam->get()->setStatus(PAM_CHIEF);


				ASM::$pam->changeSession($_PAM2);
			}
			ASM::$vom->changeSession($_VOM);

		} elseif (in_array($this->id, $democraticRegime)) {
			if (count($ballot) > 0) {
						
				arsort($ballot);
				reset($ballot);
				$_PAM2 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession(FALSE);
				$keys = array();

				$nbr = (count($ballot) > 4) ? 4 : count($ballot);

				for ($i = 0; $i < $nbr; $i++) {
					$keys[$i] = key($ballot);
					next($ballot);
				}

				ASM::$pam->load(array('id' => $keys));
				$preferences = array(array(6, TRUE), array(5, TRUE), array(4, TRUE), array(3, TRUE));

				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					$_CAM = ASM::$cam->getCurrentSession();
					ASM::$cam->newSession();
					ASM::$cam->load(array('rPlayer' => ASM::$pam->get($i)->id));

					$preferences[ASM::$cam->get()->treasurerChoice - 1][0] = PAM_TREASURER;
					$preferences[ASM::$cam->get()->warlordChoice - 1][0] = PAM_WARLORD; 
					$preferences[ASM::$cam->get()->ministerChoice - 1][0] = PAM_MINISTER; 
					$preferences[ASM::$cam->get()->chiefChoice - 1][0] = PAM_CHIEF;

					for ($i = 0; $i < $nbr; $i++) {
						if ($preferences[$i][1]) {
							ASM::$pam->get($i)->status = $preferences[$i][0];
							$preferences[$i][1] = FALSE;
							break;
						}
					}

					ASM::$cam->changeSession($_CAM);
				}



				ASM::$pam->changeSession($_PAM2);
			}

			ASM::$vom->changeSession($_VOM);
		} else {
			if (count($ballot) > 0) {	
				reset($ballot);
				$_PAM2 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession(FALSE);
				$keys = array();

				$nbr = (count($ballot) > 6) ? 6: count($ballot);

				for ($i = 0; $i < $nbr; $i++) {
					$keys[$i] = key($ballot);
					next($ballot);
				}
				$aleaNbr = rand(0, $nbr - 1);

				ASM::$pam->load(array('id' => $keys[$aleaNbr]));
				ASM::$pam->get()->setStatus(PAM_CHIEF);

				ASM::$pam->changeSession($_PAM2);
			}
		}
	}

	private function sendNotif($rPlayer, $department, $hasWin = TRUE) {
		if ($haswin) {
			$resources = ColorResource::getInfo($this->id, 'status');
			$notif = new Notification();
				$notif->setRPlayer($rPlayer);
				$notif->setTitle('Vous avez était élu');
				$notif->addBeg()
					->addTxt('Le peule a voté pour vous lors des dernières élections. Vous êtes désormais le ' . $resources[$department]);
				ASM::$ntm->add($notif);
		} else {
			$notif = new Notification();
				$notif->setRPlayer($rPlayer);
				$notif->setTitle('Vous n\'avez pas été élu');
				ASM::$ntm->add($notif);
		}
	}


	public function uElection() {
		// 604800s = 7j
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
	}
}