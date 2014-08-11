<?php
include_once ATHENA;
# give resources action

# int baseid 		id (rPlace) de la base orbitale
# int otherbaseid 	id (rPlace) de la base orbitale à qui on veut envoyer des ressources
# int quantity 		quantité de ressources à envoyer

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$otherBaseIdype = Utils::getHTTPData('totherbaseidype');
$quantity = Utils::getHTTPData('quantity');

if ($baseId !== FALSE AND $otherBaseId !== FALSE AND $quantity !== FALSE AND in_array($baseId, $verif)) {
	$resource = intval($quantity);

	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession(ASM_UMODE);
	ASM::$obm->load(array('rPlace' => $baseId));
	$orbitalBase = ASM::$obm->get();

	if ($resource > 0) {
		$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $orbitalBase->getLevelRefinery(), 'storageSpace');
		$currentStorage = $orbitalBase->getResourcesStorage();
		$maxResourcesToSend = $currentStorage - ($storageSpace * OBM_STOCKLIMIT);
		/* supprimer la limitation d'envoi de ressources */
		if ($maxResourcesToSend > 0) {
			if ($resource > $maxResourcesToSend) {
				CTR::$alert->add('Vous ne pouvez pas envoyer autant de ressources, l\'envoi a été limité a ' . Format::numberFormat($maxResourcesToSend) . ' ressources.', ALERT_STD_INFO);
				$resource = $maxResourcesToSend;
			}
			ASM::$obm->load(array('rPlace' => $otherBaseId));
			if (ASM::$obm->size() == 2) {
				$otherBase = ASM::$obm->get(1);

				$orbitalBase->decreaseResources($resource);
				$otherBase->increaseResources($resource);

				if ($orbitalBase->getRPlayer() != $otherBase->getRPlayer()) {
					$n = new Notification();
					$n->setRPlayer($otherBase->getRPlayer());
					$n->setTitle('Envoi de ressources');
					$n->addBeg()->addTxt($otherBase->getName())->addSep();
					$n->addLnk('diary/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
					$n->addTxt(' vous a envoyé ')->addStg(Format::numberFormat($resource))->addTxt(' ressources depuis sa base ');
					$n->addLnk('map/place' . $orbitalBase->getRPlace(), $orbitalBase->getName())->addTxt('.');
					$n->addSep()->addLnk('bases/base-' . $otherBase->getId()  . '/view-refinery', 'vers la raffinerie →');
					$n->addEnd();
					ASM::$ntm->add($n);
				}
				CTR::$alert->add('Ressources transférées', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('envoi de ressources impossible - erreur dans les bases orbitales', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('envoi de ressources impossible - pas assez de ressources', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('envoi de ressources impossible', ALERT_STD_ERROR);
	}
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des ressources', ALERT_STD_FILLFORM);
}