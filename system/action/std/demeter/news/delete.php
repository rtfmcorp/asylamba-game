<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Flashbag;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');

$id = $request->query->get('id');

if ($id !== FALSE) {	
	if (($factionNew = $factionNewsManager->get($id)) !== null) {
		$this->getContainer()->get('entity_manager')->remove($factionNew);
		$this->getContainer()->get('session_wrapper')->addFlashbag('L\'annonce a bien été supprimée.', Flashbag::TYPE_SUCCESS);
	} else {
		throw new FormException('Cette annonce n\'existe pas.');
	}
} else {
	throw new FormException('Manque d\'information.');
}