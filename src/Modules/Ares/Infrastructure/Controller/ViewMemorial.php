<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ViewMemorial extends AbstractController
{
	public function __invoke(
		Player $currentPlayer,
		CommanderManager $commanderManager,
	): Response {
		return $this->render('pages/ares/fleet/memorial.html.twig', [
			'commanders' => $commanderManager->getPlayerCommanders($currentPlayer->getId(), [Commander::DEAD], ['c.palmares' => 'DESC']),
		]);
	}
}
