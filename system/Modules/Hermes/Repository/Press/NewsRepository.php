<?php

namespace Asylamba\Modules\Hermes\Repository\Press;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;
use Asylamba\Modules\Hermes\Model\Press\PoliticNews;
use Asylamba\Modules\Hermes\Model\Press\TradeNews;

class NewsRepository extends AbstractRepository
{
    public function getAll($limit, $offset)
    {
        $statement = $this->connection->prepare('SELECT * FROM news ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $statement->execute([
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($n = $this->unitOfWork->getObject(News::class, $row['id'])) !== null) {
                $data[] = $n;
                continue;
            }
            $news = $this->format($row);
            $this->unitOfWork->addObject($news);
            $data[] = $news;
        }
        return $data;
    }
    
    public function countTodaysNews($type)
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) AS nb_news FROM news WHERE created_at >= :today_datetime' . (($type !== null) ? ' AND type = :type' : ''));
        $statement->execute(array_merge([
            'today_datetime' => (new \DateTime('today'))->format('Y-m-d H:i:s')
        ], ($type !== null) ? ['type' => $type] : []));
        
        return (int) $statement->fetch()['nb_news'];
    }
    
    public function insert($news)
    {
    }
    
    public function update($news)
    {
    }
    
    public function remove($news)
    {
    }
    
    public function format($data)
    {
        switch ($data['type']) {
            case News::NEWS_TYPE_MILITARY: return $this->unitOfWork->getRepository(MilitaryNews::class)->get($data['id']);
            case News::NEWS_TYPE_POLITICS: return $this->unitOfWork->getRepository(PoliticNews::class)->get($data['id']);
            case News::NEWS_TYPE_TRADE: return $this->unitOfWork->getRepository(TradeNews::class)->get($data['id']);
        }
    }
}
