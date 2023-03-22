<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\BonusEndMessage;
use Asylamba\Modules\Demeter\Model\Law\Law;
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
