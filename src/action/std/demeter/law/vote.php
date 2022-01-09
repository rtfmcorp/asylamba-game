<?php
#rlaw	id de la loi
#choice le vote du joueur

use App\Classes\Library\Utils;
use App\Modules\Demeter\Model\Law\VoteLaw;
use App\Modules\Demeter\Model\Law\Law;
use App\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$lawManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Law\LawManager::class);
$voteLawManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Law\VoteLawManager::class);

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
