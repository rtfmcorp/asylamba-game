<?php

/**
 * VoteLawLaw Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/
namespace Asylamba\Modules\Demeter\Manager\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Demeter\Model\Law\VoteLaw;

class VoteLawManager {
	/** @var EntityManager **/
	protected $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param VoteLaw $voteLaw
	 * @return int
	 */
	public function add(VoteLaw $voteLaw) {
		$this->entityManager->persist($voteLaw);
		$this->entityManager->flush($voteLaw);

		return $voteLaw->id;
	}
	
	/**
	 * @param Law $law
	 * @return array
	 */
	public function getLawVotes(Law $law)
	{
		return $this->entityManager->getRepository(VoteLaw::class)->getLawVotes($law->id);
	}
	
	/**
	 * @param int $playerId
	 * @param Law $law
	 * @return bool
	 */
	public function hasVoted($playerId, Law $law)
	{
		return $this->entityManager->getRepository(VoteLaw::class)->hasVoted($playerId, $law->id);
	}
	
	
}
