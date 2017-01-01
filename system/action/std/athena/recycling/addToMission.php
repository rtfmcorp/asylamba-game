<?php
# create recycling mission

# int id 			id de la mission
# int place 		id de la base orbitale
# int quantity 		recyclers quantity
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}
$missionId = $request->query->get('id');
$rPlace = $request->query->get('place');
$quantity = $request->request->get('quantity');

if ($rPlace !== FALSE AND $missionId !== FALSE AND $quantity !== FALSE AND in_array($rPlace, $verif)) {

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
			$recyclingMissionManager->load(array('rBase' => $rPlace, 'statement' => array(RecyclingMission::ST_ACTIVE, RecyclingMission::ST_BEING_DELETED)));

			for ($i = 0; $i < $recyclingMissionManager->size(); $i++) { 
				$usedRecyclers += $recyclingMissionManager->get($i)->recyclerQuantity + $recyclingMissionManager->get($i)->addToNextMission;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {

				$mission = NULL;
				for ($i = 0; $i < $recyclingMissionManager->size(); $i++) {
					if ($recyclingMissionManager->get($i)->id == $missionId && $recyclingMissionManager->get($i)->statement == RecyclingMission::ST_ACTIVE) {
						$mission = $recyclingMissionManager->get($i);
						break;
					}
				}
				if ($mission !== NULL) {
					$mission->addToNextMission += $quantity;
					$session->addFlashbag('Vos recycleurs ont bien été affectés, ils seront ajoutés à la prochaine mission.', Flashbag::TYPE_SUCCESS);
				} else {
					throw new ErrorException('Il y a un problème, la mission est introuvable. Veuillez contacter un administrateur.');
				}
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