<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\AllianceDeclarationResultMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AllianceDeclarationResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected CommercialRouteManager $commercialRouteManager,
		protected LawManager $lawManager,
	) {

	}

	public function __invoke(AllianceDeclarationResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getFactionId());
		$enemyColor = $this->colorManager->get($law->options['rColor']);

		$color->colorLink[$law->options['rColor']] = Color::ALLY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}
}
