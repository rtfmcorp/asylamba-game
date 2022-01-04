<?php

/**
 * news de faction
 *
 * @author Noé Zufferey
 * @copyright Asylamba
 *
 * @package Demeter
 * @update 09.01.15
*/
namespace Asylamba\Modules\Demeter\Manager\Forum;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Demeter\Model\Forum\FactionNews;
use Asylamba\Classes\Library\Parser;

class FactionNewsManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected Parser $parser,
	) {
	}
	
	/**
	 * @param int $id
	 * @return FactionNews
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(FactionNews::class)->get($id);
	}
	
	/**
	 * @param int $factionId
	 * @return FactionNews
	 */
	public function getFactionNews($factionId)
	{
		return $this->entityManager->getRepository(FactionNews::class)->getFactionNews($factionId);
	}
	
	/**
	 * @param int $factionId
	 * @return FactionNews
	 */
	public function getFactionBasicNews($factionId)
	{
		return $this->entityManager->getRepository(FactionNews::class)->getFactionBasicNews($factionId);
	}
	
	/**
	 * @param int $factionId
	 * @return FactionNews
	 */
	public function getFactionPinnedNew($factionId)
	{
		return $this->entityManager->getRepository(FactionNews::class)->getPinnedNew($factionId);
	}

	/**
	 * @param FactionNews $factionNew
	 * @return int
	 */
	public function add(FactionNews $factionNew) {
		$this->entityManager->persist($factionNew);
		$this->entityManager->flush($factionNew);

		return $factionNew->id;
	}

	/**
	 * @param FactionNews $factionNews
	 * @param string $content
	 */
	public function edit(FactionNews $factionNews, $content) {
		$factionNews->oContent = $content;

		$this->parser->parseBigTag = TRUE;

		$factionNews->pContent = $this->parser->parse($content);
		
		$this->entityManager->flush($factionNews);
	}
}
