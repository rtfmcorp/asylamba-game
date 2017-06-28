<?php
# cancel recycling mission

# int id 			id de la mission
# int place 		id de la base orbitale

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Athena\Model\RecyclingMission;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$missionId = $request->query->get('id');
$rPlace = (int) $request->query->get('place');

if ($missionId !== FALSE AND !empty($rPlace) AND in_array($rPlace, $verif)) {
	if (($base = $orbitalBaseManager->get($rPlace)) !== null) {
		$recyclingMissionManager->load(array('id' => $missionId, 'rBase' => $rPlace, 'statement' => RecyclingMission::ST_ACTIVE));

		if (($mission = $recyclingMissionManager->get($missionId)) !== null && $mission->statement = RecyclingMission::ST_ACTIVE && $mission->rBase === $rPlace) {
			$mission->statement = RecyclingMission::ST_BEING_DELETED;
			$session->addFlashbag('Ordre de mission annulé.', Flashbag::TYPE_SUCCESS);
			$this->getContainer()->get('entity_manager')->flush($mission);
		} else {
			throw new ErrorException('impossible de supprimer la mission.');
		}
	} else {
		throw new ErrorException('cette base orbitale ne vous appartient pas');
	}
} else {
	throw new FormException('pas assez d\'informations pour supprimer une mission de recyclage');
}