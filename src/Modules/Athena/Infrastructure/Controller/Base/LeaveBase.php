<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Gaia\Event\PlaceOwnerChangeEvent;
use App\Modules\Gaia\Manager\PlaceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LeaveBase extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBase $currentBase,
		CommanderManager $commanderManager,
		OrbitalBaseManager $orbitalBaseManager,
		OrbitalBaseHelper $orbitalBaseHelper,
		PlaceManager $placeManager,
		EntityManager $entityManager,
		EventDispatcherInterface $eventDispatcher,
	): Response {
		$session = $request->getSession();

		if (\count($session->get('playerBase')->get('ob')->size()) === 1) {
			throw new ConflictHttpException('vous ne pouvez pas abandonner votre unique planète');
		}
		$baseCommanders = $commanderManager->getBaseCommanders($currentBase->getId());

		$isAFleetMoving = \array_reduce($baseCommanders, fn (bool $carry, Commander $commander) => $carry || $commander->isMoving(), false);
		if ($isAFleetMoving) {
			throw new ConflictHttpException('toute les flottes de cette base doivent être immobiles');
		}

		if (Utils::interval(Utils::now(), $currentBase->dCreation, 'h') < OrbitalBase::COOL_DOWN) {
			throw new ConflictHttpException('Vous ne pouvez pas abandonner de base dans les ' . OrbitalBase::COOL_DOWN . ' premières relèves.');
		}

		# delete buildings in queue
		foreach ($currentBase->buildingQueues as $buildingQueue) {
			$entityManager->remove($buildingQueue);
		}

		# change base type if it is a capital
		if ($currentBase->typeOfBase == OrbitalBase::TYP_CAPITAL) {
			$newType = (rand(0,1) === 0) ? OrbitalBase::TYP_COMMERCIAL : OrbitalBase::TYP_MILITARY;
			# delete extra buildings
			for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
				$maxLevel = $orbitalBaseHelper->getBuildingInfo($i, 'maxLevel', $newType);
				if ($currentBase->getBuildingLevel($i) > $maxLevel) {
					$currentBase->setBuildingLevel($i, $maxLevel);
				}
			}
			# change base type
			$currentBase->typeOfBase = $newType;
		}
		$place = $placeManager->get($currentBase->getId());
		$gaiaId = $this->getParameter('gaia_id');

		$orbitalBaseManager->changeOwnerById($currentBase->getId(), $currentBase, $gaiaId, $baseCommanders);
		$place->rPlayer = $gaiaId;
		$entityManager->flush();
		$eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place), PlaceOwnerChangeEvent::NAME);

		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			if ($session->get('playerBase')->get('ob')->get($i)->get('id') === $currentBase->getId()) {
				$session->get('playerBase')->get('ob')->remove($i);
			}
		}
		$this->addFlash('success', 'Base abandonnée');

		return $this->redirectToRoute('switchbase', [
			'baseId' => $session->get('playerBase')->get('ob')->get(0)->get('id')
		]);
	}
}
