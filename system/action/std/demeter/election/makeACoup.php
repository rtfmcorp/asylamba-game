<?php
#rplayer	id du joueur
#relection id election
#program
#chiefchoice
#treasurerchoice
#warlordchoice
#ministerchoice

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Demeter\Model\Election\Election;
use Asylamba\Modules\Demeter\Model\Election\Candidate;
use Asylamba\Modules\Demeter\Model\Forum\ForumTopic;
use Asylamba\Modules\Demeter\Model\Election\Vote;

$program = Utils::getHTTPData('program');
$chiefChoice = Utils::getHTTPData('chiefchoice');
$treasurerChoice = Utils::getHTTPData('treasurerchoice');
$warlordChoice = Utils::getHTTPData('warlordchoice');
$ministerChoice = Utils::getHTTPData('ministerchoice');

if ($program !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') > Player::STANDARD && CTR::$data->get('playerInfo')->get('status') < Player::CHIEF) {
		$_CLM = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));

		if(ASM::$clm->get()->electionStatement == Color::MANDATE) {
			if (ASM::$clm->get()->regime == COLOR::ROYALISTIC) {

				$election = new Election();
				$election->rColor = ASM::$clm->get()->id;

				$date = new \DateTime(Utils::now());
				$date->modify('+' . COLOR::PUTSCHTIME . ' second');
				$election->dElection = $date->format('Y-m-d H:i:s');

				ASM::$elm->add($election);

				$candidate = new Candidate();
				$candidate->rElection = $election->id;
				$candidate->rPlayer = CTR::$data->get('playerId');
				$candidate->chiefChoice = $chiefChoice;
				$candidate->treasurerChoice = $treasurerChoice;
				$candidate->warlordChoice = $warlordChoice;
				$candidate->ministerChoice = $ministerChoice;
				$candidate->dPresentation = Utils::now();
				$candidate->program = $program; 
				ASM::$cam->add($candidate);

				$topic = new ForumTopic();
				$topic->title = 'Candidat ' . CTR::$data->get('playerInfo')->get('name');
				$topic->rForum = 30;
				$topic->rPlayer = $candidate->rPlayer;
				$topic->rColor = CTR::$data->get('playerInfo')->get('color');
				$topic->dCreation = Utils::now();
				$topic->dLastMessage = Utils::now();
				ASM::$tom->add($topic);

				ASM::$clm->get()->electionStatement = COLOR::ELECTION;

				ASM::$clm->get()->dLastElection = Utils::now();

				$vote = new Vote();
				$vote->rPlayer = CTR::$data->get('playerId');
				$vote->rCandidate = CTR::$data->get('playerId');
				$vote->rElection = $election->id;
				$vote->dVotation = Utils::now();
				ASM::$vom->add($vote);

				$_PAM123 = ASM::$pam->getCurrentsession();
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(['rColor' => ASM::$clm->get()->id, 'statement' => Player::ACTIVE]);

				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					$notif = new Notification();
					$notif->setRPlayer(ASM::$pam->get($i)->id);
					$notif->setTitle('Coup d\'Etat.');
					$notif->addBeg()
						->addTxt('Un membre de votre Faction soulève une partie du peuple et tente un coup d\'état contre le gouvernement.')
						->addSep()
						->addLnk('faction/view-election', 'prendre parti sur le coup d\'état.')
						->addEnd();
					ASM::$ntm->add($notif);
				}
				ASM::$pam->changeSession($_PAM123);

				CTR::$alert->add('Coup d\'état lancé.', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('Vous vivez dans une faction démocratique.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Un coup d\'état est défà en cours.', ALERT_STD_ERROR);
		}

		ASM::$clm->changeSession($_CLM);
	} else {
		CTR::$alert->add('Vous ne pouvez pas vous présenter, vous ne faite pas partie de l\'élite ou vous êtes déjà le hef de la faction.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}