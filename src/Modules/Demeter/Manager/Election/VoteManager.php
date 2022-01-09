<?php

/**
 * Vote Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace App\Modules\Demeter\Manager\Election;

use App\Classes\Entity\EntityManager;

use App\Modules\Demeter\Model\Election\Election;
use App\Modules\Demeter\Model\Election\Vote;

use App\Modules\Zeus\Model\Player;

class VoteManager
{
	public function __construct(protected EntityManager $entityManager)
	{
	}
	
	/**
	 * @param Election $election
	 * @return array
	 */
	public function getElectionVotes(Election $election)
	{
		return $this->entityManager->getRepository(Vote::class)->getElectionVotes($election->id);
	}
	
	/**
	 * @param Player $player
	 * @param Election $election
	 * @return Vote
	 */
	public function getPlayerVote(Player $player, Election $election)
	{
		return $this->entityManager->getRepository(Vote::class)->getPlayerVote($player->id, $election->id);
	}

	/**
	 * @param Vote $vote
	 * @return int
	 */
	public function add(Vote $vote) {
		$this->entityManager->persist($vote);
		$this->entityManager->flush($vote);

		return $vote->id;
	}
}
