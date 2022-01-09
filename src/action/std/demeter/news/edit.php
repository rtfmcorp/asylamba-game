<?php

use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$factionNewsManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager::class);
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

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
