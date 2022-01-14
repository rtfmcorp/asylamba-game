<?php

namespace App\Shared\Infrastructure\Controller;

use App\Classes\Library\Security;
use App\Classes\Library\Utils;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HomepageController extends AbstractController
{
	public function __invoke(PlayerManager $playerManager, Security $security): Response
	{
		$players = $playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);

		return $this->render('pages/homepage.html.twig', [
			'active_players' => $players,
			'bind_key' => $security->crypt($security->buildBindkey(Utils::generateString(10))),
			'player_bind_keys' => array_reduce($players, function (array $acc, Player $player) use ($security) {
				$acc[$player->getId()] = $security->crypt($security->buildBindkey($player->bind));

				return $acc;
			}, []),
			'high_mode' => $this->getParameter('highmode'),
		]);
	}
}
