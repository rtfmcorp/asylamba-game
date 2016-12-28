<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');

$id 		= $request->query->get('id');
$content	= $request->request->get('content');
$title		= $request->request->get('title');

if ($title !== FALSE AND $content !== FALSE && $id !== FALSE) {	
	$S_FNM_1 = $factionNewsManager->getCurrentSession();
	$factionNewsManager->newSession();
	$factionNewsManager->load(array('id' => $id));

	if ($factionNewsManager->size() == 1) {
		if ($session->get('playerInfo')->get('status') >= 3) {
			$factionNewsManager->get()->title = $title;
			$factionNewsManager->edit($factionNewsManager->get(), $content);
		} else {
			throw new ErrorException('Vous n\'avez pas le droit pour créer une annonce.');
		}
	} else {
		throw new FormException('Cette annonce n\'existe pas.');
	}

	$factionNewsManager->changeSession($S_FNM_1);
} else {
	throw new FormException('Manque d\'information.');
}