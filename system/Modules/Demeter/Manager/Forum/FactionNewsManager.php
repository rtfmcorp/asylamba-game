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
    /** @var EntityManager **/
    protected $entityManager;
    /** @var Parser **/
    protected $parser;
    
    /**
     * @param EntityManager $entityManager
     * @param Parser $parser
     */
    public function __construct(EntityManager $entityManager, Parser $parser)
    {
        $this->entityManager = $entityManager;
        $this->parser = $parser;
    }
    
    /**
     * @param int $id
     * @return FacionNews
     */
    public function get($id)
    {
        return $this->entityManager->getRepository(FactionNews::class)->get($id);
    }
    
    /**
     * @param int $factionId
     * @return FacionNews
     */
    public function getFactionNews($factionId)
    {
        return $this->entityManager->getRepository(FactionNews::class)->getFactionNews($factionId);
    }
    
    /**
     * @param int $factionId
     * @return FacionNews
     */
    public function getFactionBasicNews($factionId)
    {
        return $this->entityManager->getRepository(FactionNews::class)->getFactionBasicNews($factionId);
    }
    
    /**
     * @param int $factionId
     * @return FacionNews
     */
    public function getFactionPinnedNew($factionId)
    {
        return $this->entityManager->getRepository(FactionNews::class)->getPinnedNew($factionId);
    }

    /**
     * @param FactionNews $factionNew
     * @return int
     */
    public function add(FactionNews $factionNew)
    {
        $this->entityManager->persist($factionNew);
        $this->entityManager->flush($factionNew);

        return $factionNew->id;
    }

    /**
     * @param FactionNews $factionNews
     * @param string $content
     */
    public function edit(FactionNews $factionNews, $content)
    {
        $factionNews->oContent = $content;

        $this->parser->parseBigTag = true;

        $factionNews->pContent = $this->parser->parse($content);
        
        $this->entityManager->flush($factionNews);
    }
}
