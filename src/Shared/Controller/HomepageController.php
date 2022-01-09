<?php

namespace App\Shared\Controller;

use App\Classes\Library\Security;
use App\Classes\Library\Utils;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
	public function __construct(
		private PlayerManager $playerManager,
		private Security $security,
		private bool $highMode
	) {

	}

	public function __invoke(): Response
	{
		$players = $this->playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);

		return $this->render('pages/homepage.html.twig', [
			'active_players' => $players,
			'bind_key' => $this->security->crypt($this->security->buildBindkey(Utils::generateString(10))),
			'player_bind_keys' => array_reduce($players, function (array $acc, Player $player) {
				$acc[$player->getId()] = $this->security->crypt($this->security->buildBindkey($player->bind));

				return $acc;
			}, []),
			'high_mode' => $this->highMode,
		]);
	}
}
