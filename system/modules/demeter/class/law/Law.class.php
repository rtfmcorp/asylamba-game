<?php

/**
 * loi
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/

class Law {
	const VOTATION					= 0;
	const EFFECTIVE					= 1;
	const OBSOLETE					= 2;
	const REFUSED					= 3;

	const VOTEDURATION 				= 172800;

	public $id					= 0;
	public $rColorCreator 		= 0;
	public $type 				= '';
	public $options 			= array();
	public $statement 			= 0;
	public $dEndVotation		= '';
	public $dEnd 	 			= '';
	public $dCreation 			= '';

	public function getId() { return $this->id; }

	public function ballot() {
		$_VLM = ASM::$vlm->getCurrentsession();
		ASM::$vlm->load(array('rLaw' => $this->id));
		$ballot = 0;
		for ($i = 0; $i < ASM::$vlm->size(); $i++) {
			if (ASM::$vlm->vote) {
				$ballot++;
			} else {
				$ballot--;
			}
		}
		ASM::$vlm->changeSession($_VLM);

		if ($ballot > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function uLaw() {
		if ($this->satement == Law::VOTATION) {
			if ($this->dEndVotation >= Utils::now()) {
				$ballot = $this->ballot();
				if ($ballot) {
					//accepter la loi
					$this->statement = EFFECTIVE;
					//envoyer un message
				} else {
					//refuser la loi
					$this->statement = REFUSED;
					//envoyer un message
				}
			}
		} elseif ($this->statement == Law::EFFECTIVE) {
			if ($this->dEnd >= Utils::now()) {
					//finir la loi
					$this->statement = OBSOLETE;
			}
		}
	}
}