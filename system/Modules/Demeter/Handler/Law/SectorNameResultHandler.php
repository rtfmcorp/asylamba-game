<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\SectorNameResultMessage;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SectorNameResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected ColorManager $colorManager,
		protected EntityManager $entityManager,
		protected LawManager $lawManager,
		protected SectorManager $sectorManager,
	) {

	}

	public function __invoke(SectorNameResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getId());
		$sector = $this->sectorManager->get($law->options['rSector']);

		if ($sector->rColor == $color->id) {
			$sector->name = $law->options['name'];
		}
		$law->statement = Law::OBSOLETE;

		$this->entityManager->flush($sector);
		$this->entityManager->flush($law);
	}
}
