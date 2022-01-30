<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\SchoolClassResource;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Zeus\Helper\CheckName;
use App\Modules\Zeus\Model\PlayerBonus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewSchool extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBase $currentBase,
		CommanderManager $commanderManager,

	): Response {
		$session = $request->getSession();

		$commanderInvestBonus = $session->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST);

		$invest = $currentBase->iSchool * $commanderInvestBonus / 100;

		return $this->render('pages/athena/school.html.twig', [
			'commanders' => $commanderManager->getBaseCommanders($currentBase->getId(), [Commander::INSCHOOL], ['c.experience' => 'DESC']),
			'reserve_commanders' => $commanderManager->getBaseCommanders($currentBase->getId(), ['c.statement' => Commander::RESERVE], ['c.experience' => 'DESC']),
			'earned_experience' => $this->calculateEarnedExperience($invest),
			'max_commanders_in_school' => PlaceResource::get($currentBase->typeOfBase, 'school-size'),
			'random_name' => CheckName::randomize(),
			'commander_price' => SchoolClassResource::getInfo(0, 'credit'),
			'commander_invest_bonus' => $commanderInvestBonus,
		]);
	}

	// @TODO Move that logic elsewhere
	private function calculateEarnedExperience(int $invest): int
	{
		return max(round($invest / Commander::COEFFSCHOOL), 0);
	}
}
