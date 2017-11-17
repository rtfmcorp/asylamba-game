<?php

#rlaw	id de la loi

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Resource\LawResources;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$lawManager = $this->getContainer()->get('demeter.law_manager');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');

$rLaw = $request->query->get('rlaw');

if ($rLaw !== false) {
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
