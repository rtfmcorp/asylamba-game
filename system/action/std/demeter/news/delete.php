<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Flashbag;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');

$id = $request->query->get('id');

if ($id !== FALSE) {	
	$S_FNM_1 = $factionNewsManager->getCurrentSession();
	$factionNewsManager->newSession();
	$factionNewsManager->load(array('id' => $id));

	if ($factionNewsManager->size() == 1) {
		$factionNewsManager->deleteById($id);

		$this->getContainer()->get('app.session')->addFlashbag('L\'annonce a bien été supprimée.', Flashbag::TYPE_SUCCESS);
	} else {
		throw new FormException('Cette annonce n\'existe pas.');
	}

	$factionNewsManager->changeSession($S_FNM_1);
} else {
	throw new FormException('Manque d\'information.');
}