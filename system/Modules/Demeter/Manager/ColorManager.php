<?php

/**
 * Color Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 26.11.13
*/

namespace Asylamba\Modules\Demeter\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Worker\CTC;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Demeter\Manager\Election\VoteManager;
use Asylamba\Modules\Hermes\Manager\ConversationManager;
use Asylamba\Modules\Demeter\Manager\Election\CandidateManager;
use Asylamba\Modules\Demeter\Manager\Election\ElectionManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Hermes\Manager\ConversationMessageManager;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Modules\Athena\Manager\CommercialTaxManager;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Demeter\Model\Election\Election;
use Asylamba\Classes\Library\Parser;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

class ColorManager {
	/** @var EntityManager **/
	protected $entityManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var VoteManager **/
	protected $voteManager;
	/** @var ConversationManager **/
	protected $conversationManager;
	/** @var CandidateManager **/
	protected $candidateManager;
	/** @var ElectionManager **/
	protected $electionManager;
	/** @var LawManager **/
	protected $lawManager;
	/** @var NotificationManager **/
	protected $notificationManager;
	/** @var ConversationMessageManager **/
	protected $conversationMessageManager;
	/** @var CommercialTaxManager **/
	protected $commercialTaxManager;
	/** @var SectorManager **/
	protected $sectorManager;
	/** @var CommercialRouteManager **/
	protected $commercialRouteManager;
	/** @var Parser **/
	protected $parser;
	/** @var CTC **/
	protected $ctc;
	/** @var RealTimeActionScheduler **/
	protected $realtimeActionScheduler;
	
	/**
	 * @param EntityManager $entityManager
	 * @param PlayerManager $playerManager
	 * @param VoteManager $voteManager
	 * @param ConversationManager $conversationManager
	 * @param CandidateManager $candidateManager
	 * @param ElectionManager $electionManager
	 * @param LawManager $lawManager
	 * @param NotificationManager $notificationManager
	 * @param ConversationMessageManager $conversationMessageManager
	 * @param CommercialTaxManager $commercialTaxManager
	 * @param SectorManager $sectorManager
	 * @param CommercialRouteManager $commercialRouteManager
	 * @param Parser $parser
	 * @param CTC $ctc
	 * @param RealTimeActionScheduler $realtimeActionScheduler
	 */
	public function __construct(
		EntityManager $entityManager,
		PlayerManager $playerManager,
		VoteManager $voteManager,
		ConversationManager $conversationManager,
		CandidateManager $candidateManager,
		ElectionManager $electionManager,
		LawManager $lawManager,
		NotificationManager $notificationManager,
		ConversationMessageManager $conversationMessageManager,
		CommercialTaxManager $commercialTaxManager,
		SectorManager $sectorManager,
		CommercialRouteManager $commercialRouteManager,
		Parser $parser,
		CTC $ctc,
		RealTimeActionScheduler $realtimeActionScheduler
	) {
		$this->entityManager = $entityManager;
		$this->playerManager = $playerManager;
		$this->voteManager = $voteManager;
		$this->conversationManager = $conversationManager;
		$this->candidateManager = $candidateManager;
		$this->electionManager = $electionManager;
		$this->lawManager = $lawManager;
		$this->notificationManager = $notificationManager;
		$this->conversationMessageManager = $conversationMessageManager;
		$this->commercialTaxManager = $commercialTaxManager;
		$this->sectorManager = $sectorManager;
		$this->commercialRouteManager = $commercialRouteManager;
		$this->parser = $parser;
		$this->ctc = $ctc;
		$this->realtimeActionScheduler = $realtimeActionScheduler;
	}
	
