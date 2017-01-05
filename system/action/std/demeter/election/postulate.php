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

$rElection			 = $request->request->get('relection');
$program			 = $request->request->get('program');
$chiefChoice		 = $request->request->get('chiefchoice');
$treasurerChoice	 = $request->request->get('treasurerchoice');
$warlordChoice		 = $request->request->get('warlordchoice');
$ministerChoice		 = $request->request->get('ministerchoice');

if ($rElection !== FALSE && $program !== FALSE) {
	$_ELM = $electionManager->getCurrentSession();
	$electionManager->newSession();
	$electionManager->load(array('id' => $rElection));

	if ($electionManager->size() > 0) {
		if ($electionManager->get()->rColor == $session->get('playerInfo')->get('color')) {
			$chiefChoice = 1;
			$treasurerChoice = 1;
			$warlordChoice = 1;
			$ministerChoice = 1;

			if ($session->get('playerInfo')->get('status') > Player::STANDARD) {
				$_CLM = $colorManager->getCurrentSession();
				$colorManager->newSession();
				$colorManager->load(array('id' => $session->get('playerInfo')->get('color')));

				$_CAM = $candidateManager->getCurrentSession();
				$candidateManager->newSession();
				$candidateManager->load(array('rPlayer' => $session->get('playerId'), 'rElection' => $rElection));

				if ($colorManager->get()->electionStatement == Color::CAMPAIGN) {
					if ($chiefChoice !== NULL && $treasurerChoice !== FALSE && $warlordChoice !== FALSE && $ministerChoice !== FALSE) {
						if ($candidateManager->size() == 0) {
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
								$vote->rElection = $electionManager->get()->id;
								$vote->dVotation = Utils::now();

								$voteManager->add($vote);
							}

							$response->redirect('faction/view-election/candidate-' . $candidate->id);
							$session->addFlashbag('Candidature déposée.', Flashbag::TYPE_SUCCESS);
						} else {
							$candidateManager->deleteById($candidateManager->get()->getId());
							$session->addFlashbag('Candidature retirée.', Flashbag::TYPE_SUCCESS);
						}
					} else {
						throw new ErrorException('Informations manquantes sur les choix.');	
					}
				} else {
					throw new ErrorException('Vous ne pouvez présenter ou retirer votre candidature qu\'en période de campagne.');
				}

				$candidateManager->changeSession($_CAM);
				$colorManager->changeSession($_CLM);
			} else {
				throw new ErrorException('Vous ne pouvez pas vous présenter, vous ne faite pas partie de l\'élite.');
			}
		} else {
			throw new ErrorException('Cette election ne se déroule pas dans la faction du joueur.');
		}
	} else {
		throw new ErrorException('Cette election n\'existe pas.');
	}
	$electionManager->changeSession($_ELM);
} else {
	throw new ErrorException('Informations manquantes.');
}