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
	public $rColorTarget		= 0;
	public $type 				= '';
	public $statement 			= 0;
	public $duration 			= 0;
	public $dCreation 			= '';

	public function getId() { return $this->id; }

	public function uLaw() {
		if ($this->satement == Law::VOTATION) {
			if (Utils::interval($this->dCreation, Utils::now(), 's') > Law::VOTEDURATION) {
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
			if (Utils::interval($this->dCreation, Utils::now(), 's') > Law::VOTEDURATION + $this->duration) {
				$ballot = $this->ballot();
				if ($ballot) {
					//finir la loi
					$this->statement = OBSOLETE;
				}
			}
		}
	}
}