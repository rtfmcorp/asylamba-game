<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\WarDeclarationResultMessage;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WarDeclarationResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected CommercialRouteManager $commercialRouteManager,
		protected LawManager $lawManager,
	) {

	}

	public function __invoke(WarDeclarationResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getFactionId());
		$enemyColor = $this->colorManager->get($law->options['rColor']);

		$color->colorLink[$law->options['rColor']] = Color::ENEMY;
		$enemyColor->colorLink[$color->id] = Color::ENEMY;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}
}