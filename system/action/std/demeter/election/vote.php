<?php
#rplayer	id du joueur
#rcandidate id du candidat
#relection id election

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Election\Vote;
use Asylamba\Modules\Demeter\Model\Color;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$electionManager = $this->getContainer()->get('demeter.election_manager');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$voteManager = $this->getContainer()->get('demeter.vote_manager');

$rElection = $request->query->get('relection');
$rCandidate = $request->query->get('rcandidate');

if ($rElection !== FALSE && $rCandidate !== FALSE) {
	$leader = $playerManager->getFactionLeader($session->get('playerInfo')->get('color'));
	
	if ($rCandidate == 0) {
		$rCandidate = $leader->id;
	}

	if (($election = $electionManager->get($rElection)) !== null) {
		if (($candidateManager->getByElectionAndPlayer($playerManager->get($rCandidate), $election)) !== null || $leader->id == $rCandidate) {
			if ($election->rColor == $session->get('playerInfo')->get('color')) {
				$_VOM = $voteManager->getCurrentSession();
				$voteManager->newSession();
				$voteManager->load(array('rPlayer' => $session->get('playerId'), 'rElection' => $rElection));

				if ($voteManager->get() == 0) {
					$_CLM = $colorManager->getCurrentSession();
					$colorManager->newSession();
					$colorManager->load(array('id' => $election->rColor));

					if($colorManager->get()->electionStatement == Color::ELECTION) {
						$vote = new Vote();
						$vote->rPlayer = $session->get('playerId');
						$vote->rCandidate = $rCandidate;
						$vote->rElection = $rElection;
						$vote->dVotation = Utils::now();
						$voteManager->add($vote);
						$session->addFlashbag('Vous avez voté.', Flashbag::TYPE_SUCCESS);
					} else {
						throw new ErrorException('Vous ne pouvez voter pour un candidat qu\'en période d\'élection.');
					}

					$colorManager->changeSession($_CLM);
				} else {
					throw new ErrorException('Vous avez déjà voté.');
				}
			$voteManager->changeSession($_VOM);
			} else {
				throw new ErrorException('Cette election ne se déroule pas dans votre faction.');
			}
		} else {
			throw new ErrorException('Ce candidat n\'existe pas.');
		}
	} else {
		throw new ErrorException('Cette election n\'existe pas.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}