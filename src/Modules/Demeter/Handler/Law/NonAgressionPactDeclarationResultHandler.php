<?php

namespace App\Modules\Demeter\Handler\Law;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Manager\Law\LawManager;
use App\Modules\Demeter\Message\Law\NonAgressionPactDeclarationResultMessage;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Model\Law\Law;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NonAgressionPactDeclarationResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected CommercialRouteManager $commercialRouteManager,
		protected LawManager $lawManager,
	) {

	}

	public function __invoke(NonAgressionPactDeclarationResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getFactionId());
		$enemyColor = $this->colorManager->get($law->options['rColor']);

		$color->colorLink[$law->options['rColor']] = Color::NEUTRAL;
		$law->statement = Law::OBSOLETE;
		$this->commercialRouteManager->freezeRoute($color, $enemyColor);
		$this->entityManager->flush($color);
		$this->entityManager->flush($law);
	}
}
