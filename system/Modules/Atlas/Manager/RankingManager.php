<?php

namespace Asylamba\Modules\Atlas\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Hermes\Manager\ConversationManager;
use Asylamba\Modules\Hermes\Manager\ConversationMessageManager;

use Asylamba\Modules\Atlas\Routine\PlayerRoutine;
use Asylamba\Modules\Atlas\Routine\FactionRoutine;

use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Zeus\Manager\PlayerManager;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Modules\Atlas\Model\FactionRanking;
use Asylamba\Modules\Atlas\Model\Ranking;
use Asylamba\Modules\Gaia\Model\Sector;

use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Classes\Library\Utils;

class RankingManager
{
	/** @var EntityManager **/
	protected $entityManager;
	/** @var PlayerRankingManager **/
	protected $playerRankingManager;
	/** @var FactionRankingManager **/
	protected $factionRankingManager;
	/** @var ColorManager **/
	protected $colorManager;
	/** @var ConversationManager **/
	protected $conversationManager;
	/** @var ConversationMessageManager **/
	protected $conversationMessageManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var OrbitalBaseHelper **/
	protected $orbitalBaseHelper;
	
	/**
	 * @param EntityManager $entityManager
	 * @param PlayerRankingManager $playerRankingManager
	 * @param FactionRankingManager $factionRankingManager
	 * @param ColorManager $colorManager
	 * @param ConversationManager $conversationManager
	 * @param ConversationMessageManager $conversationMessageManager
	 * @param PlayerManager $playerManager
	 * @param OrbitalBaseHelper $orbitalBaseHelper
	 */
	public function __construct(
		EntityManager $entityManager,
		PlayerRankingManager $playerRankingManager,
		FactionRankingManager $factionRankingManager,
		ColorManager $colorManager,
		ConversationManager $conversationManager,
		ConversationMessageManager $conversationMessageManager,
		PlayerManager $playerManager,
		OrbitalBaseHelper $orbitalBaseHelper
	)
	{
		$this->entityManager = $entityManager;
		$this->playerRankingManager = $playerRankingManager;
		$this->factionRankingManager = $factionRankingManager;
		$this->colorManager = $colorManager;
		$this->conversationManager = $conversationManager;
		$this->conversationMessageManager = $conversationMessageManager;
		$this->playerManager = $playerManager;
		$this->orbitalBaseHelper = $orbitalBaseHelper;
	}
	
	public function processPlayersRanking()
	{
		if ($this->entityManager->getRepository(Ranking::class)->hasBeenAlreadyProcessed(true, false) === true) {
			return;
		}
		$playerRoutine = new PlayerRoutine();
		
		$players = $this->playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);
		
		$playerRankingRepository = $this->entityManager->getRepository(PlayerRanking::class);
		
		$ranking = $this->createRanking(true, false);
		
		$playerRoutine->execute(
			$players,
			$playerRankingRepository->getPlayersResources(),
			$playerRankingRepository->getPlayersResourcesData(),
			$playerRankingRepository->getPlayersGeneralData(),
			$playerRankingRepository->getPlayersArmiesData(),
			$playerRankingRepository->getPlayersPlanetData(),
			$playerRankingRepository->getPlayersTradeRoutes(),
			$playerRankingRepository->getPlayersLinkedTradeRoutes(),
			$playerRankingRepository->getAttackersButcherRanking(),
			$playerRankingRepository->getDefendersButcherRanking(),
			$this->orbitalBaseHelper
		);
		
		$S_PRM1 = $this->playerRankingManager->getCurrentSession();
		$this->playerRankingManager->newSession();
		$this->playerRankingManager->loadLastContext();

		$playerRoutine->processResults($ranking, $players, $this->playerRankingManager, $playerRankingRepository);
		
		$this->playerRankingManager->changeSession($S_PRM1);
		
		$this->entityManager->flush();
	}
	
	public function processFactionsRanking()
	{
		if ($this->entityManager->getRepository(Ranking::class)->hasBeenAlreadyProcessed(false, true) === true) {
			return;
		}
		$factionRoutine = new FactionRoutine();
		
		$factions = $this->colorManager->getInGameFactions();
		$playerRankingRepository = $this->entityManager->getRepository(PlayerRanking::class);
		$factionRankingRepository = $this->entityManager->getRepository(FactionRanking::class);
		$sectors = $this->entityManager->getRepository(Sector::class)->getAll();
		
		$ranking = $this->createRanking(false, true);
		
		foreach ($factions as $faction) {
			$this->colorManager->updateInfos($faction);
			
			$routesIncome = $factionRankingRepository->getRoutesIncome($faction);
			$playerRankings = $playerRankingRepository->getFactionPlayerRankings($faction);
			
			$factionRoutine->execute($faction, $playerRankings, $routesIncome, $sectors);
		}
		
		$S_FRM1 = $this->factionRankingManager->getCurrentSession();
		$this->factionRankingManager->newSession();
		$this->factionRankingManager->loadLastContext();
		
		$winningFactionId = $factionRoutine->processResults($ranking, $factions, $this->factionRankingManager);

		$this->factionRankingManager->changeSession($S_FRM1);
		
		if ($winningFactionId !== null) {
			$this->processWinningFaction($winningFactionId);
		}
		$this->entityManager->flush();
	}
	
	protected function processWinningFaction($factionId)
	{
		$faction = $this->colorManager->get($factionId);
		$faction->isWinner = Color::WIN;

		# envoyer un message de Jean-Mi
		$winnerName = ColorResource::getInfo($faction->id, 'officialName');
		$content = 'Salut,<br /><br />La victoire vient d\'être remportée par : <br /><strong>' . $winnerName . '</strong><br />';
		$content .= 'Cette faction a atteint les ' . POINTS_TO_WIN . ' points, la partie est donc terminée.<br /><br />Bravo et un grand merci à tous les participants !';

		$S_CVM1 = $this->conversationManager->getCurrentSession();
		$this->conversationManager->newSession();
		$this->conversationManager->load(
			['cu.rPlayer' => ID_JEANMI]
		);

		if ($this->conversationManager->size() == 1) {
			$conv = $this->conversationManager->get();

			$conv->messages++;
			$conv->dLastMessage = Utils::now();

			# désarchiver tous les users
			$users = $conv->players;
			foreach ($users as $user) {
				$user->convStatement = ConversationUser::CS_DISPLAY;
			}

			# création du message
			$message = new ConversationMessage();

			$message->rConversation = $conv->id;
			$message->rPlayer = ID_JEANMI;
			$message->type = ConversationMessage::TY_STD;
			$message->content = $content;
			$message->dCreation = Utils::now();
			$message->dLastModification = NULL;

			$this->conversationMessageManager->add($message);
		} else {
			throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
		}
		$this->conversationManager->changeSession($S_CVM1);
	}
	
	protected function createRanking($isPlayer, $isFaction)
	{
		$ranking =
			(new Ranking())
			->setIsPlayer($isPlayer)
			->setIsFaction($isFaction)
			->setCreatedAt(Utils::now())
		;
		$this->entityManager->persist($ranking);
		$this->entityManager->flush($ranking);
		return $ranking;
	}
}