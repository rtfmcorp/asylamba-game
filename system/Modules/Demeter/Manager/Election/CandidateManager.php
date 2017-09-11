<?php

/**
 * Candidate Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace Asylamba\Modules\Demeter\Manager\Election;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Demeter\Model\Election\Election;
use Asylamba\Modules\Demeter\Model\Election\Candidate;

use Asylamba\Modules\Zeus\Model\Player;

class CandidateManager
{
    /** @var EntityManager **/
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param int $id
     * @return Candidate
     */
    public function get($id)
    {
        return $this->entityManager->getRepository(Candidate::class)->get($id);
    }

    /**
     * @param Election $election
     * @return array
     */
    public function getByElection(Election $election)
    {
        return $this->entityManager->getRepository(Candidate::class)->getByElection($election->id);
    }
    
    /**
     * @param Election $election
     * @param Player $player
     * @return Candidate
     */
    public function getByElectionAndPlayer(Election $election, Player $player)
    {
        return $this->entityManager->getRepository(Candidate::class)->getByElectionAndPlayer($election->id, $player->id);
    }
    
    /**
     * @param Candidate $candidate
     * @return int
     */
    public function add(Candidate $candidate)
    {
        $this->entityManager->persist($candidate);
        $this->entityManager->flush($candidate);

        return $candidate->id;
    }
}
