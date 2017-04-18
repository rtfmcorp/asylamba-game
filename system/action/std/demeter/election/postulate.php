<?php
#rplayer	id du joueur
#relection id election
#program
#chiefchoice
#treasurerchoice
#warlordchoice
#ministerchoice

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Model\Election\Candidate;
use Asylamba\Modules\Demeter\Model\Forum\ForumTopic;
use Asylamba\Modules\Demeter\Model\Election\Vote;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$electionManager = $this->getContainer()->get('demeter.election_manager');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');
$voteManager = $this->getContainer()->get('demeter.vote_manager');
$entityManager = $this->getContainer()->get('entity_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');

$rElection			 = $request->request->get('relection');
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
						if (($candidate = $candidateManager->getByElectionAndPlayer($playerManager->get($session->get('playerId')), $election)) === null) {
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