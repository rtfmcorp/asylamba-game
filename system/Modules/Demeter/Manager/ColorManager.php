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
use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
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

class ColorManager extends Manager {
	/** @var string **/
	protected $managerType ='_Color';
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
	
	/**
	 * @param Database $database
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
	 */
	public function __construct(
		Database $database,
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
		CTC $ctc
	) {
		parent::__construct($database);
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
	}
	
	/**
	 * @param Color $color
	 * @return string
	 */
	public function getParsedDescription(Color $color) {
		$this->parser->parseBigTag = TRUE;
		return $this->parser->parse($color->description);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);
		
		$qr = $this->database->prepare('SELECT c.*
			FROM color AS c
			' . $formatWhere .'
			' . $formatOrder .'
			' . $formatLimit
		);

		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $p) {
					$valuesArray[] = $p;
				}
			} else {
				$valuesArray[] = $v;
			}
		}

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$awColor = $qr->fetchAll();

		$qr->closeCursor();

		$qr = $this->database->prepare('SELECT c.*
			FROM colorLink AS c ORDER BY rColorLinked'
		);

		$qr->execute();

		$awColorLink = $qr->fetchAll();
		$qr->closeCursor();

		$colorsArray = array();

		for ($i = 0; $i < count($awColor); $i++) {
			$color = new Color();
			$color->id = $awColor[$i]['id'];
			$color->alive = $awColor[$i]['alive'];
			$color->isWinner = $awColor[$i]['isWinner'];
			$color->credits = $awColor[$i]['credits'];
			$color->players = $awColor[$i]['players'];
			$color->activePlayers = $awColor[$i]['activePlayers'];
			$color->rankingPoints = $awColor[$i]['rankingPoints'];
			$color->points = $awColor[$i]['points'];
			$color->sectors = $awColor[$i]['sectors'];
			$color->electionStatement = $awColor[$i]['electionStatement'];
			$color->isClosed = $awColor[$i]['isClosed'];
			$color->description = $awColor[$i]['description'];
			$color->dClaimVictory = $awColor[$i]['dClaimVictory'];
			$color->dLastElection = $awColor[$i]['dLastElection'];
			$color->isInGame = $awColor[$i]['isInGame'];
			$color->colorLink[0] = Color::NEUTRAL;

			$color->officialName = ColorResource::getInfo($color->id, 'officialName');
			$color->popularName = ColorResource::getInfo($color->id, 'popularName');
			$color->government = ColorResource::getInfo($color->id, 'government');
			$color->demonym = ColorResource::getInfo($color->id, 'demonym');
			$color->factionPoint = ColorResource::getInfo($color->id, 'factionPoint');
			$color->status = ColorResource::getInfo($color->id, 'status');
			$color->regime = ColorResource::getInfo($color->id, 'regime');
			$color->devise = ColorResource::getInfo($color->id, 'devise');
			$color->desc1 = ColorResource::getInfo($color->id, 'desc1');
			$color->desc2 = ColorResource::getInfo($color->id, 'desc2');
			$color->desc3 = ColorResource::getInfo($color->id, 'desc3');
			$color->desc4 = ColorResource::getInfo($color->id, 'desc4');
			$color->bonus = ColorResource::getInfo($color->id, 'bonus');
			$color->mandateDuration = ColorResource::getInfo($color->id, 'mandateDuration');
			$color->senateDesc = ColorResource::getInfo($color->id, 'senateDesc');
			$color->campaignDesc = ColorResource::getInfo($color->id, 'campaignDesc');
	
			$color->bonusText = [];
			foreach (ColorResource::getInfo($color->id, 'bonus') AS $k) {
				$color->bonusText[] = ColorResource::getBonus($k);
			}

			if ($color->id != 0) {
				foreach ($awColorLink AS $colorLink) {
					if ($colorLink['rColor'] == $color->id) {
						$color->colorLink[$colorLink['rColorLinked']] = $colorLink['statement'];
					}
				}
			} else {
				$color->colorLink = array(Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL);
			}

			$this->_Add($color);
			if ($this->currentSession->getUMode()) {
				$this->uMethod($color);
			}
		}
	}

	public function save() {
		$colors = $this->_Save();

		foreach ($colors AS $color) {
			$qr = $this->database->prepare('UPDATE color
				SET
					alive = ?,
					isWinner = ?,
					credits = ?,
					players = ?,	
					activePlayers = ?,
					rankingPoints = ?,
					points = ?,
					sectors = ?,
					electionStatement = ?,
					isClosed = ?,
					isInGame = ?,
					description = ?,
					dClaimVictory = ?,
					dLastElection = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$color->alive,
					$color->isWinner,
					$color->credits,
					$color->players,
					$color->activePlayers,
					$color->rankingPoints,
					$color->points,
					$color->sectors,
					$color->electionStatement,
					$color->isClosed,
					$color->isInGame,
					$color->description,
					$color->dClaimVictory,
					$color->dLastElection,
					$color->id
				));

			$qr2 = $this->database->prepare('UPDATE colorLink SET
					statement = ? WHERE rColor = ? AND rColorLinked = ?
				');

			foreach ($color->colorLink as $key => $value) {
				$qr2->execute(array($value, $color->id, $key));
			}
		}
	}

	public function add($newColor) {
		$qr = $this->database->prepare('INSERT INTO color
		SET
			id = ?,
			alive = ?,
			isWinner = ?,
			credits = ?,
			players = ?,		
			activePlayers = ?,
			rankingPoints = ?,
			points = ?,
			sectors = ?,
			electionStatement = ?,
			isClosed = ?,
			isInGame = ?,
			description = ?,
			dClaimVictory = ?,
			dLastElection = ?');
		$aw = $qr->execute(array(
				$color->id,
				$color->alive,
				$color->isWinner,
				$color->credits,
				$color->players,
				$color->activePlayers,
				$color->rankingPoints,
				$color->points,
				$color->sectors,
				$color->electionStatement,
				$color->isClosed,
				$color->isInGame,
				$color->description,
				$color->dClaimVictory,
				$color->dLastElection
			));

		$newColor->id = $this->database->lastInsertId();

		$this->_Add($newColor);

		return $newColor->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM color WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	// FONCTIONS STATICS
	public function updateInfos($id) {
		$this->updatePlayers($id);
		$this->updateActivePlayers($id);
	}

	public function updatePlayers($id) {
		$_CLM1 = $this->getCurrentSession();
		$this->newSession();
		$this->load(array('id' => $id));

		$this->getById($id)->players = $this->playerManager->countByFactionAndStatements($id, [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);	

		$this->changeSession($_CLM1);
	}

	public function updateActivePlayers($id) {
		$_CLM1 = $this->getCurrentSession();
		$this->newSession();
		$this->load(array('id' => $id));

		$this->getById($id)->activePlayers = $this->playerManager->countByFactionAndStatements($id, [Player::ACTIVE]);

		$this->changeSession($_CLM1);
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

	public function updateStatus(Color $color, $factionPlayers) {
		$limit = round($color->players / 4);
		if ($limit < 10) { $limit = 10; }
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

	private function ballot(Color $color, $date, $election) {
		$chiefId = (($leader = $this->playerManager->getFactionLeader($color->id)) !== null) ? $leader->getId() : false;

		$_VOM = $this->voteManager->getCurrentSession();
		$this->voteManager->newSession();
		$this->voteManager->load(array('rElection' => $election->id));
		
		$ballot = [];
		$listCandidate = [];

		for ($i = 0; $i < $this->voteManager->size(); $i++) {
			if (array_key_exists($this->voteManager->get($i)->rCandidate, $ballot)) {
				$ballot[$this->voteManager->get($i)->rCandidate]++;
			} else {
				$ballot[$this->voteManager->get($i)->rCandidate] = 1;
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

		$convPlayerID = $this->playerManager->getFactionAccount($color->id)->id;

		$S_CVM = $this->conversationManager->getCurrentSession();
		$this->conversationManager->newSession();
		$this->conversationManager->load(
			['cu.rPlayer' => $convPlayerID]
		);
		$conv = $this->conversationManager->get();
		
		$this->conversationManager->changeSession($S_CVM);

		if ($color->regime == Color::DEMOCRATIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);
				
				$governmentMembers = $this->playerManager->getGovernmentMembers($color->getId());
				$newChief = $this->playerManager->get(key($ballot));

				$this->ctc->add($date, $this, 'uMandate', $color, array($color, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
			} else {
				$this->ctc->add($date, $this, 'uMandate', $color, array($color, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
			}
			$this->voteManager->changeSession($_VOM);

		} elseif ($color->regime == Color::ROYALISTIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				if (key($ballot) == $chiefId) {
					next($ballot);
				}

				if (((current($ballot) / ($color->activePlayers + 1)) * 100) >= Color::PUTSCHPERCENTAGE) {
				
					$governmentMembers = $this->playerManager->getGovernmentMembers($color->getId());
					$newChief = $this->playerManager->get(key($ballot));

					$this->ctc->add($date, $this, 'uMandate', $color, array($color, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
				} else {
					$looser = $this->playerManager->get(key($ballot));
					
					$this->ctc->add($date, $this, 'uMandate', $color, array($color, 0, $looser, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
					
				}
			}
			$this->voteManager->changeSession($_VOM);
		} else {
			if (($leader = $this->playerManager->getFactionLeader($color->id)) !== null) {
				$this->candidateManager->newSession();
				$this->candidateManager->load(array('rPlayer' => $leader->id, 'rElection' => $election->id));
				if ($this->candidateManager->size() > 0) {
					if (rand(0, 1) == 0) {
						$ballot = array();
					}
				}
				$this->candidateManager->changeSession($_CAM1);
			}
			if (count($ballot) > 0) {
				reset($ballot);
				$aleaNbr = rand(0, count($ballot) - 1);
				
				for ($i = 0; $i < $aleaNbr; $i++) {
					next($ballot);
				}
				
				$governmentMembers = $this->playerManager->getGovernmentMembers($color->getId());
				$newChief = $this->playerManager->get(key($ballot));

				$this->ctc->add($date, $this, 'uMandate', $color, array($color, $governmentMembers, $newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
			} else {
				$this->ctc->add($date, $this, 'uMandate', $color, array($color, 0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
			}
		}
	}

	/**
	 * @param Color $color
	 * @param array $factionPlayers
	 */
	public function uCampaign(Color $color, $factionPlayers) {
		if ($color->regime == Color::DEMOCRATIC || $color->regime == Color::THEOCRATIC) {
			$this->updateStatus($color, $factionPlayers);
			$S_ELM = $this->electionManager->getCurrentsession();
			$this->electionManager->newSession();
			$election = new Election();
			$election->rColor = $color->id;
			// @WARNING : DEFAULT VALUE
			$election->dElection = new \DateTime();
                            
			/*$date = new DateTime($this->dLastElection);
			$date->modify('+' . $this->mandateDuration + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
			$election->dElection = $date->format('Y-m-d H:i:s');*/

			$this->electionManager->add($election);
			$this->electionManager->changeSession($S_ELM);
			$this->electionStatement = Color::CAMPAIGN;
		} else {
			$this->updateStatus($color, $factionPlayers);

			/*$date = new DateTime($this->dLastElection);
			$date->modify('+' . $this->mandateDuration . ' second');
			$date = $date->format('Y-m-d H:i:s');
			$this->dLastElection = $date;*/
		}
	}

	public function uElection() {
		$this->electionStatement = Color::ELECTION;
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

			$color->electionStatement = Color::MANDATE;

			$statusArray = $color->status;
			
			if ($color->regime == Color::DEMOCRATIC) {
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($this->playerManager->get()->id);
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
			} elseif ($color->regime == Color::ROYALISTIC) {
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($this->playerManager->get()->id);
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
						->addLnk('embassy/player-' . $this->playerManager->get()->id, $this->playerManager->get()->name)
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
				$message->content .= current($candidate)['name'] . ' a reçu le soutien de ' . Format::number((current($candidate)['vote'] / $color->activePlayers) * 100) . '% de la population.' . '<br />';
				$this->conversationMessageManager->add($message);

			} else {
				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($this->playerManager->get()->id);
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

/*			$date = new DateTime($this->dLastElection);
			$date->modify('+' . $this->mandateDuration + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
			$date = $date->format('Y-m-d H:i:s');
			$this->dLastElection = $date;*/

			if ($color->regime == Color::ROYALISTIC) {
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
			} elseif ($color->regime == Color::THEOCRATIC) {
				if ($idOldChief) {
					$notif = new Notification();
					$notif->dSending = Utils::now();
					$notif->setRPlayer($idOldChief);
					$notif->setTitle('Vous avez été nommé Guide');
					$notif->addBeg()
						->addTxt(' Les Oracles ont parlé, vous êtes toujours désigné par la Grande Lumière pour guider Cardan vers la Gloire.');
					$this->notificationManager->add($notif);
				}
			}
		}
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
				$color->credits += (LawResources::getInfo($law->type, 'price') * Utils::interval($law->dEndVotation, $law->dEnd) * $color->activePlayers * 90) / 100;
			} else {
				$color->credits += (LawResources::getInfo($law->type, 'price') * 90) / 100;
			}
			//envoyer un message
		}
	}

	public function uFinishBonusLaw($law, $sector) {
		$law->statement = Law::OBSOLETE;
	}

	public function uFinishSectorTaxes(Color $color, $law, $sector) {
		if ($sector->rColor == $color->id) {
			$sector->tax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
	}

	public function uFinishSectorName(Color $color, $law, $sector) {
		if ($sector->rColor == $color->id) {
			$sector->name = $law->options['name'];
			$law->statement = Law::OBSOLETE;
		} else {
			$law->statement = Law::OBSOLETE;
		}
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
	}

	public function uFinishNeutral(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::NEUTRAL;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
	}

	public function uFinishPeace(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::PEACE;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
	}

	public function uFinishAlly(Color $color, $law,$enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::ALLY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
	}

	public function uFinishEnemy(Color $color, $law, $enemyColor) {
		$color->colorLink[$law->options['rColor']] = Color::ENEMY;
		$enemyColor->colorLink[$color->id] = Color::ENEMY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
	}

	public function uFinishPunition(Color $color, $law, $player) {
		$toPay = $law->options['credits'];
		if ($player->credit < $law->options['credits']) {
			$toPay = $player->credit;
		}
		$this->playerManager->decreaseCredit($player, $toPay);
		$color->credits += $toPay;
		$law->statement = Law::OBSOLETE;
	}


	public function uMethod(Color $color) {
		// 604800s = 7j
		$token_ctc = $this->ctc->createContext('Color');

		if ($color->regime == Color::DEMOCRATIC) {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$factionPlayers = $this->playerManager->getFactionPlayersByRanking($color->id);

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $factionPlayers));
				}
			} elseif ($color->electionStatement == Color::CAMPAIGN) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration + Color::CAMPAIGNTIME) {
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$this->ctc->add($date, $this, 'uElection', $color, array());
				}
			} else {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME) {
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$_ELM = $this->electionManager->getCurrentSession();
					$this->electionManager->newSession();
					$this->electionManager->load(array('rColor' => $color->id), array('id', 'DESC'), array('0', '1'));
					$color->dLastElection = $date;
					$this->ballot($color, $date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);
				}
			}
		} elseif ($color->regime == Color::ROYALISTIC) {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$factionPlayers = $this->playerManager->getFactionPlayersByRanking($color->getId());

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $factionPlayers));
				}
			} elseif ($color->electionStatement == Color::ELECTION) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > Color::PUTSCHTIME) {
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$_ELM = $this->electionManager->getCurrentSession();
					$this->electionManager->newSession();
					$this->electionManager->load(array('rColor' => $color->id), array('id', 'DESC'), array('0', '1'));
					$this->dLastElection = $date;
					$this->ballot($color, $date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);

					$factionPlayers = $this->playerManager->getFactionPlayersByRanking($color->getId());

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $factionPlayers));
				}
			}
		} else {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$factionPlayers = $this->playerManager->getFactionPlayersByRanking($color->getId());

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');
					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $factionPlayers));
				}
			} else {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration + Color::CAMPAIGNTIME) {
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$_ELM = $this->electionManager->getCurrentSession();
					$this->electionManager->newSession();
					$this->electionManager->load(array('rColor' => $color->id), array('id', 'DESC'), array('0', '1'));
					$color->dLastElection = $date;
					$this->ballot($color, $date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);
				}
			}
		}

		$_LAM = $this->lawManager->getCurrentSession();
		$this->lawManager->newSession();
		$this->lawManager->load(array('rColor' => $color->id, 'statement' => array(Law::VOTATION, Law::EFFECTIVE)));

		for ($i = 0; $i < $this->lawManager->size(); $i++) {
			$law = $this->lawManager->get($i);
			if ($law->statement == Law::VOTATION && $law->dEndVotation < Utils::now()) {
				$this->ctc->add($law->dEndVotation, $this, 'uVoteLaw', $color, array($color, $law, $this->lawManager->ballot($law)));
			} elseif ($law->statement == Law::EFFECTIVE && $law->dEnd < Utils::now()) {
				if (LawResources::getInfo($law->type, 'bonusLaw')) {
					#lois à bonus
					$this->ctc->add($law->dEnd, $this, 'uFinishBonusLaw', $color, array($law, $this->sectorManager->get()));
				} else {
					#loi à upgrade
					switch ($law->type) {
						case Law::SECTORTAX:
							$_SEM = $this->sectorManager->getCurrentsession();
							$this->sectorManager->newSession();
							$this->sectorManager->load(array('id' => $law->options['rSector']));
							$this->ctc->add($law->dEnd, $this, 'uFinishSectorTaxes', $color, array($color, $law, $this->sectorManager->getById($law->options['rSector'])));
							$this->sectorManager->changeSession($_SEM);
							break;
						case Law::SECTORNAME:
							$_SEM = $this->sectorManager->getCurrentsession();
							$this->sectorManager->newSession();
							$this->sectorManager->load(array('id' => $law->options['rSector']));
							$this->ctc->add($law->dEnd, $this, 'uFinishSectorName', $color, array($color, $law, $this->sectorManager->getById($law->options['rSector'])));
							$this->sectorManager->changeSession($_SEM);
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
							$S_CLM321 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $law->options['rColor']]);
							$this->ctc->add($law->dEnd, $this, 'uFinishPeace', $color, array($color, $law, $this->get()));
							$this->changeSession($S_CLM321);
							break;
						case Law::WARDECLARATION:
							$S_CLM322 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $law->options['rColor']]);
							$this->ctc->add($law->dEnd, $this, 'uFinishEnemy', $color, array($color, $law, $this->get()));
							$this->changeSession($S_CLM322);
							break;
						case Law::TOTALALLIANCE:
							$S_CLM323 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $law->options['rColor']]);
							$this->ctc->add($law->dEnd, $this, 'uFinishAlly', $color, array($color, $law,$this->get()));
							$this->changeSession($S_CLM323);
							break;
						case Law::NEUTRALPACT:
							$S_CLM324 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $law->options['rColor']]);
							$this->ctc->add($law->dEnd, $this, 'uFinishNeutral', $color, array($color, $law, $this->get()));
							$this->changeSession($S_CLM324);
							break;
						case Law::PUNITION:
							$this->ctc->add($law->dEnd, $this, 'uFinishPunition', $color, array($color, $law, $this->playerManager->get($law->options['rPlayer'])));
							break;
					}
				}
			}
		}
		$this->lawManager->changeSession($_LAM);
		$this->ctc->applyContext($token_ctc);
	}
}
