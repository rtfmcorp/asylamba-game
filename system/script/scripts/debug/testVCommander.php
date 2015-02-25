<?php

include_once GAIA;

ASM::$plm->load(array('maxDanger' => 10));

for ($i = 0; $i < ASM::$plm->size(); $i++) {
	// echo 'id : ' . ASM::$plm->get($i)->id;
	echo '<br />';
	echo 'pop : ' . ASM::$plm->get($i)->population;
	echo '<br />';
	echo 'danger réel : ' . ASM::$plm->get($i)->danger;
	echo '<br />';
	echo 'danger max : ' . ASM::$plm->get($i)->maxDanger;
	echo '<br />';
	$c = ASM::$plm->get($i)->createVirtualCommander();
	echo 'niveau : ' . $c->level;
	echo '<br />';	
	echo 'squadrons : ' . $c->id;
	echo '<br />';
	echo 'pev : ' . $c->getPev();
	echo '<br />';
	echo '<br />';

}