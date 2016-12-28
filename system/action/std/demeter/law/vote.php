<?php
#rlaw	id de la loi
#choice le vote du joueur

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Law\VoteLaw;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$lawManager = $this->getContainer()->get('demeter.law_manager');
$voteLawManager = $this->getContainer()->get('demeter.vote_law_manager');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');

$rLaw = $request->query->get('rlaw');
$choice = $request->query->get('choice');

if ($rLaw !== FALSE && $choice !== FALSE) {
	if ($session->get('playerInfo')->get('status') == Player::PARLIAMENT) {
		$_LAM = $lawManager->getCurrentSession();
		$lawManager->newSession();
		$lawManager->load(array('id' => $rLaw));

		if ($lawManager->size() > 0) {
			if ($lawManager->get()->statement == Law::VOTATION) {
				$_VLM = $voteLawManager->getCurrentSession();
				$voteLawManager->newSession();
				$voteLawManager->load(array('rPlayer' => $session->get('playerId'), 'rLaw' => $rLaw));

				if ($voteLawManager->size() == 0) {
					$vote = new VoteLaw();
					$vote->rPlayer = $session->get('playerId');
					$vote->rLaw = $rLaw;
					$vote->vote = $choice;
					$vote->dVotation = Utils::now();
					$voteLawManager->add($vote);
				} else {
					throw new ErrorException('Vous avez déjà voté.');
				}
			} else {
				throw new ErrorException('Cette loi est déjà votée.');
			}
			$voteLawManager->changeSession($_VLM);
		} else {
			throw new ErrorException('Cette loi n\'existe pas.');
		}

		$candidateManager->changeSession($_LAM);
	} else {
		throw new ErrorException('Vous n\'avez pas le droit de voter.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}