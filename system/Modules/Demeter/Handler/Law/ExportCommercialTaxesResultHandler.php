<?php

namespace Asylamba\Modules\Demeter\Handler\Law;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Manager\CommercialTaxManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Message\Law\ExportCommercialTaxesResultMessage;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ExportCommercialTaxesResultHandler implements MessageHandlerInterface
{
	public function __construct(
		protected EntityManager $entityManager,
		protected ColorManager $colorManager,
		protected CommercialTaxManager $commercialTaxManager,
		protected LawManager $lawManager,
	) {

	}

	public function __invoke(ExportCommercialTaxesResultMessage $message): void
	{
		$law = $this->lawManager->get($message->getLawId());
		$color = $this->colorManager->get($law->getFactionId());
		$relatedFaction = $this->colorManager->get($law->options['rColor']);
		$tax = $this->commercialTaxManager->getFactionsTax($color, $relatedFaction);

		if ($law->options['rColor'] == $color->id) {
			$tax->exportTax = $law->options['taxes'] / 2;
			$tax->importTax = $law->options['taxes'] / 2;
			$law->statement = Law::OBSOLETE;
		} else {
			$tax->exportTax = $law->options['taxes'];
			$law->statement = Law::OBSOLETE;
		}
		$this->entityManager->flush($law);
		$this->entityManager->flush($tax);
	}
}
