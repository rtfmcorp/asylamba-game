<?php
# create recycling mission

# int rplace 		id de la base orbitale
# int rtarget 		id de la place cible
# int quantity 		recyclers quantity

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\RecyclingMission;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = Utils::getHTTPData('rplace');
$rTarget = Utils::getHTTPData('rtarget');
$quantity = Utils::getHTTPData('quantity');

if ($rPlace !== FALSE AND $rTarget !== FALSE AND $quantity !== FALSE AND in_array($rPlace, $verif)) {

	if ($quantity > 0) {
	
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $rPlace));

		if (ASM::$obm->size() == 1) {
			$base = ASM::$obm->get();

			$maxRecyclers = OrbitalBaseResource::getInfo(OrbitalBaseResource::RECYCLING, 'level', $base->levelRecycling, 'nbRecyclers');
			$usedRecyclers = 0;

			$S_REM1 = ASM::$rem->getCurrentSession();
			ASM::$rem->newSession();
			ASM::$rem->load(array('rBase' => $rPlace, 'statement' => RecyclingMission::ST_ACTIVE));

			for ($i = 0; $i < ASM::$rem->size(); $i++) { 
				$usedRecyclers += ASM::$rem->get($i)->recyclerQuantity;
				$usedRecyclers += ASM::$rem->get($i)->addToNextMission;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {
				$S_PLM1 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(array('id' => [$rPlace, $rTarget]));

				if (ASM::$plm->size() == 2) {
					$startPlace 		= ASM::$plm->getById($rPlace);
					$destinationPlace 	= ASM::$plm->getById($rTarget);

					if ($destinationPlace->rPlayer == NULL AND in_array($destinationPlace->typeOfPlace, [2, 3, 4 ,5])) {
						$travelTime = Game::getTimeToTravel($startPlace, $destinationPlace);

						if (CTR::$data->get('playerInfo')->get('color') == $destinationPlace->sectorColor || $destinationPlace->sectorColor == ColorResource::NO_FACTION) {
							# create mission
							$rm = new RecyclingMission();
							$rm->rBase = $rPlace;
							$rm->rTarget = $rTarget;
							$rm->cycleTime = (2 * $travelTime) + RecyclingMission::RECYCLING_TIME;
							$rm->recyclerQuantity = $quantity;
							$rm->uRecycling = Utils::now();
							$rm->statement = RecyclingMission::ST_ACTIVE;
							ASM::$rem->add($rm);

							CTR::$alert->add('Votre mission a été lancée.', ALERT_STD_SUCCESS);
						} else {
							CTR::$alert->add('Vous pouvez recycler uniquement dans les secteurs de votre faction ainsi que dans les secteurs neutres.', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('On ne peut pas recycler ce lieu, petit hacker.', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Il y a un problème avec le lieu de départ ou d\'arrivée. Veuillez contacter un administrateur.', ALERT_STD_ERROR);
				}
				ASM::$plm->changeSession($S_PLM1);
			} else {
				CTR::$alert->add('Vous n\'avez pas assez de recycleurs libres pour lancer cette mission.', ALERT_STD_ERROR);
			}
			ASM::$rem->changeSession($S_REM1);
		} else {
			CTR::$alert->add('cette base orbitale ne vous appartient pas', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('Ca va être dur de recycler avec autant peu de recycleurs. Entrez un nombre plus grand que zéro.', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour créer une mission de recyclage', ALERT_STD_FILLFORM);
}