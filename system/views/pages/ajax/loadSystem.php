<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;

if (CTR::$get->exist('systemid')) {
	$systemId = CTR::$get->get('systemid');
} else if (CTR::$post->exist('systemid')) {
	$systemId = CTR::$post->get('systemid');
} else {
	$systemId = FALSE;
}

$S_SYS1 = ASM::$sys->getCurrentSession();
ASM::$sys->newSession();
ASM::$sys->load(array('id' => $systemId));

if (ASM::$sys->size() == 1) {
	# objet système
	$system = ASM::$sys->get();

	# objet place
	$places = array();
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('rSystem' => $systemId), array('position'));
	for ($i = 0; $i < ASM::$plm->size(); $i++) {
		$places[] = ASM::$plm->get($i);
	}
	ASM::$plm->changeSession($S_PLM1);

	# inclusion du "composant"
	include PAGES . 'desktop/mapElement/actionbox.php';
} else {
	return FALSE;
}

ASM::$sys->changeSession($S_SYS1);
