<?php

namespace App\Modules\Hermes\Domain\Repository;

interface ConversationRepositoryInterface
{
	public function countPlayerConversations(int $playerId): int;
}
