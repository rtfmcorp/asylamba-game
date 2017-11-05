<?php

namespace Asylamba\Modules\Hermes\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Daemon\ClientManager;

use Asylamba\Classes\Library\WS\ConnectionHandler;

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;
use Asylamba\Modules\Hermes\Model\Press\PoliticNews;
use Asylamba\Modules\Hermes\Model\Press\TradeNews;

class NewsManager
{
    /** @var EntityManager **/
    protected $entityManager;
    /** @var ClientManager **/
    protected $clientManager;
    /** @var array **/
    protected $classes = [
        News::NEWS_TYPE_MILITARY => MilitaryNews::class,
        News::NEWS_TYPE_POLITICS => PoliticNews::class,
        News::NEWS_TYPE_TRADE => TradeNews::class
    ];
    
    /**
     * @param EntityManager $entityManager
     * @param ClientManager $clientManager
     */
    public function __construct(EntityManager $entityManager, ClientManager $clientManager)
    {
        $this->entityManager = $entityManager;
        $this->clientManager = $clientManager;
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
    
    public function getTopNews()
    {
        return $this
            ->entityManager
            ->getRepository(News::class)
            ->getTopNews()
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
    
    public function get($id)
    {
        return $this
            ->entityManager
            ->getRepository(News::class)
            ->get($id)
        ;
    }
    
    public function create(News $news)
    {
        $news->setCreatedAt(new \DateTime());
        $news->setUpdatedAt(new \DateTime());
        
        $this->entityManager->persist($news);
        $this->entityManager->flush($news);
        
        $this->clientManager->broadcast(json_encode([
            'type' => ConnectionHandler::EVENT_NEWS_CREATION,
            'news' => $news
        ]));
    }
}
