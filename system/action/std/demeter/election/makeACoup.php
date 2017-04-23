<?php
#rplayer	id du joueur
#relection id election
#program
#chiefchoice
#treasurerchoice
#warlordchoice
#ministerchoice

use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Election\Election;
use Asylamba\Modules\Demeter\Model\Election\Candidate;
use Asylamba\Modules\Demeter\Model\Forum\ForumTopic;
use Asylamba\Modules\Demeter\Model\Election\Vote;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Library\Flashbag;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$electionManager = $this->getContainer()->get('demeter.election_manager');
$candidateManager = $this->getContainer()->get('demeter.candidate_manager');
$topicManager = $this->getContainer()->get('demeter.forum_topic_manager');
$voteManager = $this->getContainer()->get('demeter.vote_manager');

$program = $request->request->get('program');
$chiefChoice = $request->request->get('chiefchoice');
$treasurerChoice = $request->request->get('treasurerchoice');
$warlordChoice = $request->request->get('warlordchoice');
$ministerChoice = $request->request->get('ministerchoice');

if ($program !== FALSE) {
	if ($session->get('playerInfo')->get('status') > Player::STANDARD && $session->get('playerInfo')->get('status') < Player::CHIEF) {
		$faction = $colorManager->get($session->get('playerInfo')->get('color'));

		if($faction->electionStatement == Color::MANDATE) {
			if ($faction->regime == Color::ROYALISTIC) {

				$election = new Election();
				$election->rColor = $faction->id;
				$election->dElection = (new \DateTime('+' . Color::PUTSCHTIME . ' second'))->format('Y-m-d H:i:s');

				$electionManager->add($election);

				$candidate = new Candidate();
				$candidate->rElection = $election->id;
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

				$faction->electionStatement = Color::ELECTION;
				$faction->dLastElection = Utils::now();

				$vote = new Vote();
				$vote->rPlayer = $session->get('playerId');
				$vote->rCandidate = $session->get('playerId');
				$vote->rElection = $election->id;
				$vote->dVotation = Utils::now();
				$voteManager->add($vote);

				$factionPlayers = $playerManager->getFactionPlayers($faction->id);

				foreach ($factionPlayers as $factionPlayer) {
					if ($factionPlayer->getStatement() !== Player::ACTIVE) {
						continue;
					}
					$notif = new Notification();
					$notif->setRPlayer($factionPlayer->id);
					$notif->setTitle('Coup d\'Etat.');
					$notif->addBeg()
						->addTxt('Un membre de votre Faction soulève une partie du peuple et tente un coup d\'état contre le gouvernement.')
						->addSep()
						->addLnk('faction/view-election', 'prendre parti sur le coup d\'état.')
						->addEnd();
					$notificationManager->add($notif);
				}
				$session->addFlashbag('Coup d\'état lancé.', Flashbag::TYPE_SUCCESS);
				$this->getContainer()->get('entity_manager')->flush();
			} else {
				throw new ErrorException('Vous vivez dans une faction démocratique.');
			}
		} else {
			throw new ErrorException('Un coup d\'état est défà en cours.');
		}
	} else {
		throw new ErrorException('Vous ne pouvez pas vous présenter, vous ne faite pas partie de l\'élite ou vous êtes déjà le hef de la faction.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}