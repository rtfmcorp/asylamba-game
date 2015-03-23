<?php
#avance l'attaque de tous les officiers

include_once ARES;

ASM::$com->load(array());

for ($i = 0; $i < ASM::$com->size(); $i++) {
	$date = new DateTime(ASM::$com->get($i)->dArrival);
	$date->modify('-100000 second');
	ASM::$com->dArrival = $date;
}