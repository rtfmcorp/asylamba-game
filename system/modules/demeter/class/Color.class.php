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

	public function updateRank() {
		/*
		empire nbrplanet
		cardan nbr pop
		Aphéra tech ?
		Négor nbr credits
		Kovak puissance de l'armée
		Synelle experience
		Nerve Niveau de raffinerie
		*/
		$limit = $this->player / 4;
		if ($limit < 20) { $lmit = 20; }
		if ($limit > 40) { $lmit = 40; }

		$_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();

		switch ($this->id) {
			case 1: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 2: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 3: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 4: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 5: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 6: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
			case 7: ASM::$pam->load(array('rColor' => $this->id), array(), array($limit));
			break;
		}

		ASM::$pam->changeSession($_PAM1);
	}

	public function ballot() {

	}


	public function uElection() {
		// 604800s = 7j
		if ($this->electionStatement == MANDATE) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResources::getInfos($this->id, 'mandateDuration')) {
				$this->updateRank();
				$this->electionStatement = CAMPAIGN;
			}
		} else if ($this->electionStatement == CAMPAIGN) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResources::getInfos($this->id, 'mandateDuration') + 604800) {
				$this->electionStatement = ELECTION;
			}
		} else if ($this->electionStatement == ELECTION) {
			if (Utils::interval($this->dLastElection, Utils::now(), 's') > ColorResources::getInfos($this->id, 'mandateDuration') + 604800 * 2) {
				$this->ballot;
				$this->electionStatement = MANDATE;
				$this->dLastElection = Utils::now();
			}
		}
	}
}