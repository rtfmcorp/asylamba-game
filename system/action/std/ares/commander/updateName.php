<?php
include_once ARES;

# affect a commander

# int id 	 		id du commandant
# string name 		nom du commandant

if (CTR::$get->exist('id')) {
	$id = CTR::$get->get('id');
} elseif (CTR::$post->exist('id')) {
	$id = CTR::$post->get('id');
} else {
	$id = FALSE;
}

if (CTR::$get->exist('name')) {
	$name = CTR::$get->get('name');
} elseif (CTR::$post->exist('name')) {
	$name = CTR::$post->get('name');
} else {
	$name = FALSE;
}

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

?>