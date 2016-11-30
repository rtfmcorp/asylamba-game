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

class ColorManager extends Manager {
	/** @var string **/
	protected $managerType ='_Color';
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
	/** @var CTC **/
	protected $ctc;
	
	/**
	 * @param Database $database
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
	 * @param CTC $ctc
	 */
	public function __construct(
		Database $database,
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
		CTC $ctc
	) {
		parent::__construct($database);
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
		$this->ctc = $ctc;
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
	public static function updateInfos($id) {
		self::updatePlayers($id);
		self::updateActivePlayers($id);
	}

	public static function updatePlayers($id) {
		$_CLM1 = $this->getCurrentSession();
		$this->newSession();
		$this->load(array('id' => $id));

		$_PAM = $this->playerManager->getCurrentSession();
		$this->playerManager->newSession(FALSE);
		$this->playerManager->load(array('statement' => array(Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY), 'rColor' => $id));

		$this->getById($id)->players = $this->playerManager->size();	

		$this->playerManager->changeSession($_PAM);
		$this->changeSession($_CLM1);
	}

	public static function updateActivePlayers($id) {
		$_CLM1 = $this->getCurrentSession();
		$this->newSession();
		$this->load(array('id' => $id));

		$_PAM = $this->playerManager->getCurrentSession();
		$this->playerManager->newSession(FALSE);
		$this->playerManager->load(array('statement' => Player::ACTIVE, 'rColor' => $id));
		
		$this->getById($id)->activePlayers = $this->playerManager->size();

		$this->playerManager->changeSession($_PAM);
		$this->changeSession($_CLM1);
	}
	
	public function sendSenateNotif(Color $color, $fromChief = FALSE) {
		$_PAM121 = $this->playerManager->getCurrentsession();
		$this->playerManager->newSession();
		$this->playerManager->load(['rColor' => $color->id, 'status' => Player::PARLIAMENT]);

		for ($i = 0; $i < $this->playerManager->size(); $i++) {
			$notif = new Notification();
			$notif->setRPlayer($this->playerManager->get($i)->id);
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
					->addTxt('Votre ' . ColorResource::getInfo($this->id, 'status')[5] . ' a appliqué une loi.')
					->addSep()
					->addLnk('faction/view-senate', 'voir les lois appliquées')
					->addEnd();
			}
			$this->notificationManager->add($notif);
		}
		$this->playerManager->changeSession($_PAM121);
	}

	public function updateStatus(Color $color, $token_pam) {
		$limit = round($color->players / 4);
		if ($limit < 10) { $limit = 10; }
		if ($limit > 40) { $limit = 40; }

		$_PAM1 = $this->playerManager->getCurrentSession();
		$this->playerManager->changeSession($token_pam);

		for ($i = 0; $i < $this->playerManager->size(); $i++) {
			if ($this->playerManager->get($i)->status < Player::TREASURER) {
				if ($i < $limit) {
					if ($this->playerManager->get($i)->status != Player::PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer($this->playerManager->get($i)->id);
						$notif->setTitle('Vous êtes sénateur');
						$notif->dSending = Utils::now();
						$notif->addBeg()
							->addTxt('Vos actions vous ont fait gagner assez de prestige pour faire partie du sénat.');
						$this->notificationManager->add($notif);
					}
					$this->playerManager->get($i)->status = Player::PARLIAMENT;
				} else {
					if ($this->playerManager->get($i)->status == Player::PARLIAMENT) {
						$notif = new Notification();
						$notif->setRPlayer($this->playerManager->get($i)->id);
						$notif->setTitle('Vous n\'êtes plus sénateur');
						$notif->dSending = Utils::now();
						$notif->addBeg()
							->addTxt('Vous n\'avez plus assez de prestige pour rester dans le sénat.');
						$this->notificationManager->add($notif);
					}
					$this->playerManager->get($i)->status = Player::STANDARD;
				}
			}
		}
		$this->playerManager->changeSession($_PAM1);
	}

