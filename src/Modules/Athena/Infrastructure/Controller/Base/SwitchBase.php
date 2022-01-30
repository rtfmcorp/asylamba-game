<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base;

use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SwitchBase extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBaseManager $orbitalBaseManager,
		int $baseId,
		string $page
	): Response {
		if (null === ($base = $orbitalBaseManager->get($baseId))) {
			throw new NotFoundHttpException('Base not found');
		}

		if ($base->rPlayer !== $currentPlayer->getId()) {
			throw new AccessDeniedHttpException('This base does not belong to you');
		}

		$session = $request->getSession();

		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			$b = $session->get('playerBase')->get('ob')->get($i);
			if ($b->get('id') === $base->getId()) {
				$session->get('playerParams')->add('base', $base->getId());
				break;
			}
		}

		return $this->redirectToRoute(match ($page) {
			'generator' => 'generator',
			'refinery' => 'refinery',
			'dock1' => 'dock1',
			'dock2' => 'dock2',
			'technosphere' => 'technosphere',
			'commercialroute' => 'trade_market',
			'sell' => 'trade_market',
			'school' => 'school',
			'spatioport' => 'spatioport',
			default => 'base_overview',
		}, $request->query->all());
	}
}
