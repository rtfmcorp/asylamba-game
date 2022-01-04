<?php

#rlaw	id de la loi

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Resource\LawResources;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$lawManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Law\LawManager::class);
$candidateManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\CandidateManager::class);

$rLaw = $request->query->get('rlaw');

if ($rLaw !== FALSE) {
	if ($session->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
		if (($law = $lawManager->get($rLaw)) === null) {
			throw new ErrorException('Cette loi n\'existe pas.');
		}
	} else {
		throw new ErrorException('Vous n\'avez pas le droit d\'annuler cette loi.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}
