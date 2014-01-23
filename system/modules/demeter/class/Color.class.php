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

	public function updateStatus() {
		/*
		empire nbrplanet
		cardan nbr pop
		Aphéra tech ?
		Négor nbr credits
		Kovak puissance de l'armée
		Synelle experience
		Nerve Niveau de raffinerie
		*/
		$limit = $this->players / 4;
		if ($limit < 20) { $lmit = 20; }
		if ($limit > 40) { $lmit = 40; }

		$_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();

		switch ($this->id) {
			case 1: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 2: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 3: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 4: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 5: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 6: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
			case 7: 
				ASM::$pam->load(array('rColor' => $this->id), array('experience DESC'));
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(2);	
					} else {
						ASM::$pam->get($i)->setStatus(1);
					}
				}
			break;
		}

		ASM::$pam->save();
		ASM::$pam->changeSession($_PAM1);
	}

	public function ballot($election) {
		$royalisticRegime = array(1, 2, 3);
		$democraticRegime = array(5, 6, 7);

		if (in_array($this->id, $royalisticRegime)) {
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
			
			//monter de rang le gagnant
			ASM::$plm->getCurrentsession();

			ASM::$vom->changeSession($_VOM);

		} elseif (in_array($this->id, $democraticRegime)) {

		} else {

		}
	}


	public function uElection() {
		// 604800s = 7j
		if ($this->electionStatement == Color::MANDATE) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration')) {
				$this->updateStatus();
				$this->electionStatement = Color::CAMPAIGN;
			}
		} elseif ($this->electionStatement == Color::CAMPAIGN) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + Color::CAPAIGNTIME) {
				$this->electionStatement = Color::ELECTION;
			}
		} else {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResource::getInfo($this->id, 'mandateDuration') + Color::VOTATIONTIME) {
				$_ELM = ASM::$elm->getCurrentSession();
				ASM::$elm->newSession();
				ASM::$elm->load(array('rColor' => $this->id), array('id ASC'), array('1', '1'));

				$this->ballot(ASM::$elm->get());
				$this->electionStatement = Color::MANDATE;
				$this->dLastElection = Utils::now();

				ASM::$elm->changeSession($_ELM);
			}
		}
	}
}