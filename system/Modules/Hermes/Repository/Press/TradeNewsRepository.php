<?php

namespace Asylamba\Modules\Hermes\Repository\Press;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\Transaction;

use Asylamba\Modules\Hermes\Model\Press\TradeNews;

class TradeNewsRepository extends AbstractRepository
{
    public function getAll($limit, $offset)
    {
        $statement = $this->connection->prepare(
            'SELECT nt.*, n.title, n.content, n.created_at FROM news__trade nt INNER JOIN news n ON n.id = nt.news_id ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset'
        );
        $statement->execute([
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($n = $this->unitOfWork->getObject(TradeNews::class, $row['news_id'])) !== null) {
                $data[] = $n;
                continue;
            }
            $news = $this->format($row);
            $this->unitOfWork->addObject($news);
            $data[] = $news;
        }
        return $data;
    }
    
    public function get($id)
    {
        if (($n = $this->unitOfWork->getObject(TradeNews::class, $id)) !== null) {
            return $n;
        }
        $statement = $this->connection->prepare('SELECT nt.*, n.title, n.content, n.created_at FROM news__trade nt INNER JOIN news n ON n.id = nt.news_id WHERE nt.news_id = :id');
        $statement->execute(['id' => $id]);
        
        if (($row = $statement->fetch()) === false) {
            return null;
        }
        $news = $this->format($row);
        $this->unitOfWork->addObject($news);
        return $news;
    }
    
    public function insert($news)
    {
        $statement = $this->connection->prepare('INSERT INTO news(title, content, created_at, type) VALUES(:title, :content, :created_at, :type)');
        $statement->execute([
            'title' => $news->getTitle(),
            'content' => $news->getContent(),
            'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s'),
            'type' => TradeNews::NEWS_TYPE_TRADE
        ]);
        
        $news->setId($this->connection->lastInsertId());
        
        $statement = $this->connection->prepare('INSERT INTO news__trade(news_id, transaction_id) VALUES(:news_id, :transaction_id)');
        $statement->execute([
            'news_id' => $news->getId(),
            'transaction_id' => $news->getTransaction()->getId()
        ]);
    }
    
    public function update($news)
    {
    }
    
    public function remove($news)
    {
    }
    
    public function format($data)
    {
        return
            (new TradeNews())
            ->setId((int) $data['news_id'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setTransaction($this->unitOfWork->getRepository(Transaction::class)->get($data['transaction_id']))
        ;
    }
}
