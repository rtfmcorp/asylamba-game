<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Classes\Container\Params;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ViewHeadquarters extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
	): Response {
		// @TODO demistify that part
		if ($request->query->has('commander') && null !== ($commander = $commanderManager->get($request->query->get('commander')))) {
			if ($commander->rPlayer === $currentPlayer->getId() && in_array($commander->getStatement(), [Commander::AFFECTED, Commander::MOVING])) {
				$commanderBase = $orbitalBaseManager->get($commander->getRBase());
			} else {
				$commander = null;
			}
		}

		[$obsets, $commandersIds] = $this->getObsetsAndCommandersIds($request, $commanderManager);

		return $this->render('pages/ares/fleet/headquarters.html.twig', [
			'commander' => $commander ?? null,
			'commander_base' => $commanderBase ?? null,
			'default_parameters' => Params::$params,
			'obsets' => $obsets,
			'commandersIds' => $commandersIds,
		]);
	}

	private function getObsetsAndCommandersIds(Request $request, CommanderManager $commanderManager): array
	{
		$session = $request->getSession();
		$obsets = array(); $j = 0;
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			if ($request->cookies->get('p' . Params::LIST_ALL_FLEET, Params::$params[Params::LIST_ALL_FLEET]) || $session->get('playerBase')->get('ob')->get($i)->get('id') == $session->get('playerParams')->get('base')) {
				$obsets[$j] = array();

				$obsets[$j]['info'] = array();
				$obsets[$j]['fleets'] = array();

				$obsets[$j]['info']['id'] = $session->get('playerBase')->get('ob')->get($i)->get('id');
				$obsets[$j]['info']['name'] = $session->get('playerBase')->get('ob')->get($i)->get('name');
				$obsets[$j]['info']['type'] = $session->get('playerBase')->get('ob')->get($i)->get('type');
				$obsets[$j]['info']['img'] = $session->get('playerBase')->get('ob')->get($i)->get('img');

				$j++;
			}
		}

		# commander manager : incoming attack
		$commandersId = array(0);
		for ($i = 0; $i < $session->get('playerEvent')->size(); $i++) {
			if ($session->get('playerEvent')->get($i)->get('eventType') == $this->getParameter('event_incoming_attack')) {
				if ($session->get('playerEvent')->get($i)->get('eventInfo')->size() > 0) {
					$commandersId[] = $session->get('playerEvent')->get($i)->get('eventId');
				}
			}
		}

		$attackingCommanders =  $commanderManager->getVisibleIncomingAttacks($session->get('playerId'));
		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($attackingCommanders as $commander) {
				if ($commander->rDestinationPlace == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}
		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::AFFECTED, Commander::MOVING], ['c.rBase' => 'DESC']);

		for ($i = 0; $i < count($obsets); $i++) {
			foreach ($commanders as $commander) {
				if ($commander->rBase == $obsets[$i]['info']['id']) {
					$obsets[$i]['fleets'][] = $commander;
				}
			}
		}

		return [$obsets, $commandersId];
	}
}
