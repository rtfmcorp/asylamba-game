<?php

namespace Asylamba\Modules\Hermes\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hermes\Model\Notification;

class NotificationRepository extends AbstractRepository
{
	/**
	 * @param int $id
	 * @return Notification
	 */
	public function get($id)
	{
		if (($n = $this->unitOfWork->getObject(Notification::class, $id)) !== null) {
			return $n;
		}
		$statement = $this->connection->prepare('SELECT * FROM notification WHERE id = :id');
		$statement->execute(['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$notification = $this->format($row);
		$this->unitOfWork->addObject($notification);
		return $notification;
	}
	
	public function getUnreadNotifications($playerId)
	{
		$statement = $this->connection->prepare('SELECT * FROM notification WHERE rPlayer = :player_id AND readed = 0 ORDER BY dSending DESC');
		$statement->execute(['player_id' => $playerId]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($n = $this->unitOfWork->getObject(Notification::class, $row['id'])) !== null) {
				$data[] = $n;
				continue;
			}
			$notification = $this->format($row);
			$this->unitOfWork->addObject($notification);
			$data[] = $notification;
		}
		return $data;
	}
	
	public function getPlayerNotificationsByArchive($playerId, $isArchived)
	{
		$statement = $this->connection->prepare(
			'SELECT * FROM notification WHERE rPlayer = :player_id AND archived = :is_archived ORDER BY dSending DESC LIMIT 50');
		$statement->execute(['player_id' => $playerId, 'is_archived' => $isArchived]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($n = $this->unitOfWork->getObject(Notification::class, $row['id'])) !== null) {
				$data[] = $n;
				continue;
			}
			$notification = $this->format($row);
			$this->unitOfWork->addObject($notification);
			$data[] = $notification;
		}
		return $data;
	}
	
	public function getAllByReadState($isReaded)
	{
		$statement = $this->connection->prepare(
			'SELECT * FROM notification WHERE readed = :is_readed');
		$statement->execute(['is_readed' => $isReaded]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($n = $this->unitOfWork->getObject(Notification::class, $row['id'])) !== null) {
				$data[] = $n;
				continue;
			}
			$notification = $this->format($row);
			$this->unitOfWork->addObject($notification);
			$data[] = $notification;
		}
		return $data;
	}
	
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
			(int) $notification->getReaded(),
			(int) $notification->getArchived()
		));
		$notification->setId($this->connection->lastInsertId());
    }
    
    public function update($notification)
    {
		\Asylamba\Classes\Daemon\Server::debug($notification);
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
            (int) $notification->getReaded(),
            (int) $notification->getArchived(),
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