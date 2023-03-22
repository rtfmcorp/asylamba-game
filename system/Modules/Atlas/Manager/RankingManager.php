<?php

namespace Asylamba\Modules\Atlas\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Model\Color;
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
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected ConversationManager $conversationManager,
		protected ConversationMessageManager $conversationMessageManager,
		protected string $pointsToWin,
		protected int $jeanMiId
	) {
	}
	
	public function processWinningFaction($factionId)
	{
		$faction = $this->colorManager->get($factionId);
		$faction->isWinner = Color::WIN;

		# envoyer un message de Jean-Mi
		$winnerName = ColorResource::getInfo($faction->id, 'officialName');
		$content = 'Salut,<br /><br />La victoire vient d\'être remportée par : <br /><strong>' . $winnerName . '</strong><br />';
		$content .= 'Cette faction a atteint les ' . $this->pointsToWin . ' points, la partie est donc terminée.<br /><br />Bravo et un grand merci à tous les participants !';

		$S_CVM1 = $this->conversationManager->getCurrentSession();
		$this->conversationManager->newSession();
		$this->conversationManager->load(
			['cu.rPlayer' => $this->jeanMiId]
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
			$message->rPlayer = $this->jeanMiId;
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
	
	public function createRanking(bool $isPlayer, bool $isFaction): Ranking
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
