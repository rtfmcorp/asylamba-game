<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Model\PlayerBonus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewSpatioport extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBase $currentBase,
		CommercialRouteManager $commercialRouteManager,
		ColorManager $colorManager,
	): Response {
		$session = $request->getSession();
		$mode = $request->query->get('mode', 'list');

		$inGameFactions = $colorManager->getInGameFactions();

		return $this->render('pages/athena/spatioport.html.twig', [
			'routes' => array_merge(
				$commercialRouteManager->getByBase($currentBase->getId()),
				$commercialRouteManager->getByDistantBase($currentBase->getId())
			),
			'routes_data' => $commercialRouteManager->getBaseCommercialData($currentBase),
			'player_commercial_income_bonus' => $session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME),
			'negora_commercial_bonus' => ColorResource::BONUS_NEGORA_ROUTE,
			'is_player_from_negora' => $currentPlayer->getRColor() === ColorResource::NEGORA,
			'in_game_factions' => $inGameFactions,
			'mode' => $mode,
			'search_results' => ($mode === 'search' && $request->getMethod() === 'POST')
				? $commercialRouteManager->searchCandidates(
					$currentPlayer->getId(),
					$currentBase,
					array_reduce($inGameFactions, function (array $carry, Color $faction) use ($request) {
						if ($request->request->has('faction-' . $faction->getId())) {
							$carry[] = $faction->getId();
						}
						return $carry;
					}, []),
					abs(intval($request->request->get('min-dist', 75))),
					abs(intval($request->request->get('max-dist', 125))),
				) : null,
		]);
	}
}
