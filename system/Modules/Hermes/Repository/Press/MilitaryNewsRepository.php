<?php

namespace Asylamba\Modules\Hermes\Repository\Press;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Zeus\Model\Player;

use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;

class MilitaryNewsRepository extends AbstractRepository
{
    public function getAll($limit, $offset)
    {
        $statement = $this->connection->prepare(
            'SELECT nm.*, n.title, n.content, n.created_at FROM news__military nm INNER JOIN news n ON n.id = nm.news_id ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset'
        );
        $statement->execute([
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($n = $this->unitOfWork->getObject(MilitaryNews::class, $row['news_id'])) !== null) {
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
        if (($n = $this->unitOfWork->getObject(MilitaryNews::class, $id)) !== null) {
            return $n;
        }
        $statement = $this->connection->prepare('SELECT nm.*, n.title, n.content, n.created_at FROM news__military nm INNER JOIN news n ON n.id = nm.news_id WHERE nm.news_id = :id');
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
        
        $statement = $this->connection->prepare('INSERT INTO news__military(news_id, attacker_id, defender_id, place_id, type, is_victory) VALUES(:news_id, :attacker_id, :defender_id, :place_id, :type, :is_victory)');
        $statement->execute([
            'news_id' => $news->getId(),
            'attacker_id' => $news->getAttacker()->getId(),
            'defender_id' => $news->getDefender()->getId(),
            'place_id' => $news->getPlace()->getId(),
            'type' => $news->getType(),
            'is_victory' => $news->getIsVictory()
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
            (new MilitaryNews())
            ->setId((int) $data['news_id'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setAttacker($this->unitOfWork->getRepository(Player::class)->get($data['attacker_id']))
            ->setDefender($this->unitOfWork->getRepository(Player::class)->get($data['defender_id']))
            ->setPlace($this->unitOfWork->getRepository(Place::class)->get($data['place_id']))
            ->setType($data['type'])
            ->setIsVictory($data['is_victory'])
        ;
    }
}
