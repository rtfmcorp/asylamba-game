<?php
#rplayer	id du joueur
#relection id election
#program
#chiefchoice
#treasurerchoice
#warlordchoice
#ministerchoice

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Modules\Demeter\Model\Election\Candidate;
use App\Modules\Demeter\Model\Forum\ForumTopic;
use App\Modules\Demeter\Model\Election\Vote;
use App\Modules\Demeter\Model\Color;
use App\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$electionManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\ElectionManager::class);
$candidateManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\CandidateManager::class);
$topicManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\ForumTopicManager::class);
$voteManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Election\VoteManager::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);

$rElection			 = $request->query->get('relection');
$program			 = $request->request->get('program');
$chiefChoice		 = $request->request->get('chiefchoice');
$treasurerChoice	 = $request->request->get('treasurerchoice');
$warlordChoice		 = $request->request->get('warlordchoice');
$ministerChoice		 = $request->request->get('ministerchoice');

if ($rElection !== FALSE && $program !== FALSE) {
	if (($election = $electionManager->get($rElection)) !== null) {
		if ($election->rColor == $session->get('playerInfo')->get('color')) {
			$chiefChoice = 1;
			$treasurerChoice = 1;
			$warlordChoice = 1;
			$ministerChoice = 1;

			if ($session->get('playerInfo')->get('status') > Player::STANDARD) {
				$faction = $colorManager->get($session->get('playerInfo')->get('color'));

				if ($faction->electionStatement == Color::CAMPAIGN) {
					if ($chiefChoice !== NULL && $treasurerChoice !== FALSE && $warlordChoice !== FALSE && $ministerChoice !== FALSE) {
						if (($candidate = $candidateManager->getByElectionAndPlayer($election, $playerManager->get($session->get('playerId')))) === null) {
							$candidate = new Candidate();

							$candidate->rElection = $rElection;
							$candidate->rPlayer = $session->get('playerId');
							$candidate->chiefChoice = $chiefChoice;
							$candidate->treasurerChoice = $treasurerChoice;
							$candidate->warlordChoice = $warlordChoice;
							$candidate->ministerChoice = $ministerChoice;
							$candidate->dPresentation = Utils::now();
							$candidate->program = $program;

							$candidateManager->add($candidate);

							$topic = new ForumTopic();
							$topic->title = 'Candidat ' . $session->get('playerInfo')->get('name');
							$topic->rForum = 30;
							$topic->rPlayer = $candidate->rPlayer;
							$topic->rColor = $session->get('playerInfo')->get('color');
							$topic->dCreation = Utils::now();
							$topic->dLastMessage = Utils::now();

							$topicManager->add($topic);

							if ($session->get('playerInfo')->get('color') == 4) {
								$vote = new Vote();

								$vote->rPlayer = $session->get('playerId');
								$vote->rCandidate = $session->get('playerId');
								$vote->rElection = $election->id;
								$vote->dVotation = Utils::now();

								$voteManager->add($vote);
							}

							$response->redirect('faction/view-election/candidate-' . $candidate->id);
							$session->addFlashbag('Candidature déposée.', Flashbag::TYPE_SUCCESS);
						} else {
							$entityManager->remove($candidate);
							$session->addFlashbag('Candidature retirée.', Flashbag::TYPE_SUCCESS);
						}
					} else {
						throw new ErrorException('Informations manquantes sur les choix.');	
					}
				} else {
					throw new ErrorException('Vous ne pouvez présenter ou retirer votre candidature qu\'en période de campagne.');
				}
			} else {
				throw new ErrorException('Vous ne pouvez pas vous présenter, vous ne faite pas partie de l\'élite.');
			}
		} else {
			throw new ErrorException('Cette election ne se déroule pas dans la faction du joueur.');
		}
	} else {
		throw new ErrorException('Cette election n\'existe pas.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}
