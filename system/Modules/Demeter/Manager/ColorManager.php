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
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Message\BallotMessage;
use Asylamba\Modules\Demeter\Message\CampaignMessage;
use Asylamba\Modules\Demeter\Message\ElectionMessage;
use Asylamba\Modules\Demeter\Message\Law\AllianceDeclarationResultMessage;
use Asylamba\Modules\Demeter\Message\Law\BonusEndMessage;
use Asylamba\Modules\Demeter\Message\Law\ExportCommercialTaxesResultMessage;
use Asylamba\Modules\Demeter\Message\Law\ImportCommercialTaxesResultMessage;
use Asylamba\Modules\Demeter\Message\Law\NonAgressionPactDeclarationResultMessage;
use Asylamba\Modules\Demeter\Message\Law\PeaceDeclarationResultMessage;
use Asylamba\Modules\Demeter\Message\Law\SanctionResultMessage;
use Asylamba\Modules\Demeter\Message\Law\SectorNameResultMessage;
use Asylamba\Modules\Demeter\Message\Law\SectorTaxesResultMessage;
use Asylamba\Modules\Demeter\Message\Law\VoteMessage;
use Asylamba\Modules\Demeter\Message\Law\WarDeclarationResultMessage;
use Asylamba\Modules\Demeter\Message\SenateUpdateMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Demeter\Manager\Election\ElectionManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Library\Parser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ColorManager
{
	protected PlayerManager $playerManager;

	public function __construct(
		protected EntityManager $entityManager,
		protected ElectionManager $electionManager,
		protected LawManager $lawManager,
		protected NotificationManager $notificationManager,
		protected Parser $parser,
		protected MessageBusInterface $messageBus,
	) {
	}

	#[Required]
	public function setPlayerManager(PlayerManager $playerManager): void
	{
		$this->playerManager = $playerManager;
	}
	
	/**
	 * @param int $id
	 * @return Color
	 */
	public function get($id)
	{
		$faction = $this->entityManager->getRepository(Color::class)->get($id);
		// @TODO avoid duplicate messages
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

			$this->messageBus->dispatch(
				new SenateUpdateMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
			);
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

			$this->messageBus->dispatch(
				new CampaignMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($date->format('Y-m-d H:i:s'))],
			);
		}
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::ROYALISTIC], [Color::ELECTION]
		);
		foreach ($factions as $faction) {
			$datetime = new \DateTime($faction->dLastElection);
			$datetime->modify('+' . Color::PUTSCHTIME . ' second');

			$this->messageBus->dispatch(
				new BallotMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($datetime->format('Y-m-d H:i:s'))],
			);
		}
	}
	
	public function scheduleElections(): void
	{
		$factions = $this->entityManager->getRepository(Color::class)->getByRegimeAndElectionStatement(
			[Color::DEMOCRATIC], [Color::CAMPAIGN]
		);
		foreach ($factions as $faction) {
			$election = $this->electionManager->getFactionLastElection($faction->id);
			$this->messageBus->dispatch(
				new ElectionMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($election->dElection)],
			);
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

			$this->messageBus->dispatch(
				new BallotMessage($faction->getId()),
				[DateTimeConverter::to_delay_stamp($datetime->format('Y-m-d H:i:s'))]
			);
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

	public function uMethod(Color $color): void
	{
		$laws = $this->lawManager->getByFactionAndStatements($color->id, [Law::VOTATION, Law::EFFECTIVE]);

		foreach ($laws as $law) {
			if ($law->statement == Law::VOTATION && $law->dEndVotation < Utils::now()) {
				$this->messageBus->dispatch(
					new VoteMessage($law->getId()),
					[DateTimeConverter::to_delay_stamp($law->dEndVotation)],
				);
			} elseif ($law->statement == Law::EFFECTIVE && $law->dEnd < Utils::now()) {
				$messageClass = match (LawResources::getInfo($law->type, 'bonusLaw')) {
					true => BonusEndMessage::class,
					false => match($law->type) {
						Law::SECTORTAX => SectorTaxesResultMessage::class,
						Law::SECTORNAME => SectorNameResultMessage::class,
						Law::COMTAXEXPORT => ExportCommercialTaxesResultMessage::class,
						Law::COMTAXIMPORT => ImportCommercialTaxesResultMessage::class,
						Law::PEACEPACT => PeaceDeclarationResultMessage::class,
						Law::WARDECLARATION => WarDeclarationResultMessage::class,
						Law::TOTALALLIANCE => AllianceDeclarationResultMessage::class,
						Law::NEUTRALPACT => NonAgressionPactDeclarationResultMessage::class,
						Law::PUNITION => SanctionResultMessage::class,
					}
				};
				$this->messageBus->dispatch(
					new $messageClass($law->getId()),
					[DateTimeConverter::to_delay_stamp($law->dEnd)],
				);
			}
		}
	}
}
