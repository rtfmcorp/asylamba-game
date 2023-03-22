<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\SanctionResultMessage;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SanctionResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected LawManager $lawManager,
		protected PlayerManager $playerManager,
	) {

	}

	public function __invoke(SanctionResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getFactionId());
		$player = $this->playerManager->get($law->options['rPlayer']);

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
}
