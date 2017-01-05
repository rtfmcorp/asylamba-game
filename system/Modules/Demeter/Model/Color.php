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

namespace Asylamba\Modules\Demeter\Model;

class Color {
	# Regime
	const DEMOCRATIC 				= 1;
	const ROYALISTIC 				= 2;
	const THEOCRATIC 				= 3;

	# Relation avec les autres factions
	const NEUTRAL 					= 0;
	const PEACE 					= 1;
	const ALLY 						= 2;
	const ENEMY 					= 3;

	# constantes de prestiges
	const TWO_POINTS_PER_LEVEL 		= 2;
	const FOUR_POINTS_PER_LEVEL 	= 4;
	## empire
	const POINTCONQUER				= 100;
	const POINTBUILDBIGSHIP			= 25;

	## negore
	const MIN_PRICE 				= 10000;
	const COEF_POINT_SELLING 		= 0.00002; 	# == * 50K

	## cardan
	const BONUSOUTOFSECTOR			= 20;
	const POINTDONATE				= 10;
	const COEFPOINTDONATE 			= 0.0001;

	## kovakh
	const POINTBUILDLITTLESHIP 		= 1;
	const POINTCHANGETYPE 			= 50;
	const POINT_BATTLE_WIN			= 10;
	const POINT_BATTLE_LOOSE 		= 20;

	## Synelle
	const POINTDEFEND		  		= 20;

	## Nerve
	const COEFFPOINTCONQUER			= 10;
	// POINTCHANGETYPE aussi

	## Aphéra
	const POINTSPY					= 10;
	const POINTRESEARCH				= 2;

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

	const NOT_WIN 					= 0;
	const WIN 						= 1;

	# attributs issus de la db
	public $id 						= 0;
	public $alive 					= 0;
	public $isWinner 				= 0;
	public $credits					= 0;
	public $players 				= 0;
	public $activePlayers 			= 0;
	public $rankingPoints 			= 0;
	public $points					= 0;
	public $sectors					= 0;
	public $electionStatement		= 0;
	public $isClosed				= 0;
	public $description				= 0;
	public $dClaimVictory			= '';
	public $dLastElection			= '';
	public $isInGame 				= 0;

	# attributs issus des resources
	public $officialName 			= '';
	public $popularName 			= '';
	public $government 				= '';
	public $demonym 				= '';
	public $factionPoint 			= '';
	public $status 					= [''];
	public $regime 					= 0;
	public $devise 					= '';
	public $desc1 					= '';
	public $desc2 					= '';
	public $desc3 					= '';
	public $desc4 					= '';
	public $bonus 					= []; # array des id des bonus
	public $mandateDuration 		= 0;
	public $senateDesc 				= '';
	public $campaignDesc 			= '';

	public $bonusText 				= ['']; # array des descriptions des bonus

	public $colorLink 				= [];

	public $chiefId					= 0;

	public function getId() { return $this->id; }

	public function increaseCredit($credit) {
		$this->credits += $credit;
	}

	public function decreaseCredit($credit) {
		$this->credits -= $credit;
	}
}