<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\VoteMessage;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VoteHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected LawManager $lawManager,
		protected EntityManager $entityManager,
	) {

	}

	public function __invoke(VoteMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$faction = $this->colorManager->get($law->getFactionId());
		$ballot = $this->lawManager->ballot($law);
		if ($ballot) {
			//accepter la loi
			$law->statement = Law::EFFECTIVE;
			//envoyer un message
		} else {
			//refuser la loi
			$law->statement = Law::REFUSED;
			if (LawResources::getInfo($law->type, 'bonusLaw')) {
				$faction->credits += (LawResources::getInfo($law->type, 'price') * Utils::interval($law->dEndVotation, $law->dEnd) * ($faction->activePlayers + 1) * 90) / 100;
			} else {
				$faction->credits += (LawResources::getInfo($law->type, 'price') * 90) / 100;
			}
			//envoyer un message
		}
		$this->entityManager->flush($law);
		$this->entityManager->flush($faction);
	}
}
