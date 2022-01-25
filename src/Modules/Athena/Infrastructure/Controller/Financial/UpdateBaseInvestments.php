<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateBaseInvestments extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBaseManager $orbitalBaseManager,
		EntityManager $entityManager,
		int $baseId,
		string $category,
	): Response	{
		$credit = $request->request->get('credit');

		if (null === ($base = $orbitalBaseManager->get($baseId))) {
			throw new NotFoundHttpException('Base inexistante');
		}

		if ($base->rPlayer !== $currentPlayer->getId()) {
			throw new AccessDeniedHttpException('Cette base ne vous appartient pas');
		}

		match ($category) {
			'school' => $this->updateSchoolInvestment($base, $credit),
			'antispy' => $this->updateAntiSpyInvestment($base, $credit),
			default => throw new BadRequestHttpException('Invalid category'),
		};

		$entityManager->flush($base);

		return $this->redirectToRoute('financial_investments');
	}

	protected function updateSchoolInvestment(OrbitalBase $base, int $credit): void
	{
		if (50000 < $credit) {
			throw new BadRequestHttpException('La limite maximale d\'investissement dans l\'école de commandement est de 50\'000 crédits.');
		}
		$base->setISchool($credit);
		$this->addFlash('success', 'L\'investissement dans l\'école de commandement de votre base ' . $base->getName() . ' a été modifié');

	}

	protected function updateAntiSpyInvestment(OrbitalBase $base, int $credit): void
	{
		if (100000 < $credit) {
			throw new BadRequestHttpException('La limite maximale d\'investissement dans l\'anti-espionnage est de 100\'000 crédits.');
		}
		$base->setIAntiSpy($credit);
		$this->addFlash('success', 'L\'investissement dans l\'anti-espionnage sur votre base ' . $base->getName() . ' a été modifié');
	}
}
