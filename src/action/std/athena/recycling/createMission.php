<?php
# create recycling mission

# int rplace 		id de la base orbitale
# int rtarget 		id de la place cible
# int quantity 		recyclers quantity

use App\Classes\Library\Utils;
use App\Classes\Library\Game;
use App\Classes\Library\Flashbag;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Athena\Model\RecyclingMission;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$recyclingMissionManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\RecyclingMissionManager::class);

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = $request->query->get('rplace');
$rTarget = $request->query->get('rtarget');
$quantity = $request->request->get('quantity');

if ($rPlace !== FALSE AND $rTarget !== FALSE AND $quantity !== FALSE AND in_array($rPlace, $verif)) {
	if ($quantity > 0) {
		if (($base = $orbitalBaseManager->get($rPlace)) !== null) {
			$maxRecyclers = $orbitalBaseHelper->getInfo(OrbitalBaseResource::RECYCLING, 'level', $base->levelRecycling, 'nbRecyclers');
			$usedRecyclers = 0;

			$baseMissions = $recyclingMissionManager->getBaseActiveMissions($rPlace);

			foreach ($baseMissions as $mission) { 
				$usedRecyclers += $mission->recyclerQuantity;
				$usedRecyclers += $mission->addToNextMission;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {
				if (($startPlace = $placeManager->get($rPlace)) !== null && ($destinationPlace = $placeManager->get($rTarget)) !== null) {
					if ($destinationPlace->rPlayer == NULL AND in_array($destinationPlace->typeOfPlace, [2, 3, 4 ,5])) {
						$travelTime = Game::getTimeToTravel($startPlace, $destinationPlace);

						if ($session->get('playerInfo')->get('color') == $destinationPlace->sectorColor || $destinationPlace->sectorColor == ColorResource::NO_FACTION) {
							# create mission
							$rm = new RecyclingMission();
							$rm->rBase = $rPlace;
							$rm->rTarget = $rTarget;
							$rm->cycleTime = (2 * $travelTime) + RecyclingMission::RECYCLING_TIME;
							$rm->recyclerQuantity = $quantity;
							$rm->uRecycling = Utils::addSecondsToDate(Utils::now(), $rm->cycleTime);
							$rm->statement = RecyclingMission::ST_ACTIVE;
							$recyclingMissionManager->add($rm);

							$session->addFlashbag('Votre mission a été lancée.', Flashbag::TYPE_SUCCESS);
						} else {
							throw new ErrorException('Vous pouvez recycler uniquement dans les secteurs de votre faction ainsi que dans les secteurs neutres.');
						}
					} else {
						throw new ErrorException('On ne peut pas recycler ce lieu, petit hacker.');
					}
				} else {
					throw new ErrorException('Il y a un problème avec le lieu de départ ou d\'arrivée. Veuillez contacter un administrateur.');
				}
			} else {
				throw new ErrorException('Vous n\'avez pas assez de recycleurs libres pour lancer cette mission.');
			}
		} else {
			throw new ErrorException('cette base orbitale ne vous appartient pas');
		}
	} else {
		throw new FormException('Ca va être dur de recycler avec autant peu de recycleurs. Entrez un nombre plus grand que zéro.');
	}
} else {
	throw new FormException('pas assez d\'informations pour créer une mission de recyclage');
}
