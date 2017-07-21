<?php

namespace Asylamba\Modules\Hephaistos\Routine;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Zeus\Manager\PlayerManager;

use Asylamba\Classes\Worker\API;

use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Model\Player;

use Asylamba\Classes\Library\Utils;

class DailyRoutine
{
	/**
	 * @param EntityManager $entityManager
	 * @param API $api
	 * @param string $apimode
	 * @param PlayerManager $playerManager
	 * @param int $playerInactiveTimeLimit
	 * @param int $playerGlobalInactiveTime
	 * @param int $readTimeout
	 * @param int $unreadTimeout
	 */
	public function execute(
		EntityManager $entityManager,
		API $api,
		$apimode,
		PlayerManager $playerManager,
		$playerInactiveTimeLimit,
		$playerGlobalInactiveTime,
		$readTimeout,
		$unreadTimeout
	)
	{
		$entityManager->getRepository(Notification::class)->cleanNotifications($readTimeout, $unreadTimeout);
		
		$players = $playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE]);
		$nbPlayers = count($players);
		// @TODO understand this strange loop condition
		for ($i = $nbPlayers - 1; $i >= 0; $i--) {
			$player = $players[$i];
			if (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerInactiveTimeLimit) {

				$playerManager->kill($player->id);
			} elseif (Utils::interval(Utils::now(), $player->getDLastConnection()) >= $playerGlobalInactiveTime AND $player->statement == Player::ACTIVE) {
				$player->statement = Player::INACTIVE;

				if ($apimode === 'enabled') {
					# sending email API call
					$api->sendMail($player->bind, API::TEMPLATE_INACTIVE_PLAYER);
				}
			}
		}
		$entityManager->flush(Player::class);
	}
}