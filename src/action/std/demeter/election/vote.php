<?php
#rplayer	id du joueur
#rcandidate id du candidat
#relection id election

use App\Classes\Exception\ErrorException;
use App\Classes\Library\Flashbag;
use App\Classes\Library\Utils;
use App\Modules\Demeter\Model\Election\Vote;
use App\Modules\Demeter\Model\Color;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$electionManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\ElectionManager::class);
$candidateManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\CandidateManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$voteManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\VoteManager::class);

$rElection = $request->query->get('relection');
$rCandidate = $request->query->get('rcandidate');

if ($rElection !== FALSE && $rCandidate !== FALSE) {
	$leader = $playerManager->getFactionLeader($session->get('playerInfo')->get('color'));
	
	if ($rCandidate == 0) {
		$rCandidate = $leader->id;
	}

	if (($election = $electionManager->get($rElection)) !== null) {
		if (($candidateManager->getByElectionAndPlayer($election, $playerManager->get($rCandidate))) !== null || $leader->id == $rCandidate) {
			if ($election->rColor == $session->get('playerInfo')->get('color')) {
				if (($voteManager->getPlayerVote($playerManager->get($session->get('playerId')), $election)) === null) {
					$faction = $colorManager->get($session->get('playerInfo')->get('color'));

					if($faction->electionStatement == Color::ELECTION) {
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
				} else {
					throw new ErrorException('Vous avez déjà voté.');
				}
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
