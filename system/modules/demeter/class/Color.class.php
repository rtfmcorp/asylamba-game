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

	const CAMPAIGNTIME 		= 604800;
	const ELECTIONTIME		= 604800;

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

	public function updateStatus() {
		include_once ZEUS;

		$limit = round($this->players / 4);
		if ($limit < 20) { $limit = 20; }
		if ($limit > 40) { $limit = 40; }

		$_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();


		ASM::$pam->load(array('rColor' => $this->id), array('factionPoint DESC'));
		for ($i = 0; $i < ASM::$pam->size(); $i++) {
			if (ASM::$pam->get($i)->status < PAM_GOVERNMENT) {
				if ($i < $limit) {
					ASM::$pam->get($i)->status = PAM_PARLIAMENT;
				} else {
					ASM::$pam->get($i)->status = PAM_STANDARD;
				}
			}
		}
		ASM::$pam->changeSession($_PAM1);
	}

	public function ballot($election) {
		$royalisticRegime = array(1, 2, 3);
		$democraticRegime = array(5, 6, 7);

		$_PAM1 = ASM::$pam->getCurrentsession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('status' => array(PAM_GOVERNMENT, PAM_CHIEF)));
		for ($i = 0; $i < ASM::$pam->size(); $i++) {
			ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
		}
		ASM::$pam->save();

		ASM::$pam->changeSession($_PAM1);

		$_VOM = ASM::$vom->getCurrentSession();
		ASM::$vom->load(array('rElection' => $election->id));
		$ballot = array();

		for ($i = 0; $i < ASM::$vom->size(); $i++) {
			if (array_key_exists(ASM::$vom->get($i)->rCandidate, $ballot)) {
				$ballot[ASM::$vom->get($i)->rCandidate]++;
			} else {
				$ballot[ASM::$vom->get($i)->rCandidate] = 1;
			}
		}

		if (in_array($this->id, $royalisticRegime)) {		
			if (count($ballot) > 0) {	
				arsort($ballot);
				reset($ballot);

				$_PAM2 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession();
				ASM::$pam->load(array('id' => key($ballot)));
				ASM::$pam->get()->setStatus(PAM_CHIEF);
				ASM::$pam->save();

				ASM::$pam->changeSession($_PAM2);
			}
			ASM::$vom->changeSession($_VOM);

		} elseif (in_array($this->id, $democraticRegime)) {
			if (count($ballot) > 0) {
						
				arsort($ballot);
				reset($ballot);
				$_PAM2 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				$keys = array();

				$nbr = (count($ballot) > 6) ? 6: count($ballot);

				for ($i = 0; $i < $nbr; $i++) {
					$keys[$i] = key($ballot);
					next($ballot);
				}

				ASM::$pam->load(array('id' => $keys));

				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					ASM::$pam->get($i)->setStatus(PAM_GOVERNMENT);
				}

				ASM::$pam->save();

				ASM::$pam->changeSession($_PAM2);
			}

			ASM::$vom->changeSession($_VOM);
		} else {
			if (count($ballot) > 0) {	
				reset($ballot);
				$_PAM2 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				$keys = array();

				$nbr = (count($ballot) > 6) ? 6: count($ballot);

				for ($i = 0; $i < $nbr; $i++) {
					$keys[$i] = key($ballot);
					next($ballot);
				}
				$aleaNbr = rand(0, $nbr - 1);

				ASM::$pam->load(array('id' => $keys[$aleaNbr]));
				ASM::$pam->get()->setStatus(PAM_CHIEF);
				ASM::$pam->save();
				ASM::$pam->changeSession($_PAM2);
			}
		}
	}


	public function uElection() {
		// 604800s = 7j
		if ($this->electionStatement == self::MANDATE) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration')) {
				$this->updateStatus();
				$this->electionStatement = self::CAMPAIGN;
			}
		} elseif ($this->electionStatement == self::CAMPAIGN) {			
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + self::CAMPAIGNTIME) {
				$this->electionStatement = self::ELECTION;
			}
		} else {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + self::ELECTIONTIME + CAMPAIGNTIME) {
				$_ELM = ASM::$elm->getCurrentSession();
				ASM::$elm->newSession();
				ASM::$elm->load(array('rColor' => $this->id), array('id DESC'), array('0', '1'));
				$this->ballot(ASM::$elm->get());
				$this->electionStatement = self::MANDATE;
				$this->dLastElection = Utils::now();

				ASM::$elm->changeSession($_ELM);
			}
		}
	}
}