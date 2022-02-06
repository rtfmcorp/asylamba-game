<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewOverview extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
	): Response {
		return $this->render('pages/ares/fleet/overview.html.twig', [
			'obsets' => $this->getObsets($request, $currentPlayer, $commanderManager, $orbitalBaseManager),
		]);
	}

	private function getObsets(
		Request $request,
		Player $currentPlayer,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
	): array {
		$session = $request->getSession();
		# set d'orbitale base
		$obsets = [];
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$obsets[$i] = array();

			$obsets[$i]['info'] = [];
			$obsets[$i]['fleets'] = [];
			$obsets[$i]['dock'] = [];

			$obsets[$i]['info']['id'] = $session->get('playerBase')->get('ob')->get($i)->get('id');
			$obsets[$i]['info']['name'] = $session->get('playerBase')->get('ob')->get($i)->get('name');
			$obsets[$i]['info']['type'] = $session->get('playerBase')->get('ob')->get($i)->get('type');
		}

		# commander manager : yours
		$commanders = $commanderManager->getPlayerCommanders($currentPlayer->getId(), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'DESC']);

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($commanders as $commander) {
				if ($commander->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}
		# ship in dock
		$playerBases = $orbitalBaseManager->getPlayerBases($currentPlayer->getId());

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($playerBases as $orbitalBase) {
				if ($orbitalBase->rPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['dock'] = $orbitalBase->shipStorage;
				}
			}
		}

		return $obsets;
	}
}
