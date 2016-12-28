<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Exception\ErrorException;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');

$id = $request->query->get('id');

if ($id !== FALSE) {
	$S_FNM_1 = $factionNewsManager->getCurrentSession();
	$factionNewsManager->newSession();
	$factionNewsManager->load(array('id' => $id));

	if ($factionNewsManager->size() == 1) {
		# chargement de toutes les factions
		$S_FNM_2 = $factionNewsManager->getCurrentSession();
		$factionNewsManager->newSession();
		$factionNewsManager->load(['rFaction' => $session->get('playerInfo')->get('color')]);

		for ($i = 0; $i < $factionNewsManager->size(); $i++) { 
			if ($factionNewsManager->get($i)->id == $id) {
				$factionNewsManager->get($i)->pinned = 1;
			} else {
				$factionNewsManager->get($i)->pinned = 0;
			}
		}

		$factionNewsManager->changeSession($S_FNM_2);
	} else {
		throw new FormException('Cette annonce n\'existe pas.');	
	}

	$factionNewsManager->changeSession($S_FNM_1);
} else {
	throw new FormException('Manque d\'information.');
}