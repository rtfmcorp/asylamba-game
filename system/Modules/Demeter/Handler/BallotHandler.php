<?php

namespace Asylamba\Modules\Demeter\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Election\CandidateManager;
use Asylamba\Modules\Demeter\Manager\Election\ElectionManager;
use Asylamba\Modules\Demeter\Manager\Election\VoteManager;
use Asylamba\Modules\Demeter\Message\BallotMessage;
use Asylamba\Modules\Demeter\Message\CampaignMessage;
use Asylamba\Modules\Demeter\Message\SenateUpdateMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Hermes\Manager\ConversationManager;
use Asylamba\Modules\Hermes\Manager\ConversationMessageManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\Player;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BallotHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected ElectionManager $electionManager,
		protected PlayerManager $playerManager,
		protected VoteManager $voteManager,
		protected ConversationManager $conversationManager,
		protected ConversationMessageManager $conversationMessageManager,
		protected CandidateManager $candidateManager,
		protected MessageBusInterface $messageBus,
		protected NotificationManager $notificationManager,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(BallotMessage $message): void
	{
		$faction = $this->colorManager->get($message->getFactionId());
		$election = $this->electionManager->getFactionLastElection($faction->id);
		$chiefId = (($leader = $this->playerManager->getFactionLeader($faction->id)) !== null) ? $leader->getId() : false;

		$votes = $this->voteManager->getElectionVotes($election);

		$ballot = [];
		$listCandidate = [];

		foreach ($votes as $vote) {
			if (array_key_exists($vote->rCandidate, $ballot)) {
				$ballot[$vote->rCandidate]++;
			} else {
				$ballot[$vote->rCandidate] = 1;
			}
		}

		if (!empty($ballot)) {
			// @TODO optimize SQL queries
			foreach ($ballot as $player => $vote) {
				$listCandidate[] = [
					'id' => $player,
					'name' => $this->playerManager->get($player)->name,
					'vote' => $vote
				];
			}

			uasort($listCandidate, function($a, $b) {
				if ($a['vote'] == $b['vote']) {
					return 0;
				}
				return $a['vote'] > $b['vote']
					? -1 : 1;
			});
		}
		reset($listCandidate);

		$convPlayerID = $this->playerManager->getFactionAccount($faction->id)->id;

		$S_CVM = $this->conversationManager->getCurrentSession();
		$this->conversationManager->newSession();
		$this->conversationManager->load(
			['cu.rPlayer' => $convPlayerID]
		);
		$conv = $this->conversationManager->get();

		$this->conversationManager->changeSession($S_CVM);

		if ($faction->regime == Color::DEMOCRATIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				$governmentMembers = $this->playerManager->getGovernmentMembers($faction->getId());
				$newChief = $this->playerManager->get(key($ballot));

				$this->mandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
			} else {
				$this->mandate($faction, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
			}
		} elseif ($faction->regime == Color::ROYALISTIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				if (key($ballot) == $chiefId) {
					next($ballot);
				}

				if (((current($ballot) / ($faction->activePlayers + 1)) * 100) >= Color::PUTSCHPERCENTAGE) {
					$governmentMembers = $this->playerManager->getGovernmentMembers($faction->getId());
					$newChief = $this->playerManager->get(key($ballot));
					$this->mandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
				} else {
					$looser = $this->playerManager->get(key($ballot));
					$this->mandate($faction, 0, $looser, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
				}
			}
		} else {
			if (($leader = $this->playerManager->getFactionLeader($faction->id)) !== null) {
				if (($candidate = $this->candidateManager->getByElectionAndPlayer($election, $leader)) !== null) {
					if (rand(0, 1) == 0) {
						$ballot = array();
					}
				}
			}
			if (count($ballot) > 0) {
				reset($ballot);
				$aleaNbr = rand(0, count($ballot) - 1);

				for ($i = 0; $i < $aleaNbr; $i++) {
					next($ballot);
				}

				$governmentMembers = $this->playerManager->getGovernmentMembers($faction->getId());
				$newChief = $this->playerManager->get(key($ballot));

				$this->mandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
			} else {
				$this->mandate($faction, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
			}
		}
	}

	public function mandate(Color $color, $governmentMembers, $newChief, $idOldChief, $hadVoted, $conv, $convPlayerID, $candidate) {
		# préparation de la conversation
		$conv->messages++;
		$conv->dLastMessage = Utils::now();

		# désarchiver tous les users
		$users = $conv->players;
		foreach ($users as $user) {
			$user->convStatement = ConversationUser::CS_DISPLAY;
		}
		if ($hadVoted) {
			/*			$date = new DateTime($this->dLastElection);
						$date->modify('+' . $this->mandateDuration + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
						$date = $date->format('Y-m-d H:i:s');
						$this->dLastElection = $date;*/

			foreach ($governmentMembers as $governmentMember) {
				$governmentMember->status = Player::PARLIAMENT;
			}

			$newChief->status = Player::CHIEF;

			$color->dLastElection = Utils::now();
			$color->electionStatement = Color::MANDATE;

			$statusArray = $color->status;
			if ($color->regime === Color::DEMOCRATIC) {
				$date = new \DateTime($color->dLastElection);
				$date->modify('+' . $color->mandateDuration . ' second');

				$this->messageBus->dispatch(
					new CampaignMessage($color->getId()),
					[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
				);
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($newChief->id);
				$notif->setTitle('Votre avez été élu');
				$notif->addBeg()
					->addTxt(' Le peuple vous a soutenu, vous avez été élu ' . $statusArray[Player::CHIEF - 1] . ' de votre faction.');
				$this->notificationManager->add($notif);

				# création du message
				$message = new ConversationMessage();
				$message->rConversation = $conv->id;
				$message->rPlayer = $convPlayerID;
				$message->type = ConversationMessage::TY_STD;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;
				$message->content = 'La période électorale est terminée. Un nouveau dirigeant a été élu pour faire valoir la force de ' . $color->popularName . ' à travers la galaxie. Longue vie à <strong>' . (current($candidate)['name']) . '</strong>.<br /><br />Voici les résultats des élections :<br /><br />';
				foreach ($candidate as $player) {
					$message->content .= $player['name'] . ' a reçu ' . $player['vote'] . ' vote' . Format::plural($player['vote']) . '<br />';
				}
				$this->conversationMessageManager->add($message);
			} elseif ($color->regime === Color::ROYALISTIC) {
				$this->messageBus->dispatch(
					new SenateUpdateMessage($color->getId()),
					[DateTimeConverter::to_delay_stamp(date('Y-m-d H:i:s', (time() + $color->mandateDuration)))],
				);
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($newChief->id);
				$notif->setTitle('Votre coup d\'état a réussi');
				$notif->addBeg()
					->addTxt(' Le peuple vous a soutenu, vous avez renversé le ' . $statusArray[Player::CHIEF - 1] . ' de votre faction et avez pris sa place.');
				$this->notificationManager->add($notif);

				if ($idOldChief) {
					$notif = new Notification();
					$notif->dSending = Utils::now();
					$notif->setRPlayer($idOldChief);
					$notif->setTitle('Un coup d\'état a réussi');
					$notif->addBeg()
						->addTxt(' Le joueur ')
						->addLnk('embassy/player-' . $newChief->id, $newChief->name)
						->addTxt(' a fait un coup d\'état, vous êtes évincé du pouvoir.');
					$this->notificationManager->add($notif);
				}

				# création du message
				reset($candidate);
				if (current($candidate)['id'] == $idOldChief) {
					next($candidate);
				}
				$message = new ConversationMessage();
				$message->rConversation = $conv->id;
				$message->rPlayer = $convPlayerID;
				$message->type = ConversationMessage::TY_STD;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;
				$message->content = 'Un putsch a réussi, un nouveau dirigeant va faire valoir la force de ' . $color->popularName . ' à travers la galaxie. Longue vie à <strong>' . (current($candidate)['name']) . '</strong>.<br /><br />De nombreux membres de la faction ont soutenu le mouvement révolutionnaire :<br /><br />';
				$message->content .= current($candidate)['name'] . ' a reçu le soutien de ' . Format::number((current($candidate)['vote'] / ($color->activePlayers + 1)) * 100) . '% de la population.' . '<br />';
				$this->conversationMessageManager->add($message);

			} else {
				$date = new \DateTime($color->dLastElection);
				$date->modify('+' . $color->mandateDuration . ' second');
				$this->messageBus->dispatch(
					new CampaignMessage($color->getId()),
					[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
				);

				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($newChief->id);
				$notif->setTitle('Vous avez été nommé Guide');
				$notif->addBeg()
					->addTxt(' Les Oracles ont parlé, vous êtes désigné par la Grande Lumière pour guider Cardan vers la Gloire.');
				$this->notificationManager->add($notif);

				$message = new ConversationMessage();
				$message->rConversation = $conv->id;
				$message->rPlayer = $convPlayerID;
				$message->type = ConversationMessage::TY_STD;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;
				$message->content = 'Les Oracles ont parlé, un nouveau dirigeant va faire valoir la force de ' . $color->popularName . ' à travers la galaxie. Longue vie à <strong>' . (current($candidate)['name']) . '</strong>.<br /><br /><br /><br />';
				$this->conversationMessageManager->add($message);
			}
		} else {
			$noChief = false;
			if (($oldChief = $this->playerManager->get($idOldChief)) === null) {
				$noChief = true;
				$oldChief = $this->playerManager->getByName($color->officialName);
			}
			/*			$date = new DateTime($this->dLastElection);
						$date->modify('+' . $this->mandateDuration + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
						$date = $date->format('Y-m-d H:i:s');
						$this->dLastElection = $date;*/
			$color->dLastElection = Utils::now();
			$color->electionStatement = Color::MANDATE;

			switch ($color->regime) {
				case Color::DEMOCRATIC:
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$this->messageBus->dispatch(
						new CampaignMessage($color->getId()),
						[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
					);

					if ($idOldChief) {
						$notif = new Notification();
						$notif->dSending = Utils::now();
						$notif->setRPlayer($idOldChief);
						$notif->setTitle('Vous demeurez ' . ColorResource::getInfo($color->getId(), 'status')[Player::CHIEF - 1]);
						$notif->addBeg()
							->addTxt(' Aucun candidat ne s\'est présenté oour vous remplacer lors des dernières élections. Par conséquent, vous êtes toujours à la tête de ' . $color->popularName);
						$this->notificationManager->add($notif);
					}
					# création du message
					$message = new ConversationMessage();
					$message->rConversation = $conv->id;
					$message->rPlayer = $convPlayerID;
					$message->type = ConversationMessage::TY_STD;
					$message->dCreation = Utils::now();
					$message->dLastModification = NULL;
					$message->content = ' La période électorale est terminée. Aucun candidat ne s\'est présenté pour prendre la tête de ' . $color->popularName . '.';
					$message->content .=
						($noChief === false)
							? '<br>Par conséquent, ' . $oldChief->getName() . ' est toujours au pouvoir.'
							: '<br>Par conséquent, le siège du pouvoir demeure vacant.'
					;
					$this->conversationMessageManager->add($message);
					break;
				case Color::ROYALISTIC:
					$notif = new Notification();
					$notif->dSending = Utils::now();
					$notif->setRPlayer($newChief->id);
					$notif->setTitle('Votre coup d\'état a échoué');
					$notif->addBeg()
						->addTxt(' Le peuple ne vous a pas soutenu, l\'ancien gouvernement reste en place.');
					$this->notificationManager->add($notif);

					if ($idOldChief) {
						$notif = new Notification();
						$notif->dSending = Utils::now();
						$notif->setRPlayer($idOldChief);
						$notif->setTitle('Un coup d\'état a échoué');
						$notif->addBeg()
							->addTxt(' Le joueur ')
							->addLnk('embassy/player-' . $newChief->id, $newChief->name)
							->addTxt(' a tenté un coup d\'état, celui-ci a échoué.');
						$this->notificationManager->add($notif);
					}
					$message = new ConversationMessage();
					$message->rConversation = $conv->id;
					$message->rPlayer = $convPlayerID;
					$message->type = ConversationMessage::TY_STD;
					$message->dCreation = Utils::now();
					$message->dLastModification = NULL;
					$message->content = 'Un coup d\'état a échoué. ' . $oldChief->getName(). ' demeure le dirigeant de ' . $color->popularName . '.';
					$this->conversationMessageManager->add($message);
					break;
				case Color::THEOCRATIC:
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$this->messageBus->dispatch(
						new CampaignMessage($color->getId()),
						[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
					);

					if ($idOldChief) {
						$notif = new Notification();
						$notif->dSending = Utils::now();
						$notif->setRPlayer($idOldChief);
						$notif->setTitle('Vous avez été nommé Guide');
						$notif->addBeg()
							->addTxt(' Les Oracles ont parlé, vous êtes toujours désigné par la Grande Lumière pour guider Cardan vers la Gloire.');
						$this->notificationManager->add($notif);
					}
					$message = new ConversationMessage();
					$message->rConversation = $conv->id;
					$message->rPlayer = $convPlayerID;
					$message->type = ConversationMessage::TY_STD;
					$message->dCreation = Utils::now();
					$message->dLastModification = NULL;
					$message->content = 'Nul ne s\'est soumis au regard des dieux pour conduire ' . $color->popularName . ' vers sa gloire.';
					$message->content .=
						($noChief === false)
							? $oldChief->getName(). ' demeure l\'élu des dieux pour accomplir leurs desseins dans la galaxie.'
							: 'Par conséquent, le siège du pouvoir demeure vacant.'
					;
					$this->conversationMessageManager->add($message);
					break;
			}
		}
		$this->entityManager->flush();
	}
}
