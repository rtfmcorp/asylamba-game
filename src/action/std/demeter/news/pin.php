<?php

use App\Classes\Exception\FormException;
use App\Modules\Demeter\Model\Forum\FactionNews;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

if (($id = $request->query->get('id')) === FALSE) {
	throw new FormException('Manque d\'information.');
}

$factionNews = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager::class)->getFactionNews($session->get('playerInfo')->get('color'));
$newExists = false;
// This way of doing things remove all previous pins
foreach ($factionNews as $factionNew) { 
	if ($factionNew->id == $id) {
		$newExists = true;
		$factionNew->pinned = 1;
	} else {
		$factionNew->pinned = 0;
	}
}
if ($newExists !== true) {
	throw new FormException('Cette annonce n\'existe pas.');	
}
$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush(FactionNews::class);
