<?php
# delete a commercial route action

# int base 			id (rPlace) de la base orbitale qui veut supprimer la route
# int route 		id de la route commerciale

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\Notification;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$base = Utils::getHTTPData('base');
$route = Utils::getHTTPData('route');

if ($base !== FALSE AND $route !== FALSE AND in_array($base, $verif)) {
	$S_CRM1 = ASM::$crm->getCurrentSession();
	ASM::$crm->newSession(ASM_UMODE);
	ASM::$crm->load(array('id' => $route, 'statement' => [CommercialRoute::ACTIVE, CommercialRoute::STANDBY]));
	if (ASM::$crm->get() && ASM::$crm->size() == 1) {
		$cr = ASM::$crm->get();
		if ($cr->playerId1 == CTR::$data->get('playerId') || $cr->playerId2 == CTR::$data->get('playerId')) {
			if ($cr->getROrbitalBase() == $base OR $cr->getROrbitalBaseLinked() == $base) {
				$S_OBM1 = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession(ASM_UMODE);
				ASM::$obm->load(array('rPlace' => $cr->getROrbitalBase()));
				$proposerBase = ASM::$obm->get();
				ASM::$obm->load(array('rPlace' => $cr->getROrbitalBaseLinked()));
				$linkedBase = ASM::$obm->get(1);
				if ($cr->getROrbitalBase() == $base) {
					$notifReceiver = $linkedBase->getRPlayer();
					$myBaseName = $proposerBase->getName();
					$otherBaseName = $linkedBase->getName();
					$myBaseId = $proposerBase->getRPlace();
					$otherBaseId = $linkedBase->getRPlace();
				} else { //if ($cr->getROrbitalBaseLinked == $base) {
					$notifReceiver = $proposerBase->getRPlayer();
					$myBaseName = $linkedBase->getName();
					$otherBaseName = $proposerBase->getName();
					$myBaseId = $linkedBase->getRPlace();
					$otherBaseId = $proposerBase->getRPlace();
				}

				# perte du prestige pour les joueurs Négoriens
				$S_PAM1 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				ASM::$pam->load(array('id' => array($cr->playerId1, $cr->playerId2)));
				$exp = round($cr->getIncome() * CRM_COEFEXPERIENCE);
				
				ASM::$pam->changeSession($S_PAM1);
				//notification
				$n = new Notification();
				$n->setRPlayer($notifReceiver);
				$n->setTitle('Route commerciale détruite');
				$n->addBeg()->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'))->addTxt(' annule les accords commerciaux entre ');
				$n->addLnk('map/place-' . $myBaseId, $myBaseName)->addTxt(' et ');
				$n->addLnk('map/base-' . $otherBaseId, $otherBaseName)->addTxt('.');
				$n->addSep()->addTxt('La route commerciale qui liait les deux bases orbitales est détruite, elle ne vous rapporte donc plus rien !');
				$n->addEnd();
				ASM::$ntm->add($n);

				//destruction de la route
				ASM::$crm->deleteById($route);

				CTR::$alert->add('Route commerciale détruite', ALERT_STD_SUCCESS);
				ASM::$obm->changeSession($S_OBM1);
			} else {
				CTR::$alert->add('impossible de supprimer une route commerciale', ALERT_STD_ERROR);
			}
		} else {
				CTR::$alert->add('cette route ne vous appartient pas', ALERT_STD_ERROR);
			}
	} else {
		CTR::$alert->add('impossible de supprimer une route commerciale', ALERT_STD_ERROR);
	}
	ASM::$crm->changeSession($S_CRM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour supprimer une route commerciale', ALERT_STD_FILLFORM);
}