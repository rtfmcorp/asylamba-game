<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

$id 		= $request->query->get('id');
$content	= $request->request->get('content');
$title		= $request->request->get('title');

if ($title !== FALSE AND $content !== FALSE && $id !== FALSE) {	
	if (($factionNew = $factionNewsManager->get($id)) !== null) {
		if ($session->get('playerInfo')->get('status') >= 3) {
			$factionNew->title = $title;
			$factionNewsManager->edit($factionNew, $content);
		} else {
			throw new ErrorException('Vous n\'avez pas le droit pour créer une annonce.');
		}
	} else {
		throw new FormException('Cette annonce n\'existe pas.');
	}
} else {
	throw new FormException('Manque d\'information.');
}