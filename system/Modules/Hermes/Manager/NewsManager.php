<?php

namespace Asylamba\Modules\Hermes\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;
use Asylamba\Modules\Hermes\Model\Press\PoliticNews;
use Asylamba\Modules\Hermes\Model\Press\TradeNews;

class NewsManager
{
    /** @var EntityManager **/
    protected $entityManager;
    /** @var array **/
    protected $classes = [
        News::NEWS_TYPE_MILITARY => MilitaryNews::class,
        News::NEWS_TYPE_POLITICS => PoliticNews::class,
        News::NEWS_TYPE_TRADE => TradeNews::class
    ];
    
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList($type = null, $limit = 10, $offset = 0)
    {
        return $this
            ->entityManager
            ->getRepository(($type !== null) ? $this->classes[$type] : News::class)
            ->getAll($limit, $offset)
        ;
    }
    
    /**
     * @param string $type
     * @return int
     */
    public function countTodaysNews($type = null)
    {
        return $this
            ->entityManager
            ->getRepository(News::class)
            ->countTodaysNews($type)
        ;
    }
    
    public function create(News $news)
    {
        $news->setCreatedAt(new \DateTime());
        
        $this->entityManager->persist($news);
        $this->entityManager->flush($news);
    }
}