	/**
	 * @param int $id
	 * @return Color
	 */
	public function get($id)
	{
		$faction = $this->entityManager->getRepository(Color::class)->get($id);
		$this->uMethod($faction);
		return $faction;
	}
	
	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->entityManager->getRepository(Color::class)->getAll();
	}
	
	/**
	 * @return array
	 */
	public function getInGameFactions()
	{
		return $this->entityManager->getRepository(Color::class)->getInGameFactions();
	}
	
	/**
	 * @return array
	 */
	public function getOpenFactions()
	{
		return $this->entityManager->getRepository(Color::class)->getOpenFactions();
	}
	
	/**
	 * @return array
	 */
	public function getAllByActivePlayersNumber()
	{
		return $this->entityManager->getRepository(Color::class)->getAllByActivePlayersNumber();
	}

	/**
	 * @param Color $faction
	 * @return int
	 */
	public function add(Color $faction) {
		$this->entityManager->persist($faction);
		$this->entityManager->flush($faction);

		return $faction->id;
	}
	
	/**
	 * @param Color $color
	 * @return string
	 */
	public function getParsedDescription(Color $color) {
		$this->parser->parseBigTag = TRUE;
		return $this->parser->parse($color->description);
	}
	
	public function scheduleSenateUpdate()
	{
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::ROYALISTIC], [Color::MANDATE]
		);
		foreach ($factions as $faction) {
			$date = new \DateTime($faction->dLastElection);
			$date->modify('+' . $faction->mandateDuration . ' second');
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'updateSenate', $faction, $date->format('Y-m-d H:i:s'));
		}
	}
	
	public function scheduleCampaigns()
	{
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::DEMOCRATIC, Color::THEOCRATIC],
			[Color::MANDATE]
		);
		foreach ($factions as $faction) {
			$date = new \DateTime($faction->dLastElection);
			$date->modify('+' . $faction->mandateDuration . ' second');
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'uCampaign', $faction, $date->format('Y-m-d H:i:s'));
		}
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::ROYALISTIC], [Color::ELECTION]
		);
		foreach ($factions as $faction) {
			$datetime = new \DateTime($faction->dLastElection);
			$datetime->modify('+' . Color::PUTSCHTIME . ' second');
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'ballot', $faction, $datetime->format('Y-m-d H:i:s'));
		}
	}
	
	public function scheduleElections()
	{
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::DEMOCRATIC], [Color::CAMPAIGN]
		);
		foreach ($factions as $faction) {
			$election = $this->electionManager->getFactionLastElection($faction->id);
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'uElection', $faction, $election->dElection);
		}
	}
	
	public function scheduleBallot()
	{
		$factions = array_merge(
			$this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
				[Color::DEMOCRATIC], [Color::ELECTION]
			),
			$this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
				[Color::THEOCRATIC], [Color::CAMPAIGN, Color::ELECTION]
			)
		);
		foreach ($factions as $faction) {
			$datetime = new \DateTime($faction->dLastElection);
			$datetime->modify('+' . $faction->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME . ' second');

			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'ballot', $faction, $datetime->format('Y-m-d H:i:s'));
		}
		$this->entityManager->flush(Color::class);
	}

	// FONCTIONS STATICS
	public function updateInfos(Color $faction) {
		$faction->players = $this
			->playerManager
			->countByFactionAndStatements($faction->id, [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY])
		;
		$faction->activePlayers = $this
			->playerManager
			->countByFactionAndStatements($faction->id, [Player::ACTIVE])
		;
		$this->entityManager->flush($faction);
	}
	
	public function sendSenateNotif(Color $color, $fromChief = FALSE) {
		$parliamentMembers = $this->playerManager->getParliamentMembers($color->id);
		
		foreach ($parliamentMembers as $parliamentMember) {
			$notif = new Notification();
			$notif->setRPlayer($parliamentMember->id);
			if ($fromChief == FALSE) {
				$notif->setTitle('Loi proposée');
				$notif->addBeg()
					->addTxt('Votre gouvernement a proposé un projet de loi, en tant que membre du sénat, il est de votre devoir de voter pour l\'acceptation ou non de ladite loi.')
					->addSep()
					->addLnk('faction/view-senate', 'voir les lois en cours de vote')
					->addEnd();
			} else {
				$notif->setTitle('Loi appliquée');
				$notif->addBeg()
					->addTxt('Votre ' . ColorResource::getInfo($color->id, 'status')[5] . ' a appliqué une loi.')
					->addSep()
					->addLnk('faction/view-senate', 'voir les lois appliquées')
					->addEnd();
			}
			$this->notificationManager->add($notif);
		}
	}
	
	/**
	 * @param int $factionId
	 */
	public function updateSenate($factionId)
	{
		$faction = $this->get($factionId);
		$this->updateStatus($faction, $this->playerManager->getFactionPlayersByRanking($factionId));
		
		if ($faction->regime === Color::ROYALISTIC && $faction->electionStatement === Color::MANDATE) {
			$date = date('Y-m-d H:i:s', time() + $faction->mandateDuration);
			$faction->dLastElection = $date;
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'updateSenate', $faction, $date);
			$this->entityManager->flush($faction);
		}
	}

	/**
	 * @param Color $color
	 * @param array $factionPlayers
	 */
	public function updateStatus(Color $color, $factionPlayers) {
		$limit = round($color->players / 4);
		// If there is less than 40 players in a faction, the limit is up to 10 senators
		if ($limit < 10) { $limit = 10; }
		// If there is more than 120 players in a faction, the limit is up to 40 senators
		if ($limit > 40) { $limit = 40; }

		foreach ($factionPlayers as $key => $factionPlayer) {
			if ($factionPlayer->status < Player::TREASURER) {
				if ($key < $limit) {
					if ($factionPlayer->status != Player::PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer($factionPlayer->id);
						$notif->setTitle('Vous êtes sénateur');
						$notif->dSending = Utils::now();
						$notif->addBeg()
							->addTxt('Vos actions vous ont fait gagner assez de prestige pour faire partie du sénat.');
						$this->notificationManager->add($notif);
					}
					$factionPlayer->status = Player::PARLIAMENT;
				} else {
					if ($factionPlayer->status == Player::PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer($factionPlayer->id);
						$notif->setTitle('Vous n\'êtes plus sénateur');
						$notif->dSending = Utils::now();
						$notif->addBeg()
							->addTxt('Vous n\'avez plus assez de prestige pour rester dans le sénat.');
						$this->notificationManager->add($notif);
					}
					$factionPlayer->status = Player::STANDARD;
				}
			}
		}
		$this->entityManager->flush(Player::class);
	}

	public function ballot($factionId) {
		$faction = $this->get($factionId);
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

				$this->uMandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
			} else {
				$this->uMandate($faction, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
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
					$this->uMandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
				} else {
					$looser = $this->playerManager->get(key($ballot));
					$this->uMandate($faction, 0, $looser, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
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

				$this->uMandate($faction, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate);
			} else {
				$this->uMandate($faction, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate);
			}
		}
	}

	/**
	 * @param int $factionId
	 */
	public function uCampaign($factionId) {
		$faction = $this->get($factionId);
		$factionPlayers = $this->playerManager->getFactionPlayersByRanking($faction->getId());
		$this->updateStatus($faction, $factionPlayers);
		
		$date = new \DateTime($faction->dLastElection);
		$date->modify('+' . $faction->mandateDuration + Color::CAMPAIGNTIME . ' second');
		
		$election = new Election();
		$election->rColor = $faction->id;
		$election->dElection = $date->format('Y-m-d H:i:s');

		$this->electionManager->add($election);
		$faction->electionStatement = Color::CAMPAIGN;
		if ($faction->regime === Color::DEMOCRATIC) {
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'uElection', $faction, $election->dElection);
		} elseif ($faction->regime === Color::THEOCRATIC) {
			$this->realtimeActionScheduler->schedule('demeter.color_manager', 'ballot', $faction, $election->dElection);
		}
		$this->entityManager->flush($election);
		$this->entityManager->flush($faction);
	}

	/**
	 * @param int $factionId
	 */
	public function uElection($factionId) {
		$faction = $this->get($factionId);
		$faction->electionStatement = Color::ELECTION;
		$election = $this->electionManager->getFactionLastElection($factionId);
		
		$date = new \DateTime($election->dElection);
		$date->modify('+' . Color::ELECTIONTIME . ' second');
		
		$this->realtimeActionScheduler->schedule('demeter.color_manager', 'ballot', $faction, $date->format('Y-m-d H:i:s'));
		
		$this->entityManager->flush($faction);
	}

	public function uMandate(Color $color, $governmentMembers, $newChief, $idOldChief, $hadVoted, $conv, $convPlayerID, $candidate) {
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
				$this->realtimeActionScheduler->schedule('demeter.color_manager', 'uCampaign', $color, $date->format('Y-m-d H:i:s'));
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
				$this->realtimeActionScheduler->schedule('demeter.color_manager', 'updateSenate', $color, date('Y-m-d H:i:s', (time() + $color->mandateDuration)));
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
                    $this->realtimeActionScheduler->schedule('demeter.color_manager', 'uCampaign', $color, $date->format('Y-m-d H:i:s'));
				
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

	public function uVoteLaw(Color $color, $law, $ballot) {
		if ($ballot) {
			//accepter la loi
			$law->statement = Law::EFFECTIVE;
			//envoyer un message
		} else {
			//refuser la loi
			$law->statement = Law::REFUSED;
			if (LawResources::getInfo($law->type, 'bonusLaw')) {
				$color->credits += (LawResources::getInfo($law->type, 'price') * Utils::interval($law->dEndVotation, $law->dEnd) * ($color->activePlayers + 1) * 90) / 100;
			} else {
				$color->credits += (LawResources::getInfo($law->type, 'price') * 90) / 100;
			}
			//envoyer un message
		}
		$this->entityManager->flush($law);
		$this->entityManager->flush($color);
	}

	public function uFinishBonusLaw($law) {
		$law->statement = Law::OBSOLETE;
		$this->entityManager->flush($law);
	}

	public function uFinishSectorTaxes(Color $color, $law, $sector) {
		if ($sector->rColor == $color->id) {
			$sector->tax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
		$this->entityManager->flush($law);
		$this->entityManager->flush($sector);
	}

	public function uFinishSectorName(Color $color, $law, $sector) {
		if ($sector->rColor == $color->id) {
			$sector->name = $law->options['name'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
		$this->entityManager->flush($sector);
		$this->entityManager->flush($law);
	}
	
	public function uFinishExportComercialTaxes(Color $color, $law, $tax) {
		if ($law->options['rColor'] == $color->id) {
			$tax->exportTax = $law->options['taxes'] / 2;
			$tax->importTax = $law->options['taxes'] / 2;
			$law->statement = Law::OBSOLETE;
		} else {
			$tax->exportTax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		}
		$this->entityManager->flush($law);
		//$this->entityManager->flush($tax);
	}

	public function uFinishImportComercialTaxes(Color $color, $law, $tax) {
		if ($law->options['rColor'] == $color->id) {
			$tax->exportTax = $law->options['taxes'] / 2;
			$tax->importTax = $law->options['taxes'] / 2;
			$law->statement = Law::OBSOLETE;
		} else {
			$tax->importTax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		}
		$this->entityManager->flush($law);
		//$this->entityManager->flush($tax);
	}

	public function uFinishNeutral(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::NEUTRAL;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}

	public function uFinishPeace(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::PEACE;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}

	public function uFinishAlly(Color $color, $law,$enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::ALLY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}

	public function uFinishEnemy(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::ENEMY;
		$enemyColor->colorLink[$color->id] = Color::ENEMY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}

	public function uFinishPunition(Color $color, $law, $player) {
		$toPay = $law->options['credits'];
		if ($player->credit < $law->options['credits']) {
			$toPay = $player->credit;
		}
		$this->playerManager->decreaseCredit($player, $toPay);
		$color->credits += $toPay;
		$law->statement = Law::OBSOLETE;
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}


	public function uMethod(Color $color) {
		// 604800s = 7j
		$token_ctc = $this->ctc->createContext('Color');

		$laws = $this->lawManager->getByFactionAndStatements($color->id, [Law::VOTATION, Law::EFFECTIVE]);

		foreach ($laws as $law) {
			if ($law->statement == Law::VOTATION && $law->dEndVotation < Utils::now()) {
				$this->ctc->add($law->dEndVotation, $this, 'uVoteLaw', $color, array($color, $law, $this->lawManager->ballot($law)));
			} elseif ($law->statement == Law::EFFECTIVE && $law->dEnd < Utils::now()) {
				if (LawResources::getInfo($law->type, 'bonusLaw')) {
					#lois à bonus
					$this->ctc->add($law->dEnd, $this, 'uFinishBonusLaw', $color, array($law));
				} else {
					#loi à upgrade
					switch ($law->type) {
						case Law::SECTORTAX:
							$this->ctc->add($law->dEnd, $this, 'uFinishSectorTaxes', $color, array($color, $law, $this->sectorManager->get($law->options['rSector'])));
							break;
						case Law::SECTORNAME:
							$this->ctc->add($law->dEnd, $this, 'uFinishSectorName', $color, array($color, $law, $this->sectorManager->get($law->options['rSector'])));
							break;
						case Law::COMTAXEXPORT:
							$_CTM = $this->commercialTaxManager->getCurrentsession();
							$this->commercialTaxManager->newSession();
							$this->commercialTaxManager->load(array('faction' => $color->id, 'relatedFaction' => $law->options['rColor']));
							$this->ctc->add($law->dEnd, $this, 'uFinishExportComercialTaxes', $color, array($color, $law, $this->commercialTaxManager->get()));
							$this->commercialTaxManager->changeSession($_CTM);
							break;
						case Law::COMTAXIMPORT:
							$_CTM = $this->commercialTaxManager->getCurrentsession();
							$this->commercialTaxManager->newSession();
							$this->commercialTaxManager->load(array('faction' => $color->id, 'relatedFaction' => $law->options['rColor']));
							$this->ctc->add($law->dEnd, $this, 'uFinishImportComercialTaxes', $color, array($color, $law, $this->commercialTaxManager->get()));
							$this->commercialTaxManager->changeSession($_CTM);
							break;
						case Law::PEACEPACT:
							$this->ctc->add($law->dEnd, $this, 'uFinishPeace', $color, array($color, $law, $this->get($law->options['rColor'])));
							break;
						case Law::WARDECLARATION:
							$this->ctc->add($law->dEnd, $this, 'uFinishEnemy', $color, array($color, $law, $this->get($law->options['rColor'])));
							break;
						case Law::TOTALALLIANCE:
							$this->ctc->add($law->dEnd, $this, 'uFinishAlly', $color, array($color, $law,$this->get($law->options['rColor'])));
							break;
						case Law::NEUTRALPACT:
							$this->ctc->add($law->dEnd, $this, 'uFinishNeutral', $color, array($color, $law, $this->get($law->options['rColor'])));
							break;
						case Law::PUNITION:
							$this->ctc->add($law->dEnd, $this, 'uFinishPunition', $color, array($color, $law, $this->playerManager->get($law->options['rPlayer'])));
							break;
					}
				}
			}
		}
		$this->ctc->applyContext($token_ctc);
	}
}
