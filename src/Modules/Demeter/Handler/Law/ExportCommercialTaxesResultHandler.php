<?php

namespace App\Modules\Demeter\Handler\Law;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Manager\CommercialTaxManager;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Manager\Law\LawManager;
use App\Modules\Demeter\Message\Law\ExportCommercialTaxesResultMessage;
use App\Modules\Demeter\Model\Law\Law;
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
