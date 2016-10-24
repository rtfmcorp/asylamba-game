<?php
// supprime toutes les routes
// 	-> avec message

# int id 		id (rPlace) de la base orbitale

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Model\OrbitalBase;

$baseId = Utils::getHTTPData('id');

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (count($verif) > 1) {
	$_COM = ASM::$com->getCurrentSession();
	ASM::$com->newSession();
	ASM::$com->load(array('rBase' => $baseId));
	$areAllFleetImmobile = TRUE;
	for ($i = 0; $i < ASM::$com->size(); $i++) {
		if (ASM::$com->get($i)->statement == Commander::MOVING) {
			$areAllFleetImmobile = FALSE;
		}
	}
	if ($areAllFleetImmobile) {
		if ($baseId != FALSE && in_array($baseId, $verif)) {
			$_OBM = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

			if (ASM::$obm->size() > 0) {
				$base = ASM::$obm->get();

				if (Utils::interval(Utils::now(), $base->dCreation, 'h') >= OrbitalBase::COOL_DOWN) {

					# delete buildings in queue
					$S_BQM1 = ASM::$bqm->getCurrentSession();
					ASM::$bqm->newSession(ASM_UMODE);
					ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));
					for ($i = ASM::$bqm->size() - 1; $i >= 0; $i--) {
						ASM::$bqm->deleteById(ASM::$bqm->get($i)->id);
					}
					ASM::$bqm->changeSession($S_BQM1);

					# change base type if it is a capital
					if ($base->typeOfBase == OrbitalBase::TYP_CAPITAL) {
						if (rand(0,1) == 0) {
							$newType = OrbitalBase::TYP_COMMERCIAL;
						} else {
							$newType = OrbitalBase::TYP_MILITARY;
						}
						# delete extra buildings
						for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
							$maxLevel = OrbitalBaseResource::getBuildingInfo($i, 'maxLevel', $newType);
							if ($base->getBuildingLevel($i) > $maxLevel) {
								$base->setBuildingLevel($i, $maxLevel);
							}
						}
						# change base type
						$base->typeOfBase = $newType;
					}

					$_PLM = ASM::$plm->getCurrentSession();
					ASM::$plm->newSession();
					ASM::$plm->load(array('id' => $baseId));

					$S_CRM1 = ASM::$crm->getCurrentSession();
					ASM::$crm->newSession();
					ASM::$crm->load(array('rOrbitalBase' => $baseId));
					ASM::$crm->load(array('rOrbitalBaseLinked' => $baseId));
					$S_CRM2 = ASM::$crm->getCurrentSession();
					ASM::$crm->changeSession($S_CRM1);

					$S_REM1 = ASM::$rem->getCurrentSession();
					ASM::$rem->newSession();
					ASM::$rem->load(array('rBase' => $baseId));
					$S_REM2 = ASM::$rem->getCurrentSession();
					ASM::$rem->changeSession($S_REM1);

					$S_COM2 = ASM::$com->getCurrentSession();
					ASM::$com->newSession(FALSE); # FALSE obligatory, else the umethod make shit
					ASM::$com->load(array('c.rBase' => $baseId));
					$S_COM3 = ASM::$com->getCurrentSession();
					ASM::$com->changeSession($S_COM2);

					ASM::$obm->changeOwnerById($baseId, $base, ID_GAIA, $S_CRM2, $S_REM2, $S_COM3);
					ASM::$plm->get()->rPlayer = ID_GAIA;

					ASM::$plm->changeSession($_PLM);
					
					for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
						if ($verif[$i] == $baseId) {
							unset($verif[$i]);
							$verif = array_merge($verif);
						}
					}
					CTR::$alert->add('Base abandonnée', ALERT_STD_SUCCESS);
					CTR::redirect(Format::actionBuilder('switchbase', ['base' => $verif[0]], FALSE));
				} else {
					CTR::$alert->add('Vous ne pouvez pas abandonner de base dans les ' . OrbitalBase::COOL_DOWN . ' premières relèves.', ALERT_STD_ERROR);	
				}
			} else {
				CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);	
			}
			ASM::$obm->changeSession($_OBM);
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('toute les flottes de cette base doivent être immobiles', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($_COM);
} else {
	CTR::$alert->add('vous ne pouvez pas abandonner votre unique planète', ALERT_STD_ERROR);
}
