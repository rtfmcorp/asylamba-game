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
	const COEFFPOINTCONQUERDONE		= 10;
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

		$limit = $this->players / 4;
		if ($limit < 20) { $lmit = 20; }
		if ($limit > 40) { $lmit = 40; }

		$_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();

		/*
		empire :
			+
				DONE construire un batiment
				DONE construire un gros vaisseaux
				DONE conquête
			-
				TODO détruire un bâtiment
				DONE perdre une panète

		Négore
			+
				DONE avoir une nouvelle route
				TODO faire une vente/achat intéressant
			-
				DONE perdre une route
				TODO faire un vente/achat pas intéressant

		Cardan
			+
				DONE prendre une planète, bonus si elle et hors secteur
				TODO faire un don à la faction
			-
				DONE perdre une planète

		Kovakh
			+
				DONE contruire 1 petit vaisseau
				DONE construire 1 gros vaisseau
				DONE gagner un combat
				DONE nomer une planète militaire
			-
				DONE perdre un combat
				DONE dénommer une planète militaire
		
		Synelle
			+
				DONE défense victorieuse
			-
				MAYBE TODO attaquer un autre joueur

		Nerve
			+
				DONE nommer une planète indus
				DONE up un batiment indus (raf, doc 1 et 2)
				DONE prendre une planète (points selon coeff resources)

			-
				DONE dénommer une plan indus
				TODO déup un batiment indus
				DONE perdre une planète (points selon coeffresources)

		Aphéra
			+
				DONE trouver une recherche
				DONE trouver une techno
				TODO espionnage réussi

		*/

			ASM::$pam->load(array('rColor' => $this->id), array('factionPoint DESC'));
			for ($i = 0; $i < ASM::$pam->size(); $i++) {
				if (ASM::$pam->get($i)->status < PAM_GOVERNMENT) {
					if ($i < $limit) {
						ASM::$pam->get($i)->setStatus(PAM_PARLIAMENT);
					} else {
						ASM::$pam->get($i)->setStatus(PAM_STANDARD);
					}
				}
			}

		// ASM::$pam->save();
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