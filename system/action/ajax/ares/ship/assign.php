<?php
# assign ship action

# string direction 	'ctb' = commander to base / 'btc' = base to commander
					# [commander] envoie [quantity] [ship] depuis son [squadron] a une [base]
					# [base] envoie [quantity] [ship] a un [commander] sur son [squadron]
# int base 			base id
# int ship  		ship id
# int quantity		ship quantity
# int commander		commander id
# int squadron 		squadron id

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}


$direction 		= Utils::getHTTPData('direction');
$baseId 		= Utils::getHTTPData('base');
$shipId 		= Utils::getHTTPData('ship');
$quantity 		= Utils::getHTTPData('quantity');
$commanderId 	= Utils::getHTTPData('commander');
$squadron 		= Utils::getHTTPData('squadron');


if ($direction !== FALSE AND $baseId !== FALSE AND $shipId !== FALSE AND $commanderId !== FALSE AND $squadron !== FALSE AND in_array($baseId, $verif)) {
	if ($direction == 'ctb' OR $direction == 'btc') {
		if (ShipResource::isAShip($shipId)) {
			if ($quantity === FALSE) {
				$quantity = 1;
			}
			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession();
			ASM::$obm->load(array('rPlace' => $baseId));

			$S_COM1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.id' => $commanderId, 'c.rBase' => $baseId));
			
			if (ASM::$obm->size() == 1 AND ASM::$com->size() == 1) {
				$base = ASM::$obm->get();
				$commander = ASM::$com->get();
				if ($commander->statement == Commander::AFFECTED) {

					if ($direction == 'ctb') {			// commander to base
						// if the commander has the quantity of ships required
						if ($commander->getSquadron($squadron)->getNbrShipByType($shipId) - $quantity >= 0) {
							$base->setShipStorage($shipId, ($base->getShipStorage($shipId) + $quantity));
							$commander->getSquadron($squadron)->updateShip($shipId, -$quantity);
							# CTR::$alert->add('Vaisseau(x) envoyé(s) à la base.', ALERT_BUG_SUCCESS);
						} else {
							CTR::$alert->add('L\'escadrille n\'a pas autant de vaisseaux !', ALERT_STD_ERROR);
						}
					} else {							// base to commander
						// if the base has the quantity of ships required
						if ($base->getShipStorage($shipId) - $quantity >= 0) {
							// if it's enough PEV space in the commander
							if (($commander->getSquadron($squadron)->getPev() + (ShipResource::getInfo($shipId, 'pev') * $quantity)) <= 100) {
								$base->setShipStorage($shipId, ($base->getShipStorage($shipId) - $quantity));
								$commander->getSquadron($squadron)->updateShip($shipId, $quantity);
								# CTR::$alert->add('Vaisseau(x) envoyé(s) dans l\'escadrille.', ALERT_BUG_SUCCESS);

								# tutorial
								if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
									switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
										case TutorialResource::FILL_SQUADRON:
											TutorialHelper::setStepDone();
											break;
									}
								}
							} else {
								CTR::$alert->add('Il n\'y a pas assez de place dans l\'escadrille pour ces vaisseaux.', ALERT_STD_ERROR);
							}
						} else {
							CTR::$alert->add('La base n\'a pas autant de vaisseaux !', ALERT_STD_ERROR);
						}
					}
				} else {
					CTR::$alert->add('Cet officier ne peut être modifier.', ALERT_STD_ERROR);					
				}
			} else {
				CTR::$alert->add('Erreur dans les commandants ou la base.', ALERT_STD_ERROR);
			}
			ASM::$com->changeSession($S_COM1);
			ASM::$obm->changeSession($S_OBM1);
		} else {
			CTR::$alert->add('Le vaisseau n\'existe pas.', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('L\'argument direction n\'est pas correct.', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('Pas assez d\'informations pour assigner un vaisseau', ALERT_STD_FILLFORM);
}