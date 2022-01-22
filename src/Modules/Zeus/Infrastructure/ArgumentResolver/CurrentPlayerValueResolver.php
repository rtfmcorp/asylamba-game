<?php

namespace App\Modules\Zeus\Infrastructure\ArgumentResolver;

use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CurrentPlayerValueResolver implements ArgumentValueResolverInterface
{
	public function __construct(protected PlayerManager $playerManager)
	{

	}

	public function supports(Request $request, ArgumentMetadata $argument): bool
	{
		if (Player::class !== $argument->getType()) {
			return false;
		}
		if (null === $request->getSession()->get('playerId')) {
			return false;
		}
		return true;
	}

	public function resolve(Request $request, ArgumentMetadata $argument): iterable
	{
		yield $this->playerManager->get($request->getSession()->get('playerId'));
	}
}
