<?php

namespace App\Modules\Athena\Infrastructure\ArgumentResolver;

use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CurrentBaseValueResolver implements ArgumentValueResolverInterface
{
	public function __construct(protected OrbitalBaseManager $orbitalBaseManager)
	{

	}

	public function supports(Request $request, ArgumentMetadata $argument): bool
	{
		if (OrbitalBase::class !== $argument->getType()) {
			return false;
		}
		if (null === $request->getSession()->get('playerParams')?->get('base')) {
			return false;
		}
		return true;
	}

	public function resolve(Request $request, ArgumentMetadata $argument): iterable
	{
		yield $this->orbitalBaseManager->get($request->getSession()->get('playerParams')->get('base'));
	}
}
