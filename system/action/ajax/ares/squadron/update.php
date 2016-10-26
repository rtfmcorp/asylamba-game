<?php
# udpate ships in squadron

# int base 			ref base id
# int commander		ref commander id
# int squadron 		ref squadron id

# string newSquadron	liste de vaisseaux séparé par un _

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Ares\Model\Commander;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseID 		= Utils::getHTTPData('base');
$commanderID 	= Utils::getHTTPData('commander');
$squadronID 	= Utils::getHTTPData('squadron');
$newSquadron 	= Utils::getHTTPData('army');

if ($baseID !== FALSE AND $commanderID !== FALSE AND $squadronID !== FALSE AND $newSquadron !== FALSE AND in_array($baseID, $verif)) {
	$newSquadron = explode('_', $newSquadron);
	$newSquadron = array_map(function($el) {
		return $el < 0 ? 0 : (int)$el;
	}, $newSquadron);

	if (count($newSquadron) == 12) {
		# chargement de la base orbitale
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $baseID));

		# chargement du commandant
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.id' => $commanderID, 'c.rBase' => $baseID, 'c.statement' => [Commander::AFFECTED]));

		if (ASM::$obm->size() == 1 AND ASM::$com->size() == 1) {
			$base = ASM::$obm->get();
			$commander = ASM::$com->get();
			$squadron = $commander->getSquadron($squadronID);

			if ($squadron !== FALSE) {
				$squadronSHIP = $squadron->arrayOfShips;
				$baseSHIP = $base->shipStorage;

				foreach ($newSquadron as $i => $v) {
					$baseSHIP[$i] -= ($v - $squadronSHIP[$i]);
					$squadronSHIP[$i] = $v;
				}

				# token de vérification
				$baseOK = TRUE;
				$squadronOK = TRUE;
				$totalPEV = 0;

				# vérif shipStorage (pas de nombre négatif)
				foreach ($baseSHIP as $i => $v) {
					if ($v < 0) {
						$baseOK = FALSE;
						break;
					}
				}

				# vérif de squadron (pas plus de 100 PEV, pas de nombre négatif)
				foreach ($squadronSHIP as $i => $v) {
					$totalPEV += $v * ShipResource::getInfo($i, 'pev');
					if ($v < 0) {
						$squadronOK = FALSE;
						break;
					}
				}

				if ($baseOK AND $squadronOK AND $totalPEV <= 100) {
					# tutorial
					if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
						switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
							case TutorialResource::FILL_SQUADRON:
								TutorialHelper::setStepDone();
								break;
						}
					}

					$base->shipStorage = $baseSHIP;
					$commander->getSquadron($squadronID)->arrayOfShips = $squadronSHIP;
				} else {
					CTR::$alert->add('Erreur dans la répartition des vaisseaux.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Erreur dans les références du commandant ou de la base.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Erreur dans les références du commandant ou de la base.', ALERT_STD_ERROR);
		}

		ASM::$com->changeSession($S_COM1);
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('Pas assez d\'informations pour assigner un vaisseau.', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour assigner un vaisseau.', ALERT_STD_FILLFORM);
}