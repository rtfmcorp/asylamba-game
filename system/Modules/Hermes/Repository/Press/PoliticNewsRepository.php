<?php

namespace Asylamba\Modules\Hermes\Repository\Press;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Modules\Hermes\Model\Press\PoliticNews;

class PoliticNewsRepository extends AbstractRepository
{
    public function getAll($limit, $offset)
    {
        $statement = $this->connection->prepare(
            'SELECT np.*, n.title, n.content, n.created_at FROM news__politics np INNER JOIN news n ON n.id = np.news_id ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset'
        );
        $statement->execute([
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($n = $this->unitOfWork->getObject(PoliticNews::class, $row['news_id'])) !== null) {
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
        if (($n = $this->unitOfWork->getObject(PoliticNews::class, $id)) !== null) {
            return $n;
        }
        $statement = $this->connection->prepare('SELECT np.*, n.title, n.content, n.created_at FROM news__politics np INNER JOIN news n ON n.id = np.news_id WHERE np.news_id = :id');
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
            'type' => MilitaryNews::NEWS_TYPE_MILITARY
        ]);
        
        $news->setId($this->connection->lastInsertId());
        
        $statement = $this->connection->prepare('INSERT INTO news__politics(news_id, faction_id, type) VALUES(:news_id, :faction_id, :type)');
        $statement->execute([
            'news_id' => $news->getId(),
            'faction_id' => $news->getFaction()->getId(),
            'type' => $news->getType(),
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
            (new PoliticNews())
            ->setId((int) $data['news_id'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setFaction($this->unitOfWork->getRepository(Color::class)->get($data['faction_id']))
            ->setType($data['type'])
        ;
    }
}