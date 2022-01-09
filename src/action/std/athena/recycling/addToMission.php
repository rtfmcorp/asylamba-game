<?php
# create recycling mission

# int id 			id de la mission
# int place 		id de la base orbitale
# int quantity 		recyclers quantity
use App\Classes\Library\Flashbag;
use App\Modules\Athena\Model\RecyclingMission;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseHelper = $this->getContainer()->get(\App\Modules\Athena\Helper\OrbitalBaseHelper::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);
$recyclingMissionManager = $this->getContainer()->get(\App\Modules\Athena\Manager\RecyclingMissionManager::class);

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}
$missionId = (int) $request->query->get('id');
$rPlace = $request->query->get('place');
$quantity = $request->request->get('quantity');

if ($rPlace !== FALSE AND !empty($missionId) AND $quantity !== FALSE AND in_array($rPlace, $verif)) {

	if ($quantity > 0) {
		if (($base = $orbitalBaseManager->get($rPlace)) !== null) {
			$maxRecyclers = $orbitalBaseHelper->getInfo(OrbitalBaseResource::RECYCLING, 'level', $base->levelRecycling, 'nbRecyclers');
			$usedRecyclers = 0;

			$baseMissions = $recyclingMissionManager->getBaseActiveMissions($rPlace);

			foreach ($baseMissions as $mission) { 
				$usedRecyclers += $mission->recyclerQuantity + $mission->addToNextMission;
			}

			if ($maxRecyclers - $usedRecyclers >= $quantity) {

				$mission = NULL;
				foreach ($baseMissions as $baseMission) {
					if ($baseMission->id === $missionId && $baseMission->statement === RecyclingMission::ST_ACTIVE) {
						$mission = $baseMission;
						break;
					}
				}
				if ($mission !== NULL) {
					$mission->addToNextMission += $quantity;
					$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush($mission);
					$session->addFlashbag('Vos recycleurs ont bien été affectés, ils seront ajoutés à la prochaine mission.', Flashbag::TYPE_SUCCESS);
				} else {
					throw new ErrorException('Il y a un problème, la mission est introuvable. Veuillez contacter un administrateur.');
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