	private function ballot(Color $color, $date, $election) {
		$_PAM_1 = $this->playerManager->getCurrentsession();
		$this->playerManager->newSession(FALSE);
		$this->playerManager->load(array('rColor' => $color->id, 'status' => Player::CHIEF));
		$chiefId = ($this->playerManager->size() == 0) ? FALSE : $this->playerManager->get()->id;
		$this->playerManager->changeSession($_PAM_1);

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
			$_PAM_2 = $this->playerManager->getCurrentsession();
			$this->playerManager->newSession(FALSE);
			$this->playerManager->load(array('id' => array_keys($ballot)));

			foreach ($ballot as $player => $vote) {
				$listCandidate[] = [
					'id' => $player,
					'name' => $this->playerManager->getById($player)->name,
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

			$this->playerManager->changeSession($_PAM_2);		
		}

		reset($listCandidate);

		$S_PAM1 = $this->playerManager->getCurrentSession();
		$this->playerManager->newSession(FALSE);
		$this->playerManager->load(
			['statement' => Player::DEAD, 'rColor' => $this->id],
			['id', 'ASC'],
			[0, 1]
		);
		$convPlayerID = $this->playerManager->get()->id;

		$S_CVM = $this->conversationManager->getCurrentSession();
		$this->conversationManager->newSession();
		$this->conversationManager->load(
			['cu.rPlayer' => $this->playerManager->get()->id]
		);
		$conv = $this->conversationManager->get();
		
		$this->conversationManager->changeSession($S_CVM);
		$this->playerManager->changeSession($S_PAM1);

		if ($this->regime == self::DEMOCRATIC) {
			if (count($ballot) > 0) {
				$_PAM1 = $this->playerManager->getCurrentsession();
				$this->playerManager->newSession(FALSE);
				$token_playersGovernement = $this->playerManager->getCurrentsession();
				$this->playerManager->load(array('status' => array(Player::TREASURER, Player::WARLORD, Player::MINISTER, Player::CHIEF), 'rColor' => $this->id));
				$this->playerManager->changeSession($_PAM1);

				arsort($ballot);
				reset($ballot);

				$_PAM2 = $this->playerManager->getCurrentsession();
				$this->playerManager->newSession(FALSE);
				$token_newChief = $this->playerManager->getCurrentSession();
				$this->playerManager->load(array('id' => key($ballot)));
				$this->playerManager->changeSession($_PAM2);

				$this->ctc->add($date, $this, 'uMandate', array($token_playersGovernement, $token_newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
			} else {
				$this->ctc->add($date, $this, 'uMandate', array(0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
			}
			$this->voteManager->changeSession($_VOM);

		} elseif ($this->regime == self::ROYALISTIC) {
			if (count($ballot) > 0) {
				arsort($ballot);
				reset($ballot);

				if (key($ballot) == $chiefId) {
					next($ballot);
				}

				if (((current($ballot) / ($this->activePlayers + 1)) * 100) >= self::PUTSCHPERCENTAGE) {
					$_PAM1 = $this->playerManager->getCurrentsession();
					$this->playerManager->newSession(FALSE);
					$token_playersGovernement = $this->playerManager->getCurrentsession();
					$this->playerManager->load(array('status' => array(Player::TREASURER, Player::WARLORD, Player::MINISTER, Player::CHIEF), 'rColor' => $this->id));
					$this->playerManager->changeSession($_PAM1);
				
					$_PAM2 = $this->playerManager->getCurrentsession();
					$this->playerManager->newSession(FALSE);
					$token_newChief = $this->playerManager->getCurrentSession();
					$this->playerManager->load(array('id' => key($ballot)));
					$this->playerManager->changeSession($_PAM2);

					$statusArray = $this->status;

					$this->ctc->add($date, $this, 'uMandate', array($token_playersGovernement, $token_newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
				} else {
					$_PAM2 = $this->playerManager->getCurrentsession();
					$this->playerManager->newSession(FALSE);
					$token_looser = $this->playerManager->getCurrentsession();
					$this->playerManager->load(array('id' => key($ballot)));
					$this->playerManager->changeSession($_PAM2);
					
					$this->ctc->add($date, $this, 'uMandate', array(0, $token_looser, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
					
				}
			}
			$this->voteManager->changeSession($_VOM);
		} else {
			$_PAM4 = $this->playerManager->getCurrentsession();
			$this->playerManager->newSession(FALSE);
			$this->playerManager->load(array('rColor' => $this->id, 'status' => Player::CHIEF));
			if ($this->playerManager->size() > 0) {
				$_CAM1 = $this->playerManager->getCurrentsession();
				$this->candidateManager->newSession();
				$this->candidateManager->load(array('rPlayer' => $this->playerManager->get()->id, 'rElection' => $election->id));
				if ($this->candidateManager->size() > 0) {
					if (rand(0, 1) == 0) {
						$ballot = array();
					}
				}
				$this->candidateManager->changeSession($_CAM1);
			}
			$this->playerManager->changeSession($_PAM4);
			if (count($ballot) > 0) {
				reset($ballot);
				$aleaNbr = rand(0, count($ballot) - 1);
				
				$_PAM1 = $this->playerManager->getCurrentsession();
				$this->playerManager->newSession(FALSE);
				$token_playersGovernement = $this->playerManager->getCurrentsession();
				$this->playerManager->load(array('status' => array(Player::TREASURER, Player::WARLORD, Player::MINISTER, Player::CHIEF), 'rColor' => $this->id));
				$this->playerManager->changeSession($_PAM1);

				for ($i = 0; $i < $aleaNbr; $i++) {
					next($ballot);
				}
				
				$_PAM2 = $this->playerManager->getCurrentsession();
				$this->playerManager->newSession(FALSE);
				$token_newChief = $this->playerManager->getCurrentSession();
				$this->playerManager->load(array('id' => key($ballot)));
				$this->playerManager->changeSession($_PAM2);

				$this->ctc->add($date, $this, 'uMandate', array($token_playersGovernement, $token_newChief, $chiefId, TRUE, $conv, $convPlayerID, $listCandidate));
			} else {
				$this->ctc->add($date, $this, 'uMandate', array(0, 0, $chiefId, FALSE, $conv, $convPlayerID, $listCandidate));
			}
		}
	}

	public function uCampaign(Color $color, $token_pam) {
		if ($color->regime == Color::DEMOCRATIC || $color->regime == Color::THEOCRATIC) {
			$this->updateStatus($color, $token_pam);
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
			$this->updateStatus($color, $token_pam);

			/*$date = new DateTime($this->dLastElection);
			$date->modify('+' . $this->mandateDuration . ' second');
			$date = $date->format('Y-m-d H:i:s');
			$this->dLastElection = $date;*/
		}
	}

	public function uElection() {
		$this->electionStatement = Color::ELECTION;
	}

	public function uMandate(Color $color, $token_playersGovernement, $token_newChief, $idOldChief, $hadVoted, $conv, $convPlayerID, $candidate) {
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

			$_PAM = $this->playerManager->getCurrentSession();
			$this->playerManager->changeSession($token_playersGovernement);
			for ($i = 0; $i < $this->playerManager->size(); $i++) { 
				$this->playerManager->get($i)->status = Player::PARLIAMENT;
			}

			$this->playerManager->changeSession($token_newChief);
			$this->playerManager->get()->status = Player::CHIEF;

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
			
			$this->playerManager->changeSession(Color::MANDATE);

/*			$date = new DateTime($this->dLastElection);
			$date->modify('+' . $this->mandateDuration + self::ELECTIONTIME + self::CAMPAIGNTIME . ' second');
			$date = $date->format('Y-m-d H:i:s');
			$this->dLastElection = $date;*/

			if ($color->regime == Color::ROYALISTIC) {
				$_PAM2 = $this->playerManager->getCurrentSession();
				$this->playerManager->changeSession($token_newChief);

				$notif = new Notification();
				$notif->dSending = Utils::now();
				$notif->setRPlayer($this->playerManager->get()->id);
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
						->addLnk('embassy/player-' . $this->playerManager->get()->id, $this->playerManager->get()->name)
						->addTxt(' a tenté un coup d\'état, celui-ci a échoué.');
					$this->notificationManager->add($notif);
				}
				
				$this->playerManager->newSession($_PAM2);
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
		$player->decreaseCredit($toPay);
		$color->credits += $toPay;
		$law->statement = Law::OBSOLETE;
	}


	public function uMethod(Color $color) {
		// 604800s = 7j
		$token_ctc = $this->ctc->createContext('Color');

		if ($color->regime == Color::DEMOCRATIC) {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$_PAM = $this->playerManager->getCurrentSession();
					$this->playerManager->newSession(FALSE);
					$this->playerManager->load(['rColor' => $color->id], ['factionPoint', 'DESC']);
					$token_pam = $this->playerManager->getCurrentSession();
					$this->playerManager->changeSession($_PAM);

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $token_pam));
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
					$this->ballot($date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);
				}
			}
		} elseif ($color->regime == Color::ROYALISTIC) {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$_PAM = $this->playerManager->getCurrentSession();
					$this->playerManager->newSession(FALSE);
					$this->playerManager->load(['rColor' => $color->id], ['factionPoint', 'DESC']);
					$token_pam = $this->playerManager->getCurrentSession();
					$this->playerManager->changeSession($_PAM);

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $token_pam));
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
					$this->ballot($date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);

					$_PAM = $this->playerManager->getCurrentSession();
					$this->playerManager->newSession(FALSE);
					$this->playerManager->load(['rColor' => $color->id], ['factionPoint', 'DESC']);
					$token_pam = $this->playerManager->getCurrentSession();
					$this->playerManager->changeSession($_PAM);

					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $token_pam));
				}
			}
		} else {
			if ($color->electionStatement == Color::MANDATE) {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration) {
					$_PAM = $this->playerManager->getCurrentSession();
					$this->playerManager->newSession(FALSE);
					$this->playerManager->load(['rColor' => $color->id], ['factionPoint', 'DESC']);
					$token_pam = $this->playerManager->getCurrentSession();
					$this->playerManager->changeSession($_PAM);

					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration . ' second');
					$date = $date->format('Y-m-d H:i:s');
					$this->ctc->add($date, $this, 'uCampaign', $color, array($color, $token_pam));
				}
			} else {
				if (Utils::interval($color->dLastElection, Utils::now(), 's') > $color->mandateDuration + self::CAMPAIGNTIME) {
					$date = new \DateTime($color->dLastElection);
					$date->modify('+' . $color->mandateDuration + Color::ELECTIONTIME + Color::CAMPAIGNTIME . ' second');
					$date = $date->format('Y-m-d H:i:s');

					$_ELM = $this->electionManager->getCurrentSession();
					$this->electionManager->newSession();
					$this->electionManager->load(array('rColor' => $color->id), array('id', 'DESC'), array('0', '1'));
					$color->dLastElection = $date;
					$this->ballot($date, $this->electionManager->get());

					$this->electionManager->changeSession($_ELM);
				}
			}
		}

		$_LAM = $this->lawManager->getCurrentSession();
		$this->lawManager->newSession();
		$this->lawManager->load(array('rColor' => $color->id, 'statement' => array(Law::VOTATION, Law::EFFECTIVE)));

		for ($i = 0; $i < $this->lawManager->size(); $i++) {
			if ($this->lawManager->get($i)->statement == Law::VOTATION && $this->lawManager->get($i)->dEndVotation < Utils::now()) {
				$this->ctc->add($this->lawManager->get($i)->dEndVotation, $this, 'uVoteLaw', $color, array($this->lawManager->get($i), $this->lawManager->get($i)->ballot()));
			} elseif ($this->lawManager->get($i)->statement == Law::EFFECTIVE && $this->lawManager->get($i)->dEnd < Utils::now()) {
				if (LawResources::getInfo($this->lawManager->get($i)->type, 'bonusLaw')) {
					#lois à bonus
					$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishBonusLaw', $color, array($this->lawManager->get($i), $this->sectorManager->get()));
				} else {
					#loi à upgrade
					switch ($this->lawManager->get($i)->type) {
						case Law::SECTORTAX:
							$_SEM = $this->sectorManager->getCurrentsession();
							$this->sectorManager->newSession();
							$this->sectorManager->load(array('id' => $this->lawManager->get($i)->options['rSector']));
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishSectorTaxes', $color, array($this->lawManager->get($i), $this->sectorManager->getById($this->lawManager->get($i)->options['rSector'])));
							$this->sectorManager->changeSession($_SEM);
							break;
						case Law::SECTORNAME:
							$_SEM = $this->sectorManager->getCurrentsession();
							$this->sectorManager->newSession();
							$this->sectorManager->load(array('id' => $this->lawManager->get($i)->options['rSector']));
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishSectorName', $color, array($this->lawManager->get($i), $this->sectorManager->getById($this->lawManager->get($i)->options['rSector'])));
							$this->sectorManager->changeSession($_SEM);
							break;
						case Law::COMTAXEXPORT:
							$_CTM = $this->commercialTaxManager->getCurrentsession();
							$this->commercialTaxManager->newSession();
							$this->commercialTaxManager->load(array('faction' => $color->id, 'relatedFaction' => $this->lawManager->get($i)->options['rColor']));
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishExportComercialTaxes', $color, array($this->lawManager->get($i), $this->commercialTaxManager->get()));
							$this->commercialTaxManager->changeSession($_CTM);
							break;
						case Law::COMTAXIMPORT:
							$_CTM = $this->commercialTaxManager->getCurrentsession();
							$this->commercialTaxManager->newSession();
							$this->commercialTaxManager->load(array('faction' => $color->id, 'relatedFaction' => $this->lawManager->get($i)->options['rColor']));
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishImportComercialTaxes', $color, array($this->lawManager->get($i), $this->commercialTaxManager->get()));
							$this->commercialTaxManager->changeSession($_CTM);
							break;
						case Law::PEACEPACT:
							$S_CLM321 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $this->lawManager->get($i)->options['rColor']]);
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishPeace', $color, array($this->lawManager->get($i), $this->get()));
							$this->changeSession($S_CLM321);
							break;
						case Law::WARDECLARATION:
							$S_CLM322 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $this->lawManager->get($i)->options['rColor']]);
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'u$colorFinishEnemy', $color, array($this->lawManager->get($i), $this->get()));
							$this->changeSession($S_CLM322);
							break;
						case Law::TOTALALLIANCE:
							$S_CLM323 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $this->lawManager->get($i)->options['rColor']]);
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishAlly', $color, array($this->lawManager->get($i),$this->get()));
							$this->changeSession($S_CLM323);
							break;
						case Law::NEUTRALPACT:
							$S_CLM324 = $this->getCurrentsession();
							$this->newSession();
							$this->load(['id' => $this->lawManager->get($i)->options['rColor']]);
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishNeutral', $color, array($this->lawManager->get($i), $this->get()));
							$this->changeSession($S_CLM324);
							break;
						case Law::PUNITION:
							$S_PAM = $this->playerManager->getCurrentsession();
							$this->playerManager->newSession();
							$this->playerManager->load(array('id' => $this->lawManager->get($i)->options['rPlayer']));
							$this->ctc->add($this->lawManager->get($i)->dEnd, $this, 'uFinishPunition', $color, array($this->lawManager->get($i), $this->playerManager->get()));
							$this->playerManager->changeSession($S_PAM);
							break;
						
						default:
							break;
					}
				}
			}
		}
		$this->lawManager->changeSession($_LAM);
		$this->ctc->applyContext($token_ctc);
	}
}
