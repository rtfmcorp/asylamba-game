<?php

use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Flashbag;

$factionNewsManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager::class);
$request = $this->getContainer()->get('app.request');

$id = $request->query->get('id');

if ($id !== FALSE) {	
	if (($factionNew = $factionNewsManager->get($id)) !== null) {
		$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->remove($factionNew);
		$this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->addFlashbag('L\'annonce a bien été supprimée.', Flashbag::TYPE_SUCCESS);
	} else {
		throw new FormException('Cette annonce n\'existe pas.');
	}
} else {
	throw new FormException('Manque d\'information.');
}
