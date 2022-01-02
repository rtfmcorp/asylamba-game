<?php

/**
 * law Manager
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

class LawManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected VoteLawManager $voteLawManager
	) {
	}
	
	/**
	 * @param int $id
	 * @return Law
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(Law::class)->get($id);
	}
	
	/**
	 * @param int $factionId
	 * @param array $statements
	 * @return array
	 */
	public function getByFactionAndStatements($factionId, $statements = [])
	{
		return $this->entityManager->getRepository(Law::class)->getByFactionAndStatements($factionId, $statements);
	}
	
	/**
	 * @param int $factionId
	 * @param string $type
	 * @return bool
	 */
	public function lawExists($factionId, $type)
	{
		return $this->entityManager->getRepository(Law::class)->lawExists($factionId, $type);
	}

	/**
	 * @param Law $law
	 * @return int
	 */
	public function add(Law $law) {
		$this->entityManager->persist($law);
		$this->entityManager->flush($law);

		return $law->id;
	}

	/**
	 * @param Law $law
	 * @return bool
	 */
	public function ballot(Law $law) {
		$votes = $this->voteLawManager->getLawVotes($law);

		$ballot = 0;

		foreach ($votes as $vote) {
			if ($vote->vote) {
				$ballot++;
			} else {
				$ballot--;
			}
		}
		return $ballot >= 0;
	}
}
