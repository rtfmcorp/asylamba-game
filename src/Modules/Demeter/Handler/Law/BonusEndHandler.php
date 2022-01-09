<?php

namespace App\Modules\Demeter\Handler\Law;

use App\Classes\Entity\EntityManager;
use App\Modules\Demeter\Manager\Law\LawManager;
use App\Modules\Demeter\Message\Law\BonusEndMessage;
use App\Modules\Demeter\Model\Law\Law;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BonusEndHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected LawManager $lawManager,
	) {

	}

	public function __invoke(BonusEndMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$law->statement = Law::OBSOLETE;
		$this->entityManager->flush($law);
	}
}
