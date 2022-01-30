<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base;

use App\Classes\Container\ArrayList;
use App\Classes\Library\Game;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Resource\PlaceResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ViewOverview extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBase $orbitalBase,
		CommanderManager $commanderManager,
		CommercialRouteManager $commercialRouteManager,
	): Response {
		// @TODO: move it to the using part of the code and remove useless data
		if ($orbitalBase->getLevelSpatioport() > 0) {
			$orbitalBase->commercialRoutesData = $commercialRouteManager->getBaseCommercialData($orbitalBase);
		}
		return $this->render('pages/athena/overview.html.twig', [
			'commanders' => $commanderManager->getBaseCommanders($orbitalBase->getId(), [Commander::AFFECTED, Commander::MOVING]),
			'vanguard_positions' => PlaceResource::get($orbitalBase->typeOfBase, 'l-line-position'),
			'vanguard_positions_count' => PlaceResource::get($orbitalBase->typeOfBase, 'l-line'),
			'rear_positions' => PlaceResource::get($orbitalBase->typeOfBase, 'r-line-position'),
			'rear_positions_count' => PlaceResource::get($orbitalBase->typeOfBase, 'r-line'),
			'science_coeff' => Game::getImprovementFromScientificCoef($orbitalBase->getPlanetHistory()),
			'minimal_change_level' => $this->getParameter('athena.obm.change_type_min_level'),
			'capital_change_level' => $this->getParameter('athena.obm.capital_min_level'),
			'capitals_count' => $this->getCapitalsCount($request->getSession()),
		]);
	}

	private function getCapitalsCount(SessionInterface $session): int
	{
		return \count(\array_filter(
			$session->get('playerBase')->get('ob')->all(),
			fn (ArrayList $baseData) => $baseData->get('type') === OrbitalBase::TYP_CAPITAL,
		));
	}
}
