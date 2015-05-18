<?php
#avance l'attaque de tous les officiers

include_once ARES;

ASM::$com->newSession();
ASM::$com->load(['c.statement' => Commander::MOVING]);

for ($i = 0; $i < ASM::$com->size(); $i++) {
	ASM::$com->get($i)->dArrival = ASM::$com->get($i)->dStart;
}