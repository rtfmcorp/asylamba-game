<?php

namespace App\Modules\Hermes\Infrastructure\Repository;

use App\Classes\Entity\AbstractRepository;
use App\Modules\Hermes\Domain\Repository\ConversationRepositoryInterface;

class ConversationRepository extends AbstractRepository implements ConversationRepositoryInterface
{
	public function countPlayerConversations(int $playerId): int
	{
		$qr = $this->connection->prepare(
			'SELECT COUNT(c.id) AS count
			FROM `conversation` AS c
			LEFT JOIN `conversationUser` AS u
			ON u.rConversation = c.id
			WHERE u.rPlayer = :player_id
			AND u.dLastView < c.dLastMessage'
		);
		$qr->execute(['player_id' => $playerId]);
		return $qr->fetch()['count'];
	}

	public function insert($entity)
	{
		// TODO: Implement insert() method.
	}

	public function update($entity)
	{
		// TODO: Implement update() method.
	}

	public function remove($entity)
	{
		// TODO: Implement remove() method.
	}
}
