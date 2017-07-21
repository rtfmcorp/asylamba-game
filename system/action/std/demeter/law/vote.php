<?php
#rlaw	id de la loi
#choice le vote du joueur

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Law\VoteLaw;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$lawManager = $this->getContainer()->get('demeter.law_manager');
$voteLawManager = $this->getContainer()->get('demeter.vote_law_manager');

$rLaw = $request->query->get('rlaw');
$choice = $request->query->get('choice');

if ($rLaw !== FALSE && $choice !== FALSE) {
	if ($session->get('playerInfo')->get('status') == Player::PARLIAMENT) {

		if (($law = $lawManager->get($rLaw)) !== null) {
			if ($law->statement == Law::VOTATION) {
				if ($voteLawManager->hasVoted($session->get('playerId'), $law)) {
					throw new ErrorException('Vous avez déjà voté.');
				}
				$vote = new VoteLaw();
				$vote->rPlayer = $session->get('playerId');
				$vote->rLaw = $rLaw;
				$vote->vote = $choice;
				$vote->dVotation = Utils::now();
				$voteLawManager->add($vote);
			} else {
				throw new ErrorException('Cette loi est déjà votée.');
			}
		} else {
			throw new ErrorException('Cette loi n\'existe pas.');
		}
	} else {
		throw new ErrorException('Vous n\'avez pas le droit de voter.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}