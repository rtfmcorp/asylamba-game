<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PutCommanderInSchool extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
		EntityManager $entityManager,
		int $id
	): Response {
		if (null === ($commander = $commanderManager->get($id)) || $commander->rPlayer !== $currentPlayer->getId()) {
			throw new BadRequestHttpException('Ce commandant n\'existe pas ou ne vous appartient pas');
		}
		$orbitalBase = $orbitalBaseManager->get($commander->rBase);

		if ($commander->statement == Commander::RESERVE) {
			$commanders = $commanderManager->getBaseCommanders($commander->rBase, [Commander::INSCHOOL]);

			if (count($commanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
				$commander->statement = Commander::INSCHOOL;
				$commander->uCommander = Utils::now();
			} else {
				throw new ConflictHttpException('Votre école est déjà pleine.');
			}
		} elseif ($commander->statement == Commander::INSCHOOL) {
			$commander->statement = Commander::RESERVE;
			$commander->uCommander = Utils::now();
		} else {
			throw new ConflictHttpException('Vous ne pouvez rien faire avec cet officier.');
		}
		$entityManager->flush();

		return $this->redirectToRoute('school');
	}
}
