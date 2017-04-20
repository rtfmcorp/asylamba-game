<?php

namespace Asylamba\Modules\Hermes\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hermes\Model\Notification;

class NotificationRepository extends AbstractRepository
{
    public function insert($notification)
    {
		$qr = $this->connection->prepare('INSERT INTO
			notification(rPlayer, title, content, dSending, readed, archived)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$notification->getRPlayer(),
			$notification->getTitle(),
			$notification->getContent(),
			$notification->getDSending(),
			$notification->getReaded(),
			$notification->getArchived()
		));
		$notification->setId($this->connection->lastInsertId());
    }
    
    public function update($notification)
    {
        $statement = $this->connection->prepare(
            'UPDATE notification SET id = ?,
                rPlayer = ?,
                title = ?,
                content = ?,
                dSending = ?,
                readed = ?,
                archived = ?
            WHERE id = ?'
        );
        $statement->execute(array(
            $notification->getId(),
            $notification->getRPlayer(),
            $notification->getTitle(),
            $notification->getContent(),
            $notification->getDSending(),
            $notification->getReaded(),
            $notification->getArchived(),
            $notification->getId()
        ));
    }
    
    public function remove($notification)
    {
		$statement = $this->connection->prepare('DELETE FROM notification WHERE id = ?');
		$statement->execute(array($notification->id));
    }
    
    /**
     * @param int $playerId
     * @return int
     */
    public function removePlayerNotifications($playerId)
    {
		$statement = $this->connection->prepare('DELETE FROM notification WHERE rPlayer = ? AND archived = 0');
		$statement->execute(array($playerId));
        
        return $statement->rowCount();
    }
    
    /**
     * @param array $data
     * @return Notification
     */
    public function format($data)
    {
        $notification = new Notification();
        $notification->setId((int) $data['id']);
        $notification->setRPlayer((int) $data['rPlayer']);
        $notification->setTitle($data['title']);
        $notification->setContent($data['content']);
        $notification->setDSending($data['dSending']);
        $notification->setReaded((bool) $data['readed']);
        $notification->setArchived((bool) $data['archived']);
        return $notification;
    }
}