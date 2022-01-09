<?php

#rlaw	id de la loi

use App\Classes\Exception\ErrorException;
use App\Modules\Demeter\Resource\LawResources;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$lawManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Law\LawManager::class);
$candidateManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Election\CandidateManager::class);

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
