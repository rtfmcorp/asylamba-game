<?php
# create recycling mission

# int rplace 		id de la base orbitale
# int rtarget 		id de la place cible
# int quantity 		recyclers quantity

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\RecyclingMission;

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = $request->query->get('rplace');
$rTarget = $request->query->get('rtarget');
$quantity = $request->request->get('quantity');

if ($rPlace !== FALSE AND $rTarget !== FALSE AND $quantity !== FALSE AND in_array($rPlace, $verif)) {

	if ($quantity > 0) {
	
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession();
		$orbitalBaseManager->load(array('rPlace' => $rPlace));

		if ($orbitalBaseManager->size() == 1) {
			$base = $orbitalBaseManager->get();

			$maxRecyclers = $orbitalBaseHelper->getInfo(OrbitalBaseResource::RECYCLING, 'level', $base->levelRecycling, 'nbRecyclers');
			$usedRecyclers = 0;

			$S_REM1 = $recyclingMissionManager->getCurrentSession();
			$recyclingMissionManager->newSession();
			$recyclingMissionManager->load(array('rBase' => $rPlace, 'statement' => RecyclingMission::ST_ACTIVE));

			for ($i = 0; $i < $recyclingMissionManager->size(); $i++) { 
				$usedRecyclers += $recyclingMissionManager->get($i)->recyclerQuantity;
				$usedRecyclers += $recyclingMissionManager->get($i)->addToNextMission;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {
				$S_PLM1 = $placeManager->getCurrentSession();
				$placeManager->newSession();
				$placeManager->load(array('id' => [$rPlace, $rTarget]));

				if ($placeManager->size() == 2) {
					$startPlace 		= $placeManager->getById($rPlace);
					$destinationPlace 	= $placeManager->getById($rTarget);

					if ($destinationPlace->rPlayer == NULL AND in_array($destinationPlace->typeOfPlace, [2, 3, 4 ,5])) {
						$travelTime = Game::getTimeToTravel($startPlace, $destinationPlace);

						if ($session->get('playerInfo')->get('color') == $destinationPlace->sectorColor || $destinationPlace->sectorColor == ColorResource::NO_FACTION) {
							# create mission
							$rm = new RecyclingMission();
							$rm->rBase = $rPlace;
							$rm->rTarget = $rTarget;
							$rm->cycleTime = (2 * $travelTime) + RecyclingMission::RECYCLING_TIME;
							$rm->recyclerQuantity = $quantity;
							$rm->uRecycling = Utils::now();
							$rm->statement = RecyclingMission::ST_ACTIVE;
							$recyclingMissionManager->add($rm);

							$response->flashbag->add('Votre mission a été lancée.', Response::FLASHBAG_SUCCESS);
						} else {
							throw new ErrorException('Vous pouvez recycler uniquement dans les secteurs de votre faction ainsi que dans les secteurs neutres.');
						}
					} else {
						throw new ErrorException('On ne peut pas recycler ce lieu, petit hacker.');
					}
				} else {
					throw new ErrorException('Il y a un problème avec le lieu de départ ou d\'arrivée. Veuillez contacter un administrateur.');
				}
				$placeManager->changeSession($S_PLM1);
			} else {
				throw new ErrorException('Vous n\'avez pas assez de recycleurs libres pour lancer cette mission.');
			}
			$recyclingMissionManager->changeSession($S_REM1);
		} else {
			throw new ErrorException('cette base orbitale ne vous appartient pas');
		}
		$orbitalBaseManager->changeSession($S_OBM1);
	} else {
		throw new FormException('Ca va être dur de recycler avec autant peu de recycleurs. Entrez un nombre plus grand que zéro.');
	}
} else {
	throw new FormException('pas assez d\'informations pour créer une mission de recyclage');
}