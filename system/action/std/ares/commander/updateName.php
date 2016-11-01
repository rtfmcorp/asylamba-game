<?php

# affect a commander

# int id 	 		id du commandant
# string name 		nom du commandant

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Parser;

$id = Utils::getHTTPData('id');
$name = Utils::getHTTPData('name');


if ($id !== FALSE) {
	if ($name !== FALSE) {
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.id' => $id, 'c.rPlayer' => CTR::$data->get('playerId')));
		if (ASM::$com->size() == 1) {
			$commander = ASM::$com->get();
			$p = new Parser();
			$name = $p->protect($name);
			if (strlen($name) > 1 AND strlen($name) < 26) {
				$commander->setName($name);
				CTR::$alert->add('le nom de votre commandant est maintenant ' . $name, ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('le nom doit comporter entre 2 et 25 caractères', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Ce commandant n\'existe pas ou ne vous appartient pas', ALERT_STD_ERROR);
		}

		ASM::$com->changeSession($S_COM1);
	} else {
		CTR::$alert->add('manque d\'information', ALERT_BUG_ERROR);
	}
} else {
	CTR::$alert->add('manque d\'information', ALERT_BUG_ERROR);
}