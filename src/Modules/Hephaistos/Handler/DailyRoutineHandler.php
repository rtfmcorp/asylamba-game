<?php

namespace App\Modules\Hephaistos\Handler;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Classes\Worker\API;
use App\Modules\Hephaistos\Message\DailyRoutineMessage;
use App\Modules\Hephaistos\Routine\DailyRoutine;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DailyRoutineHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected API $api,
		protected string $apiMode,
		protected PlayerManager $playerManager,
		protected int $playerInactiveTimeLimit,
		protected int $playerGlobalInactiveTime,
		protected int $notificationsReadTimeout,
		protected int $notificationsUnreadTimeout
	) {
	}

	public function __invoke(DailyRoutineMessage $message): void
	{
		$this->entityManager->getRepository(Notification::class)->cleanNotifications(
			$this->notificationsReadTimeout,
			$this->notificationsUnreadTimeout
		);

		$players = $this->playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE]);
		$nbPlayers = count($players);
		// @TODO understand this strange loop condition
		for ($i = $nbPlayers - 1; $i >= 0; $i--) {
			$player = $players[$i];
			if (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $this->playerInactiveTimeLimit) {
				$this->playerManager->kill($player->id);
			} elseif (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $this->playerGlobalInactiveTime && $player->statement == Player::ACTIVE) {
				$player->statement = Player::INACTIVE;

				if ($this->apiMode === 'enabled') {
					# sending email API call
					$this->api->sendMail($player->bind, API::TEMPLATE_INACTIVE_PLAYER);
				}
			}
		}
		$this->entityManager->flush(Player::class);
	}
}
